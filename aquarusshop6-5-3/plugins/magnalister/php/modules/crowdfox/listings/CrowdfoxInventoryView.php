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
 * (c) 2010 - 2019 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_MODULES . 'magnacompatible/listings/MagnaCompatibleInventoryView.php');

class CrowdfoxInventoryView extends MagnaCompatibleInventoryView {

    public function __construct($settings = array()) {
        global $_MagnaShopSession, $_MagnaSession, $_url, $_modules;

        $this->marketplace = $_MagnaSession['currentPlatform'];
        $this->mpID = $_MagnaSession['mpID'];

        $this->settings = array_merge(array(
            'maxTitleChars' => 40,
            'itemLimit' => 50,
            'language' => getDBConfigValue($this->marketplace . '.lang', $this->mpID, false),
        ), $settings);

        if ($this->settings['language'] === false){
            $this->settings['language'] = mlLanguageIDFromCode($_SESSION['magna']['selected_language']);
        }

        $this->simplePrice = new SimplePrice();
        $this->mpCurrency = getCurrencyFromMarketplace($this->mpID);
        $this->simplePrice->setCurrency($this->mpCurrency);
        $this->url = $_url;
        $this->url['view'] = 'inventory';
        $this->magnasession = &$_MagnaSession;
        $this->magnaShopSession = &$_MagnaShopSession;

        if (array_key_exists('tfSearch', $_POST) && !empty($_POST['tfSearch'])){
            $this->search = $_POST['tfSearch'];
        } else if (array_key_exists('search', $_GET) && !empty($_GET['search'])){
            $this->search = $_GET['search'];
        }
    }

    public function prepareInventoryData()
    {
        global $magnaConfig;

        $result = $this->getInventory();
        if (($result !== false) && !empty($result['DATA'])){
            $this->renderableData = $result['DATA'];
            foreach ($this->renderableData as &$item){
                if (isset($item['ItemTitle'])){
                    $item['MarketplaceTitle'] = $item['ItemTitle'];
                    $item['MarketplaceTitleShort'] = (mb_strlen($item['MarketplaceTitle'], 'UTF-8') > $this->settings['maxTitleChars'] + 2)
                        ? (fixHTMLUTF8Entities(mb_substr($item['MarketplaceTitle'], 0, $this->settings['maxTitleChars'], 'UTF-8')) . '&hellip;')
                        : fixHTMLUTF8Entities($item['MarketplaceTitle']);
                    unset($item['ItemTitle']);
                }
                if (is_array($this->settings['language'])){
                    $sLanguageId = current($this->settings['language']);
                } else{
                    $sLanguageId = $this->settings['language'];
                }
                $pID = magnaSKU2pID($item['SKU']);
                $sTitle = (string)MagnaDB::gi()->fetchOne("
					SELECT products_name
					FROM " . TABLE_PRODUCTS_DESCRIPTION . "
					WHERE products_id = '" . $pID . "'
					    AND language_code = '" . mlGetLanguageCodeFromID($sLanguageId) . "'
				");

                $item['Title'] = '&mdash;';
                if (!empty($sTitle)){
                    $item['Title'] = $sTitle;
                }

                $item['TitleShort'] = (mb_strlen($item['Title'], 'UTF-8') > $this->settings['maxTitleChars'] + 2)
                    ? (fixHTMLUTF8Entities(mb_substr($item['Title'], 0, $this->settings['maxTitleChars'], 'UTF-8')) . '&hellip;')
                    : fixHTMLUTF8Entities($item['Title']);
                $item['DateAdded'] = ((isset($item['DateAdded'])) ? strtotime($item['DateAdded']) : '');
            }
            unset($result);
        }

    }

    public function renderActionBox()
    {
        global $_modules;

        $js = '';
        $left = (!empty($this->renderableData) ?
            '<input type="button" class="ml-button" value="' . ML_BUTTON_LABEL_DELETE . '" id="listingDelete" name="listing[delete]"/>' :
            ''
        );

        ob_start(); ?>
        <script type="text/javascript">/*<![CDATA[*/
            $(document).ready(function () {
                $('#listingDelete').click(function () {
                    if (($('#csinventory input[type="checkbox"]:checked').length > 0) &&
                        confirm(unescape(<?php echo "'" . html2url(sprintf(ML_GENERIC_DELETE_LISTINGS, $_modules[$this->marketplace]['title'])) . "'"; ?>))
                    ) {
                        $('#action').val('delete');
                        $(this).parents('form').submit();
                    }
                });
            });
            /*]]>*/</script>
        <?php // Durch aufrufen der Seite wird automatisch ein Aktualisierungsauftrag gestartet
        $js = ob_get_contents();
        ob_end_clean();

        if ($left == ''){
            return '';
        }
        return '
			<input type="hidden" id="action" name="action" value="">
			<input type="hidden" name="timestamp" value="' . time() . '">
			<table class="actions">
				<thead><tr><th>' . ML_LABEL_ACTIONS . '</th></tr></thead>
				<tbody><tr><td>
					<table><tbody><tr>
						<td class="firstChild">' . $left . '</td>
						<td><label for="tfSearch">' . ML_LABEL_SEARCH . ':</label>
							<input id="tfSearch" name="tfSearch" type="text" value="' . fixHTMLUTF8Entities($this->search, ENT_COMPAT) . '"/>
							<input type="submit" class="ml-button" value="' . ML_BUTTON_LABEL_GO . '" name="search_go" /></td>
						<td class="lastChild"></td>
					</tr></tbody></table>
				</td></tr></tbody>
			</table>
			' . $js;
    }

    public function renderView()
    {
        $html = $this->renderLatestReport();
        $html .= '<form action="' . toUrl($this->url) . '" id="csInventoryView" method="post">';
        $this->initInventoryView();
        $html .= $this->renderInventoryTable();
        return $html . $this->renderActionBox() . '
			</form>
			<script type="text/javascript">/*<![CDATA[*/
				$(document).ready(function() {
					$(\'#csInventoryView\').submit(function () {
						jQuery.blockUI(blockUILoading);
					});
					$(\'#crowdfoxInfo\').click(function () {
						$(\'#infodiag\').jDialog();
					});
				});
			/*]]>*/</script>';
    }

    protected function getInventory() {
        try {
            $request = array(
                'ACTION' => 'GetInventory',
                'LIMIT' => $this->settings['itemLimit'],
                'OFFSET' => $this->offset,
                'ORDERBY' => $this->sort['order'],
                'SORTORDER' => $this->sort['type'],
            );
            if (!empty($this->search)) {
                $request['SEARCH'] = $this->search;
            }
            $result = MagnaConnector::gi()->submitRequest($request);
            $this->numberofitems = (int)$result['NUMBEROFLISTINGS'];
            return $result;

        } catch (MagnaException $e) {
            return false;
        }
    }

    protected function getSortOpt() {
        if (isset($_GET['sorting'])) {
            $sorting = $_GET['sorting'];
        } else {
            $sorting = 'blabla'; // fallback for default
        }
        $sortFlags = array (
            'sku' => 'SKU',
            'title' => 'ItemTitle',
            'productid' => 'ProductId',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'dateadded' => 'DateAdded'
        );
        $order = 'ASC';
        if (strpos($sorting, '-desc') !== false) {
            $order = 'DESC';
            $sorting = str_replace('-desc', '', $sorting);
        }
        if (array_key_exists($sorting, $sortFlags)) {
            $this->sort['order'] = $sortFlags[$sorting];
            $this->sort['type']  = $order;
        } else {
            $this->sort['order'] = 'DateAdded';
            $this->sort['type']  = 'DESC';
        }
    }

    private function renderLatestReport()
    {
        $latestReport = getDBConfigValue($this->magnasession['currentPlatform'] . '.inventory.import', $this->mpID);

        return '<table class="magnaframe">
					<thead><tr><th>' . ML_LABEL_NOTE . '</th></tr></thead>
					<tbody><tr><td class="fullWidth">
						<table>
							<tbody>
							<tr><td>' . CROWDFOX_INVENTORY_INFO . '

								</td>
								</tr>
							</tbody>
						</table>
					</td></tr></tbody>
				</table>';
    }

    protected function getFields()
    {
        return array(
            'SKU' => array(
                'Label' => ML_LABEL_SKU,
                'Sorter' => 'sku',
                'Getter' => null,
                'Field' => 'SKU'
            ),
            'Title' => array(
                'Label' => ML_LABEL_SHOP_TITLE,
                'Sorter' => null,
                'Getter' => 'getTitle',
                'Field' => null,
            ),
            'MarketplaceTitle' => array(
                'Label' => ML_CROWDFOX_LABEL_TITLE,
                'Sorter' => 'title',
                'Getter' => 'getMarketplaceTitle',
                'Field' => null,
            ),
            'ProductId' => array(
                'Label' => ML_CROWDFOX_LABEL_ITEM_ID,
                'Sorter' => 'productid',
                'Getter' => 'getProductIdLink',
                'Field' => null,
            ),
            'Price' => array(
                'Label' => ML_CROWDFOX_LABEL_PRICE,
                'Sorter' => 'price',
                'Getter' => 'getItemPrice',
                'Field' => null
            ),
            'Quantity' => array(
                'Label' => ML_STOCK_SHOP_STOCK_CROWDFOX,
                'Sorter' => 'quantity',
                'Getter' => 'getQuantities',
                'Field' => null,
            ),
            'LastSync' => array(
                'Label' => ML_GENERIC_CHECKINDATE,
                'Sorter' => 'lastsync',
                'Getter' => 'getItemLastSync',
                'Field' => null
            ),
            'Status' => array(
                'Label' => ML_GENERIC_INVENTORY_STATUS,
                'Sorter' => 'status',
                'Getter' => 'getStatus',
                'Field' => null
            )
        );
    }

    protected function getProductIdLink($item)
    {
        if (empty($item['ProductId'])) {
            return '<td>&mdash;</td>';
        }

        return '<td><a href="http://www.crowdfox.com/offer/buy/'.$item['ProductId'].'" target="_blank">'.$item['ProductId'].'</a></td>';
    }

    protected function getQuantities($item)
    {
        $shopQuantity = (int)MagnaDB::gi()->fetchOne("
			SELECT products_quantity
			  FROM " . TABLE_PRODUCTS . "
			 WHERE products_id = '" . magnaSKU2pID($item['SKU']) . "'
		");

        if ($shopQuantity == 0) {
            $shopQuantity = '&mdash;';
        }

        return '<td>' . $shopQuantity . ' / ' . $item['Quantity'] . '</td>';
    }

    protected function getItemLastSync($item)
    {
        if (empty($item['LastSync']) || $item['LastSync'] === '2000-01-01 00:00:00') {
            return '<td>&mdash;</td>';
        }

        return '<td>' . $item['LastSync'] . '</td>';
    }

    protected function getStatus($item)
    {
        if (isset($item['Status']) === false){
            $status = '-';
        } else if ($item['Status'] === 'Transferred'){
            $status = ML_GENERIC_INVENTORY_STATUS_TRANSFERRED;
        } else if ($item['Status'] === 'Update'){
            $status = ML_GENERIC_INVENTORY_STATUS_PENDING_UPDATE;
        } else{
            $status = ML_GENERIC_INVENTORY_STATUS_PENDING_NEW;
        }

        return '<td>' . $status . '</td>';
    }

    protected function getMarketplaceTitle($item)
    {
        return '<td title="' . fixHTMLUTF8Entities($item['MarketplaceTitle'], ENT_COMPAT) . '">' . $item['MarketplaceTitleShort'] . '</td>';
    }

    protected function renderDataGrid($id = '') {
        global $magnaConfig;

        $html = '
			<table' . (($id != '') ? ' id="' . $id . '"' : '') . ' class="datagrid">
				<thead class="small"><tr>
					<td class="nowrap" style="width: 5px;">
						<input type="checkbox" id="selectAll"/><label for="selectAll">' . ML_LABEL_CHOICE . '</label>
					</td>';
        $fieldsDesc = $this->getFields();
        foreach ($fieldsDesc as $fdesc) {
            $html .= '
					<td>' . $fdesc['Label'] .
                ((isset($fdesc['Sorter']) && ($fdesc['Sorter'] != null)) ? ' ' . $this->sortByType($fdesc['Sorter']) : '') .
                '</td>';
        }
        $html .= '
				</tr></thead>
				<tbody>
		';
        $oddEven = false;
        foreach ($this->renderableData as $item) {
            $details = htmlspecialchars(str_replace('"', '\\"', serialize(array(
                'SKU' => $item['SKU'],
                'Price' => $item['Price'],
                'Currency' => isset($item['Currency']) ? $item['Currency'] : $this->mpCurrency,
            ))));

            $addStyle = 'style="';
            $noBackgroundColor = false;
            if (empty($item['GTIN'])) {
                $noBackgroundColor = true;
                $addStyle .= empty($item['GTIN']) ? 'background-color: red;color: black' : '';
            } else {
                $addStyle .= ($item['Title'] === '&mdash;' && $item['SKU'] !== '&mdash;') ? 'color:#900;' : '';
            }

            $addStyle .= '"';

            $html .= '
				<tr class="' . (!$noBackgroundColor ? (($oddEven = !$oddEven) ? 'odd' : 'even') : '') . '" ' . $addStyle . '>
					<td><input type="checkbox" name="SKUs[]" value="' . $item['SKU'] . '">
						<input type="hidden" name="details[' . $item['SKU'] . ']" value="' . $details . '"></td>';
            foreach ($fieldsDesc as $fdesc) {
                if ($fdesc['Field'] != null) {
                    $html .= '
					<td>' . $item[$fdesc['Field']] . '</td>';
                } else {
                    $html .= '
					' . call_user_func(array($this, $fdesc['Getter']), $item);
                }
            }
            $html .= '
				</tr>';
        }
        $html .= '
				</tbody>
			</table>';

        return $html;
    }

}
