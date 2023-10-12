<?php 

namespace Shipcloud;

/**
 * ShipmentIndexResponse
 */
class ShipmentIndexResponse extends JsonSerializable
{
	/**
	 * acronym of the carrier
	 * 
	 * @var string
	 */
	protected $carrier;

	/**
	 * the original tracking number that can be used on the carriers website
	 * 
	 * @var string
	 */
	protected $carrier_tracking_no;

	/**
	 * timestamp the shipment was created
	 * 
	 * @var DateTime
	 */
	protected $created_at;

	/**
	 * If missing, the default sender address (if defined in your shipcloud account) will be used
	 * 
	 * @var Address
	 */
	protected $from;

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
	 * email address of the receiver who should be notified of a change of shipment status by shipcloud
	 * 
	 * @var string
	 */
	protected $notification_email;

	/**
	 * @var ArrayObject
	 */
	protected $packages;

	/**
	 * price that we're going to charge you (exl. VAT)
	 * 
	 * @var int
	 */
	protected $price;

	/**
	 * a reference number (max. 30 characters) that you want this shipment to be identified with. You can use this afterwards to easier find the shipment in the shipcloud.io backoffice
	 * 
	 * @var string
	 */
	protected $reference_number;

	/**
	 * the service that was used for creating this shipment
	 * 
	 * @var string
	 */
	protected $service;

	/**
	 * email address of the shipper who should be notified of a change of shipment status by shipcloud
	 * 
	 * @var string
	 */
	protected $shipper_notification_email;

	/**
	 * the receivers address
	 * 
	 * @var Address
	 */
	protected $to;

	/**
	 * URL you can send your customers so they can track this shipment
	 * 
	 * @var string
	 */
	protected $tracking_url;


	public static function create()
	{
		return new static();
	}


	/**
	 * Provides the carrier attribute.
	 * acronym of the carrier
	 */
	public function carrier() {
		return $this->carrier;
	}

	/**
	 * Provides the carrier_tracking_no attribute.
	 * the original tracking number that can be used on the carriers website
	 */
	public function carrierTrackingNo() {
		return $this->carrier_tracking_no;
	}

	/**
	 * Provides the created_at attribute.
	 * timestamp the shipment was created
	 */
	public function createdAt() {
		return $this->created_at;
	}

	/**
	 * Provides the from attribute.
	 * If missing, the default sender address (if defined in your shipcloud account) will be used
	 */
	public function from() {
		return $this->from;
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
	 * Provides the notification_email attribute.
	 * email address of the receiver who should be notified of a change of shipment status by shipcloud
	 */
	public function notificationEmail() {
		return $this->notification_email;
	}

	/**
	 * Provides the packages attribute.
	 */
	public function packages() {
		return $this->packages;
	}

	/**
	 * Provides the price attribute.
	 * price that we're going to charge you (exl. VAT)
	 */
	public function price() {
		return $this->price;
	}

	/**
	 * Provides the reference_number attribute.
	 * a reference number (max. 30 characters) that you want this shipment to be identified with. You can use this afterwards to easier find the shipment in the shipcloud.io backoffice
	 */
	public function referenceNumber() {
		return $this->reference_number;
	}

	/**
	 * Provides the service attribute.
	 * the service that was used for creating this shipment
	 */
	public function service() {
		return $this->service;
	}

	/**
	 * Setter for the carrier attribute.
	 * acronym of the carrier
	 * 
	 * @param string $value
	 */
	public function setCarrier($value) {
		$this->carrier = $value;
		return $this;
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
	 * Setter for the created_at attribute.
	 * timestamp the shipment was created
	 * 
	 * @param DateTime $value
	 */
	public function setCreatedAt(DateTime $value) {
		$this->created_at = $value;
		return $this;
	}

	/**
	 * Setter for the from attribute.
	 * If missing, the default sender address (if defined in your shipcloud account) will be used
	 * 
	 * @param Address $value
	 */
	public function setFrom(Address $value) {
		$this->from = $value;
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
	 * Setter for the notification_email attribute.
	 * email address of the receiver who should be notified of a change of shipment status by shipcloud
	 * 
	 * @param string $value
	 */
	public function setNotificationEmail($value) {
		$this->notification_email = $value;
		return $this;
	}

	/**
	 * Setter for the packages attribute.
	 * 
	 * @param ArrayObject $value
	 */
	public function setPackages(ArrayObject $value) {
		$this->packages = $value;
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
	 * Setter for the reference_number attribute.
	 * a reference number (max. 30 characters) that you want this shipment to be identified with. You can use this afterwards to easier find the shipment in the shipcloud.io backoffice
	 * 
	 * @param string $value
	 */
	public function setReferenceNumber($value) {
		$this->reference_number = $value;
		return $this;
	}

	/**
	 * Setter for the service attribute.
	 * the service that was used for creating this shipment
	 * 
	 * @param string $value
	 */
	public function setService($value) {
		$this->service = $value;
		return $this;
	}

	/**
	 * Setter for the shipper_notification_email attribute.
	 * email address of the shipper who should be notified of a change of shipment status by shipcloud
	 * 
	 * @param string $value
	 */
	public function setShipperNotificationEmail($value) {
		$this->shipper_notification_email = $value;
		return $this;
	}

	/**
	 * Setter for the to attribute.
	 * the receivers address
	 * 
	 * @param Address $value
	 */
	public function setTo(Address $value) {
		$this->to = $value;
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
	 * Provides the shipper_notification_email attribute.
	 * email address of the shipper who should be notified of a change of shipment status by shipcloud
	 */
	public function shipperNotificationEmail() {
		return $this->shipper_notification_email;
	}

	/**
	 * Provides the to attribute.
	 * the receivers address
	 */
	public function to() {
		return $this->to;
	}

	/**
	 * Provides the tracking_url attribute.
	 * URL you can send your customers so they can track this shipment
	 */
	public function trackingUrl() {
		return $this->tracking_url;
	}
}