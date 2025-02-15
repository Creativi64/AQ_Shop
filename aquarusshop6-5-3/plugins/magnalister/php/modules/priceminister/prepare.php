<?php
/**
 * 888888ba                 dP  .88888.                    dP
 * 88    `8b                88 d8'   `88                   88
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b.
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P'
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class PriceministerPrepare extends MagnaCompatibleBase
{

    protected $prepareSettings = array();

    public function __construct(&$params)
    {
        if (!empty($_POST['FullSerializedForm'])) {
                $newPost = array();
                parse_str_unlimited($_POST['FullSerializedForm'], $newPost);
                $_POST = array_merge($_POST, $newPost);
        }

        parent::__construct($params);

        $this->prepareSettings['selectionName'] = isset($_GET['view']) ? $_GET['view'] : 'apply';
        $this->resources['url']['mode'] = 'prepare';
        $this->resources['url']['view'] = $this->prepareSettings['selectionName'];
    }

    protected function saveMatching()
    {
        if (!array_key_exists('saveMatching', $_POST)){
            if (!isset($_POST['Action']) || $_POST['Action'] !== 'SaveMatching' || $_GET['where'] === 'varmatchView'){
                return;
            }
        }

        require_once(DIR_MAGNALISTER_MODULES . 'priceminister/classes/PriceministerProductSaver.php');

        $oProductSaver = new PriceministerProductSaver($this->resources['session']);
        $aProductIDs = MagnaDB::gi()->fetchArray("
			SELECT pID
			  FROM " . TABLE_MAGNA_SELECTION . "
			 WHERE     mpID = '" . $this->mpID . "'
				   AND selectionname = '" . $this->prepareSettings['selectionName'] . "'
				   AND session_id = '" . session_id() . "'
		", true);

        $shopVariations = $this->saveMatchingAttributes($oProductSaver);
        $itemDetails = $_POST;

        $itemDetails['CategoryAttributes'] = $shopVariations;

        if (1 == count($aProductIDs)){
            $oProductSaver->saveSingleProductProperties($aProductIDs[0], $itemDetails, $this->prepareSettings['selectionName'], $this->isAjax);
        } else if (!empty($aProductIDs)){
            $oProductSaver->saveMultipleProductProperties($aProductIDs, $itemDetails, $this->prepareSettings['selectionName']);
        }

        $saveMatching = array_key_exists('saveMatching', $_POST);

        if (count($oProductSaver->aErrors) === 0 || !$saveMatching || count($aProductIDs) != 1 || isset($_POST['matching_nextpage'])) {
            $isAjax = false;
            if (!$saveMatching){
                # stay on prepare product form
                $_POST['prepare'] = 'prepare';
                $isAjax = true;
            }

            $matchingNotFinished = isset($_POST['matching_nextpage']) && ctype_digit($_POST['matching_nextpage']) || $isAjax;
            if ($matchingNotFinished === false){
                MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
                    'mpID' => $this->mpID,
                    'selectionname' => $this->prepareSettings['selectionName'],
                    'session_id' => session_id()
                ));
                
                if (!empty($oProductSaver->aErrors)) {
                    foreach ($oProductSaver->aErrors as $sError) {
                        echo '<div class="errorBox">' . $sError . '</div>';
                    }
                }
            } else if ($saveMatching && $matchingNotFinished && !empty($oProductSaver->aErrors)) {
                foreach ($oProductSaver->aErrors as $sError) {
                    echo '<div class="errorBox">' . $sError . '</div>';
                }
            }
        } else {
            # stay on prepare product form
            $_POST['prepare'] = 'prepare';

            if ($saveMatching) {
                foreach ($oProductSaver->aErrors as $sError) {
                    echo '<div class="errorBox">' . $sError . '</div>';
                }
            }
        }
    }

    protected function saveMatchingAttributes($oProductSaver)
    {
        if (isset($_POST['Variations'])){
            parse_str($_POST['Variations'], $params);
            $_POST = $params;
        }

        $sIdentifier = $_POST['PrimaryCategory'];
        $matching = isset($_POST['ml']['match']) ? $_POST['ml']['match'] : false;
        $savePrepare = isset($_POST['saveMatching']) ? $_POST['saveMatching'] : false;

        $match = isset($_POST['match']) ? reset($_POST['match']) : '';
        if($match != 'false'){
            $oProductSaver->aErrors = array_merge($oProductSaver->aErrors,
                PriceministerHelper::gi()->saveMatching($sIdentifier, $matching, $savePrepare, true));
        }

        return $matching ? json_encode($matching['ShopVariation']) : false;
    }

    protected function deleteMatching()
    {
        if (!(array_key_exists('unprepare', $_POST)) || empty($_POST['unprepare'])){
            return;
        }

        $pIDs = MagnaDB::gi()->fetchArray('
			SELECT pID FROM ' . TABLE_MAGNA_SELECTION . '
			 WHERE mpID=\'' . $this->mpID . '\' AND
			       selectionname=\'' . $this->prepareSettings['selectionName'] . '\' AND
			       session_id=\'' . session_id() . '\'
		', true);

        if (empty($pIDs)){
            return;
        }
        foreach ($pIDs as $pID){
            $where = (getDBConfigValue('general.keytype', '0') == 'artNr')
                ? array('products_model' => MagnaDB::gi()->fetchOne('
							SELECT products_model
							  FROM ' . TABLE_PRODUCTS . '
							 WHERE products_id=' . $pID
                ))
                : array('products_id' => $pID);
            $where['mpID'] = $this->mpID;

            MagnaDB::gi()->delete(TABLE_MAGNA_PRICEMINISTER_PREPARE, $where);
            MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
                'pID' => $pID,
                'mpID' => $this->mpID,
                'selectionname' => $this->prepareSettings['selectionName'],
                'session_id' => session_id()
            ));
        }
        unset($_POST['unprepare']);
    }

    protected function processMatching()
    {
        if ($this->prepareSettings['selectionName'] === 'match'){
            $className = 'MatchingPrepareView';
        } elseif ($this->prepareSettings['selectionName'] === 'varmatch') {
            $className = 'VariationMatching';
        } else {
            $className = 'ApplyPrepareView';
        }

        if (($class = $this->loadResource('prepare', $className)) === false){
            if ($this->isAjax){
                echo '{"error": "This is not supported"}';
            } else {
                echo 'This is not supported';
            }

            return;
        }

        $params = array();
        foreach (array('mpID', 'marketplace', 'marketplaceName', 'resources', 'prepareSettings') as $attr){
            if (isset($this->$attr)){
                $params[$attr] = &$this->$attr;
            }
        }

        $cMDiag = new $class($params);

        echo $this->isAjax ? $cMDiag->renderAjax() : $cMDiag->process();
    }

    protected function processSelection()
    {
        if (($class = $this->loadResource('prepare', 'PrepareCategoryView')) === false){
            if ($this->isAjax){
                echo '{"error": "This is not supported"}';
            } else {
                echo 'This is not supported';
            }
            return;
        }
        $pV = new $class(null, $this->prepareSettings);
        if ($this->isAjax){
            echo $pV->renderAjaxReply();
        } else {
            echo $pV->printForm();
        }
    }

    protected function processProductList()
    {
        if ($this->prepareSettings['selectionName'] === 'match'){
            $className = 'MatchingProductList';
        } elseif ($this->prepareSettings['selectionName'] === 'varmatch') {
            $this->processMatching();
            return;
        } else {
            $className = 'ApplyProductList';
        }

        if (($sClass = $this->loadResource('prepare', $className)) === false){
            if ($this->isAjax){
                echo '{"error": "This is not supported"}';
            } else {
                echo 'This is not supported';
            }
            return;
        }

        $o = new $sClass();
        echo $o;
    }

    public function process()
    {
        if (isset($_POST['request'])){
            if (in_array($_POST['request'], array('ItemSearchByKW', 'ItemSearchByEAN', 'ItemSearchByCat', 'ItemSearchByProductId'))){
                echo $this->doSearch();
                return;
            }

            if ($_POST['request'] === 'AdvertAttrForCategory'){
                $mpAttributeTitle = str_replace('%marketplace%', ucfirst($this->marketplace), ML_GENERAL_VARMATCH_MP_ATTRIBUTE);
                $mpOptionalAttributeTitle = str_replace('%marketplace%', ucfirst($this->marketplace), ML_GENERAL_VARMATCH_MP_OPTIONAL_ATTRIBUTE);

                $html = '
<table id="variationMatcher" class="attributesTable">
   <input type="hidden" name="PrimaryCategory" id="PrimaryCategory"/> 
   <tbody id="tbodyDynamicMatchingHeadline" style="display:none;">
	    <tr class="headline">
		    <td colspan="1"><h4>'.$mpAttributeTitle.'</h4></td>
		    <td colspan="2"><h4>Mein Web-Shop Attribut</h4></td>
	    </tr>
	</tbody>
	<tbody id="tbodyDynamicMatchingInput" style="display:none;">
		<tr>
			<th></th>
			<td class="input">'.ML_GENERAL_VARMATCH_SELECT_CATEGORY.'</td>
			<td class="info"></td>
		</tr>
	</tbody>
	<tbody id="tbodyDynamicMatchingOptionalHeadline" style="display:none;">
	    <tr class="headline">
		    <td colspan="1"><h4>'.$mpOptionalAttributeTitle.'</h4></td>
		    <td colspan="2"><h4>Mein Web-Shop Attribut</h4></td>
	    </tr>
	</tbody>
	<tbody id="tbodyDynamicMatchingOptionalInput" style="display:none;">
		<tr>
			<th></th>
			<td class="input">'.ML_GENERAL_VARMATCH_SELECT_CATEGORY.'</td>
			<td class="info"></td>
		</tr>
	</tbody>
</table>
<p id="categoryInfo" style="display: none">' . ML_GENERAL_VARMATCH_CATEGORY_INFO . '</p>';
                echo $html;
                return;
            }

        }

        if (isset($_GET['automatching']) && $_GET['automatching'] === 'getProgress'){
            global $_MagnaSession;

            echo json_encode(array('x' => (int)MagnaDB::gi()->fetchOne("
			    SELECT count(pID)
			      FROM " . TABLE_MAGNA_SELECTION . "
			     WHERE     mpID = '" . $_MagnaSession['mpID'] . "'
			           AND selectionname = '" . $this->prepareSettings['selectionName'] . "'
			           AND session_id = '" . session_id() . "'
			  GROUP BY mpID
			")));
            return;
        } else if (isset($_GET['automatching']) && $_GET['automatching'] === 'start'){
            $autoMatchingStats = $this->insertAutoMatchProduct();
            $warningForCategories = empty($autoMatchingStats['categories']) ? '' : trim(sprintf(ML_PRICEMINISTER_TEXT_AUTOMATIC_MATCHING_SUMMARY_CATEGORIES, implode(', ', $autoMatchingStats['categories'])));

            $re = trim(sprintf(
                ML_PRICEMINISTER_TEXT_AUTOMATIC_MATCHING_SUMMARY,
                $autoMatchingStats['success'],
                $autoMatchingStats['nosuccess'],
                $autoMatchingStats['almost'],
                $warningForCategories
            ));
            echo magnalisterIsUTF8($re) ? $re : utf8_encode($re);
        }

        $this->saveMatching();
        $this->deleteMatching();

        $hasNextPage = isset($_POST['matching_nextpage']) && ctype_digit($_POST['matching_nextpage']);

        if (
            (
                isset($_POST['prepare']) ||
                (isset($_GET['where']) && (($_GET['where'] == 'catMatchView') || ($_GET['where'] == 'prepareView') || ($_GET['where'] == 'varmatchView'))) ||
                $hasNextPage
            ) &&
            ($this->getSelectedProductsCount() > 0)
        ){
            $this->processMatching();
        } else {
            if (defined('MAGNA_DEV_PRODUCTLIST') && MAGNA_DEV_PRODUCTLIST === true){
                $this->processProductList();
            } else {
                $this->processSelection();
            }
        }
    }

    protected function getSelectedProductsCount()
    {
        $query = '
			SELECT COUNT(*)
			FROM ' . TABLE_MAGNA_SELECTION . ' s
			LEFT JOIN ' . TABLE_MAGNA_PRICEMINISTER_PREPARE . ' p on p.mpID = s.mpID and p.products_id = s.pID
			WHERE s.mpID = ' . $this->mpID . '
			    AND s.selectionname = "' . $this->prepareSettings['selectionName'] . '"
			    AND s.session_id = "' . session_id() . '"
		';

        if (isset($_POST['match']) && $_POST['match'] === 'notmatched'){
            $query .= ' AND coalesce(p.Verified, "") != "OK"';
        }

        return (int)MagnaDB::gi()->fetchOne($query);
    }

    private function insertAutoMatchProduct()
    {
        $autoMatchingStats = array(
            'success' => 0,
            'almost' => 0,
            'nosuccess' => 0,
            'categories'=> array(),
            '_timer' => microtime(true)
        );

        $sClass = $this->loadResource('prepare', 'MatchingPrepareView');

        $params = array();
        foreach (array('mpID', 'marketplace', 'marketplaceName', 'resources', 'prepareSettings') as $attr){
            if (isset($this->$attr)){
                $params[$attr] = &$this->$attr;
            }
        }

        $v = new $sClass($params);
        $products = $v->getSelection(true);

        $sKeyType = 'products_' . ((getDBConfigValue('general.keytype', '0') == 'artNr') ? 'model' : 'id');

        foreach ($products as $product){
            $searchResults = PriceministerHelper::SearchOnPriceminister($product['EAN'], 'EAN');

            if ($searchResults === false
                || (is_array($searchResults) && count($searchResults) === 0)
            ){
                $searchResults = PriceministerHelper::SearchOnPriceminister($product['Title'], 'Title');
            }

            $iMatchedArrayKey = null;
            if (!empty($searchResults)){
                foreach ($searchResults as $sKey => $searchResult){
                    if ($searchResult['ean_match'] === true){
                        $iMatchedArrayKey = $sKey;
                        break;
                    }
                }
            } else {
                $searchResults = array();
            }

            if ($iMatchedArrayKey === null
                && count($searchResults) != 1
            ){
                if (count($searchResults) > 0){
                    $autoMatchingStats['almost']++;
                }
                $autoMatchingStats['nosuccess']++;
                MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
                    'pID' => $product['Id'],
                    'mpID' => $this->mpID,
                    'selectionname' => 'Match',
                    'session_id' => session_id()
                ));
                continue;
            } elseif ($iMatchedArrayKey === null) {
                $iMatchedArrayKey = 0;
            }

            $categoryAttributes = MagnaDB::gi()->fetchOne('SELECT ShopVariation FROM ' . TABLE_MAGNA_PRICEMINISTER_VARIANTMATCHING .
                " WHERE MpIdentifier like '{$searchResults[$iMatchedArrayKey]['alias']}' AND MpId = $this->mpID");

            if (empty($categoryAttributes)){
                $autoMatchingStats['categories'][] = $searchResults[$iMatchedArrayKey]['category_name'];
                continue;
            }

            $matchedProduct = array(
                'mpID' => $this->mpID,
                'products_id' => $product['Id'],
                'products_model' => $product['Model'],
                'Title' => $searchResults[$iMatchedArrayKey]['headline'],
                'EAN' => isset($searchResults[$iMatchedArrayKey]['references']['barcode']) ? $searchResults[$iMatchedArrayKey]['references']['barcode'] : '',
                'MarketplaceCategories' => $searchResults[$iMatchedArrayKey]['alias'],
                'MarketplaceCategoriesName' => $searchResults[$iMatchedArrayKey]['alias'],
                'TopMarketplaceCategory' => $searchResults[$iMatchedArrayKey]['alias'],
                'CategoryAttributes' => $categoryAttributes,
                'ConditionType' => $product['Condition'],
                'PrepareType' => 'Match',
                'MPProductId' => $searchResults[$iMatchedArrayKey]['productid'],
                'PreparedTS' => date('Y-m-d H:i:s'),
                'Verified' => 'OK'
            );

            MagnaDB::gi()->insert(TABLE_MAGNA_PRICEMINISTER_PREPARE, $matchedProduct, true);

            if (MLProduct::gi()->hasMasterItems()){
                // fetch master and insert dummy
                if ($sKeyType == 'products_model'){
                    $sData = $product['Model'];
                } else {
                    $sData = $product['Id'];
                }
                $aMaster = MagnaDb::gi()->fetchRow(eecho("
						SELECT m.products_id, m.products_model
						  FROM " . TABLE_PRODUCTS . " p
					INNER JOIN " . TABLE_PRODUCTS . " m ON p.products_master_model = m.products_model
						 WHERE p." . $sKeyType . " = '" . $sData . "'
				", false));

                if ($aMaster !== false){
                    MagnaDB::gi()->insert(TABLE_MAGNA_PRICEMINISTER_PREPARE, array(
                        'mpID' => $this->mpID,
                        'products_id' => $aMaster['products_id'],
                        'products_model' => $aMaster['products_model'],
                        'EAN' => 'dummyMasterProduct',
                        'PrepareType' => 'Match',
                        'Verified' => 'OK',
                        'PreparedTs' => date('Y-m-d H:i:s'),
                    ), true);
                }
            }

            MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
                'pID' => $product['Id'],
                'mpID' => $this->mpID,
                'selectionname' => 'Match',
                'session_id' => session_id()
            ));

            $autoMatchingStats['success']++;
        }

        return $autoMatchingStats;
    }

    private function doSearch()
    {
        switch ($_POST['request']){
            case 'ItemSearchByCat':
                $sSearch = $_POST['cat'];
                $sType = 'Category';
                break;
            case 'ItemSearchByEAN':
                $sSearch = $_POST['ean'];
                $sType = 'EAN';
                break;
            case 'ItemSearchByProductId':
                $sSearch = $_POST['mppid'];
                $sType = 'productids';
                break;
            default:
                $sSearch = $_POST['search'];
                $sType = 'KW';
        }

        $product = array(
            'Id' => $_POST['productID'],
            'Results' => PriceministerHelper::SearchOnPriceminister($sSearch, $sType)
        );

        $sClass = $this->loadResource('prepare', 'MatchingPrepareView');

        $params = array();
        foreach (array('mpID', 'marketplace', 'marketplaceName', 'resources', 'prepareSettings') as $attr){
            if (isset($this->$attr)){
                $params[$attr] = &$this->$attr;
            }
        }

        $v = new $sClass($params);
        $this->prepareSettings['selectionName'] = 'match';
        return $v->getSearchResultsHtml($product, $this->getSelectedProductsCount() === 1);
    }
}
