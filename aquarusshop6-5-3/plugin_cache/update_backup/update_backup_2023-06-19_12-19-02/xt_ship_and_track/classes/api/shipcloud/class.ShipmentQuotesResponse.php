<?php 

namespace Shipcloud;

/**
 * ShipmentQuotesResponse
 */
class ShipmentQuotesResponse {

	/**
	 * price that we're going to charge you (exl. VAT)
	 * 
	 * @var int
	 */
	private $price;

	/**
	 * Provides the charge attribute.
	 * price that we're going to charge you (exl. VAT)
	 */
	public function price() {
		return $this->price;
	}

	/**
	 * Setter for the charge attribute.
	 * price that we're going to charge you (exl. VAT)
	 * 
	 * @param int $value
	 */
	public function setCharge($value) {
		$this->price = $value;
		return $this;
	}
}