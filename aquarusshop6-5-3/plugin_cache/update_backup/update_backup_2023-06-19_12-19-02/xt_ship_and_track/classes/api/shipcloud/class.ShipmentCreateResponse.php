<?php 

namespace Shipcloud;

/**
 * ShipmentCreateResponse
 */
class ShipmentCreateResponse extends JsonSerializable
{

	/**
	 * the original tracking number that can be used on the carriers website
	 * 
	 * @var string
	 */
	protected $carrier_tracking_no;

	/**
	 * the shipment id that can be used for requesting info about a shipment or tracking it
	 * 
	 * @var string
	 */
	protected $id;

	/**
	 * URL where you can download the label in pdf format
	 * 
	 * @var string
	 */
	protected $label_url;

	/**
	 * price that we're going to charge you (exl. VAT)
	 * 
	 * @var int
	 */
	protected $price;

	/**
	 * URL you can send your customers so they can track this shipment
	 * 
	 * @var string
	 */
	protected $tracking_url;

	/**
	 * Provides the carrier_tracking_no attribute.
	 * the original tracking number that can be used on the carriers website
	 */
	public function carrierTrackingNo() {
		return $this->carrier_tracking_no;
	}

	/**
	 * Provides the id attribute.
	 * the shipment id that can be used for requesting info about a shipment or tracking it
	 */
	public function id() {
		return $this->id;
	}

	/**
	 * Provides the label_url attribute.
	 * URL where you can download the label in pdf format
	 */
	public function labelUrl() {
		return $this->label_url;
	}

	/**
	 * Provides the price attribute.
	 * price that we're going to charge you (exl. VAT)
	 */
	public function price() {
		return $this->price;
	}

	/**
	 * Setter for the carrier_tracking_no attribute.
	 * the original tracking number that can be used on the carriers website
	 * 
	 * @param string $value
	 */
	public function setCarrierTrackingNo($value) {
		$this->carrier_tracking_no = $value;
		return $this;
	}

	/**
	 * Setter for the id attribute.
	 * the shipment id that can be used for requesting info about a shipment or tracking it
	 * 
	 * @param string $value
	 */
	public function setId($value) {
		$this->id = $value;
		return $this;
	}

	/**
	 * Setter for the label_url attribute.
	 * URL where you can download the label in pdf format
	 * 
	 * @param string $value
	 */
	public function setLabelUrl($value) {
		$this->label_url = $value;
		return $this;
	}

	/**
	 * Setter for the price attribute.
	 * price that we're going to charge you (exl. VAT)
	 * 
	 * @param int $value
	 */
	public function setPrice($value) {
		$this->price = $value;
		return $this;
	}

	/**
	 * Setter for the tracking_url attribute.
	 * URL you can send your customers so they can track this shipment
	 * 
	 * @param string $value
	 */
	public function setTrackingUrl($value) {
		$this->tracking_url = $value;
		return $this;
	}

	/**
	 * Provides the tracking_url attribute.
	 * URL you can send your customers so they can track this shipment
	 */
	public function trackingUrl() {
		return $this->tracking_url;
	}
}