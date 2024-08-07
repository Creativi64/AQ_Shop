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

require_once(DIR_MAGNALISTER_MODULES.'amazon/classes/MLProductListAmazonAbstract.php');

class AmazonCheckinProductList extends MLProductListAmazonAbstract {
	
	public function __construct() {
		$this->aListConfig[] = array(
			'head' => array(
				'attributes'	=> 'class="lowestprice"',
				'content'		=> 'ML_GENERIC_LOWEST_PRICE',
			),
			'field' => array('amazonlowestprice'),
		);
		$this->aListConfig[] = array(
			'head' => array(
				'attributes'	=> 'class="lowestprice"',
				'content'		=> 'ML_LABEL_DATA_PREPARED',
			),
			'field' => array('amazonpreparetype'),
		);
		parent::__construct();
		$this
			->addDependency('MLProductListDependencyCheckinToSummaryAction', array('selectionname' => $this->getSelectionName()))
			#->addDependency('MLProductListDependencyNoVariantsFilter')
			->addDependency('MLProductListDependencyAmazonHistoryAction')
			->addDependency('MLProductListDependencyTemplateSelectionAction')
			->addDependency('MLProductListDependencyAmazonLastPreparedFilter')
		;
	}
	
	/**
	 * removing items which are in propertiestable
	 */
	protected function buildQuery(){
		$sKeyType = (getDBConfigValue('general.keytype', '0') == 'artNr') ? 'products_model' : 'products_id';
		parent::buildQuery()
			->oQuery->join(
				"(
					SELECT products_id FROM `".TABLE_MAGNA_AMAZON_APPLY."` WHERE data<>'' AND is_incomplete='false' AND mpID='".$this->aMagnaSession['mpID']."'
					UNION 
					SELECT products_id FROM `".TABLE_MAGNA_AMAZON_PROPERTIES."` WHERE asin <>'' AND mpID='".$this->aMagnaSession['mpID']."'
				) prepared on p.`products_id` = prepared.`products_id`",
				ML_Database_Model_Query_Select::JOIN_TYPE_INNER
			)
		;
		return $this;
	}
	
	protected function getSelectionName() {
		return 'checkin';
	}
	
	protected function getPrepareType($aRow) {
		if ($sPrepareType = $this->getPrepareData($aRow, 'preparetype')) {
			if ($sPrepareType == 'applied') {
				return ML_AMAZON_LABEL_PREPARE_IS_APPLIED;
			} elseif ($sPrepareType == 'matched') {
				return ML_AMAZON_LABEL_PREPARE_IS_MATCHED;
			} else {
				return ML_AMAZON_LABEL_PREPARE_IS_APPLIED.', '.ML_AMAZON_LABEL_PREPARE_IS_MATCHED;
			}
		} else {
			return '&mdash;';
		}
	}
	
}
