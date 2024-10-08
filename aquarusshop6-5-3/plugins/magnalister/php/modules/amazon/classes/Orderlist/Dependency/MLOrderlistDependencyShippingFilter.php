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
 * (c) 2010 - 2021 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
require_once DIR_MAGNALISTER_MODULES_AMAZON_ORDERLIST . '/Dependency/MLOrderlistDependency.php';

class MLOrderlistDependencyShippingFilter extends MLOrderlistDependency {

	public function manipulateQuery() {
		if($this->getFilterRequest() !== null){
			$this->oQuery->set(array('FulfillmentChannel' => $this->getFilterRequest()));
		}
		return $this;
	}

	public function getFilterRightTemplate() {
		return 'select';
	}

	protected function getDefaultConfig() {
		return array(
			'selectValues' => array(
                'all' =>  ML_AMAZON_SHIPPINGLABEL_FILTER_ORDERTYPE_DEFAULT,
                'MFN' => ML_AMAZON_SHIPPINGLABEL_FILTER_ORDERTYPE_MFN,
                'MFN-Prime' => ML_AMAZON_SHIPPINGLABEL_FILTER_ORDERTYPE_MFNPRIME,
                'Business' => ML_AMAZON_SHIPPINGLABEL_FILTER_ORDERTYPE_BUSINESS,
                'AFN' => ML_AMAZON_SHIPPINGLABEL_FILTER_ORDERTYPE_FBA,
				'SameDay' =>  ML_AMAZON_SHIPPINGLABEL_FILTER_ORDERTYPE_PRIME_SAMEDAY,
                'NextDay' => ML_AMAZON_SHIPPINGLABEL_FILTER_ORDERTYPE_PRIME_NEXTDAY,
                'SecondDay' => ML_AMAZON_SHIPPINGLABEL_FILTER_ORDERTYPE_PRIME_SECONDDAY
			),
			'statusconditions' => array(
			// string => ML_Database_Model_Query_Select over keytype for filtering
			)
		);
	}


}
