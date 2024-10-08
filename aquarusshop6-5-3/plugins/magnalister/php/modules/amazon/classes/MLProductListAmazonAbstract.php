<?php
require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/MLProductList.php');

abstract class MLProductListAmazonAbstract extends MLProductList {
	
	protected $aPrepareData = array();
	
	protected function getPrepareData($aRow, $sFieldName = null) {
		if (!isset($this->aPrepareData[$aRow['products_id']])) {
			$aApplyData = MagnaDB::gi()->fetchRow("
				SELECT * 
				FROM ".TABLE_MAGNA_AMAZON_APPLY." 
				WHERE ".(
						(getDBConfigValue('general.keytype', '0') == 'artNr')
							? 'products_model=\''.MagnaDB::gi()->escape($aRow['products_model']).'\''
							: 'products_id=\''.$aRow['products_id'].'\''
				)."
				AND mpID = '".$this->aMagnaSession['mpID']."'
			");
			$aMatchData =  MagnaDB::gi()->fetchRow("
				SELECT * 
				FROM ".TABLE_MAGNA_AMAZON_PROPERTIES." 
				WHERE ".(
						(getDBConfigValue('general.keytype', '0') == 'artNr')
							? 'products_model=\''.MagnaDB::gi()->escape($aRow['products_model']).'\''
							: 'products_id=\''.$aRow['products_id'].'\''
				)."
				AND mpID = '".$this->aMagnaSession['mpID']."'
			");
			
			if (empty($aApplyData) && empty($aMatchData)) {
				$this->aPrepareData[$aRow['products_id']] = array();
			} elseif (empty($aMatchData)) {
				$aApplyData['preparetype'] = 'applied';
				$this->aPrepareData[$aRow['products_id']] = $aApplyData;
			} elseif (empty($aApplyData)) {
				$aMatchData['preparetype'] = 'matched';
				$this->aPrepareData[$aRow['products_id']] = $aMatchData;
			} else { //both - not good
				$aApplyData['preparetype'] = array('matched', 'applied');
				$aData = $aApplyData;
				foreach ($aMatchData as $sKey => $mValue) {
					$aData[$sKey] = $mValue;
				}
				$this->aPrepareData[$aRow['products_id']] = $aData;
			}
		}
		if($sFieldName === null){
			return $this->aPrepareData[$aRow['products_id']];
		}else{
			return isset($this->aPrepareData[$aRow['products_id']][$sFieldName]) ? $this->aPrepareData[$aRow['products_id']][$sFieldName] : null;
		}
	}
	
	protected function getPreparedStatusIndicator($aRow){
		$aData = $this->getPrepareData($aRow);
		if (empty($aData)) {
			return html_image(DIR_MAGNALISTER_IMAGES . 'status/grey_dot.png', ML_AMAZON_LABEL_APPLY_NOT_PREPARED, 9, 9);
		}elseif (
			(isset($aData['is_incomplete']) && 'true' == $aData['is_incomplete'])//apply
			||
			(isset($aData['asin']) && empty($aData['asin']))//matching
		) {
			return html_image(DIR_MAGNALISTER_IMAGES . 'status/red_dot.png', ML_AMAZON_LABEL_APPLY_PREPARE_INCOMPLETE, 9, 9);
		}else{
			return html_image(DIR_MAGNALISTER_IMAGES . 'status/green_dot.png', ML_AMAZON_LABEL_APPLY_PREPARE_COMPLETE, 9, 9);
		}
	}

	protected function isPreparedDifferently($aRow) {
		$aData = $this->getPrepareData($aRow, 'data');
		if (!empty($aData)) {
			$aData = unserialize(base64_decode($aData));
			if (!isset($aData['ShopVariation'])) {
				// product prepared before attributes matching - if it has attributes before then it is prepared differently
				return isset($aData['Attributes']);
			}

			$category = $aData['MainCategory'];
			$categoryMatching = AmazonHelper::gi()->getCategoryMatching($category);
			$shopVariation = is_array($aData['ShopVariation']) ? $aData['ShopVariation'] : json_decode($aData['ShopVariation'], true);
			return AmazonHelper::gi()->detectChanges($categoryMatching, $shopVariation);
		}

		return false;
	}
	
	protected function getLowestPrice($aRow){
		$fLowestPrice = $this->getPrepareData($aRow, 'lowestprice');
		return $fLowestPrice > 0 ? $this->getPrice()->setPrice($fLowestPrice)->format() : '&mdash;';
	}
}