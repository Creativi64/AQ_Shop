<?php 

namespace Shipcloud;

/**
 * ShipmentCreateRequest
 */
class ShipmentCreateRequest extends JsonSerializable
{

	/**
	 * @var array
	 */
	protected $additional_services = array();

	/**
	 * acronym of the carrier you want to use
	 * 
	 * @var string
	 */
	protected $carrier;

	/**
	 * determines if a shipping label should be created at the carrier (this means you will be charged when using the production api key)
	 * 
	 * @var bool
	 */
	protected $create_shipping_label;

	/**
	 * text that describes the contents of the shipment. This parameter is mandatory if you're using UPS and the following conditions are true: from and to countries are not the same; from and/or to countries are not in the EU; from and to countries are in the EU and the shipments service is not standard
	 * 
	 * @var string
	 */
	protected $description;

	/**
	 * If missing, the default sender address (if defined in your shipcloud account) will be used
	 * 
	 * @var Address
	 */
	protected $from;

	/**
	 * label characteristics
	 * 
	 * @var Label
	 */
	protected $label;

	/**
	 * here you can save additional data that you want to be associated with the shipment. Any combination of key-value pairs is possible
	 * 
	 * @var \stdClass
	 */
	protected $metadata;

	/**
	 * email address that we should notify once there's an update for this shipment
	 * 
	 * @var string
	 */
	protected $notification_email;

	/**
	 * @var Package
	 */
	protected $package;

	/**
	 * pickup request for this shipment
	 * 
	 * @var Pickup
	 */
	protected $pickup;

	/**
	 * a reference number (max. 30 characters) that you want this shipment to be identified with. You can use this afterwards to easier find the shipment in the shipcloud.io backoffice
	 * 
	 * @var string
	 */
	protected $reference_number;

	/**
	 * The service that should be used for the shipment. standard: The standard (ground) service for each carrier. returns: Returns service. one_day: express delivery. one_day_early: express delivery until 10am.
	 *
	 * @var string
	 */
	protected $service;

	/**
	 * the receivers address
	 * 
	 * @var Address
	 */
	protected $to;

	public static function create()
	{
		return new static();
	}

	/**
	 * Provides the additional_services attribute.
	 */
	public function additionalServices() {
		return $this->additional_services;
	}

	/**
	 * Provides the carrier attribute.
	 * acronym of the carrier you want to use
	 */
	public function carrier() {
		return $this->carrier;
	}

	/**
	 * Provides the create_shipping_label attribute.
	 * determines if a shipping label should be created at the carrier (this means you will be charged when using the production api key)
	 */
	public function createShippingLabel() {
		return $this->create_shipping_label;
	}

	/**
	 * Provides the description attribute.
	 * text that describes the contents of the shipment. This parameter is mandatory if you're using UPS and the following conditions are true: from and to countries are not the same; from and/or to countries are not in the EU; from and to countries are in the EU and the shipments service is not standard
	 */
	public function description() {
		return $this->description;
	}

	/**
	 * Provides the from attribute.
	 * If missing, the default sender address (if defined in your shipcloud account) will be used
	 */
	public function from() {
		return $this->from;
	}

	/**
	 * Provides the label attribute.
	 * label characteristics
	 */
	public function label() {
		return $this->label;
	}

	/**
	 * Provides the metadata attribute.
	 * here you can save additional data that you want to be associated with the shipment. Any combination of key-value pairs is possible
	 */
	public function metadata() {
		return $this->metadata;
	}

	/**
	 * Provides the notification_email attribute.
	 * email address that we should notify once there's an update for this shipment
	 */
	public function notificationEmail() {
		return $this->notification_email;
	}

	/**
	 * Provides the package attribute.
	 */
	public function package() {
		return $this->package;
	}

	/**
	 * Provides the pickup attribute.
	 * pickup request for this shipment
	 */
	public function pickup() {
		return $this->pickup;
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
	 * The service that should be used for the shipment. standard: The standard (ground) service for each carrier. returns: Returns service. one_day: express delivery. one_day_early: express delivery until 10am.
	 */
	public function service() {
		return $this->service;
	}

	/**
	 * Setter for the additional_services attribute.
	 * 
	 * @param ArrayObject $value
	 */
	public function setAdditionalServices(ArrayObject $value) {
		$this->additional_services = $value;
		return $this;
	}

	/**
	 * Setter for the carrier attribute.
	 * acronym of the carrier you want to use
	 * 
	 * @param string $value
	 */
	public function setCarrier($value) {
		$this->carrier = $value;
		return $this;
	}

	/**
	 * Setter for the create_shipping_label attribute.
	 * determines if a shipping label should be created at the carrier (this means you will be charged when using the production api key)
	 * 
	 * @param bool $value
	 */
	public function setCreateShippingLabel($value) {
		$this->create_shipping_label = $value;
		return $this;
	}

	/**
	 * Setter for the description attribute.
	 * text that describes the contents of the shipment. This parameter is mandatory if you're using UPS and the following conditions are true: from and to countries are not the same; from and/or to countries are not in the EU; from and to countries are in the EU and the shipments service is not standard
	 * 
	 * @param string $value
	 */
	public function setDescription($value) {
		$this->description = $value;
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
	 * Setter for the label attribute.
	 * label characteristics
	 * 
	 * @param Label $value
	 */
	public function setLabel(Label $value) {
		$this->label = $value;
		return $this;
	}

	/**
	 * Setter for the metadata attribute.
	 * here you can save additional data that you want to be associated with the shipment. Any combination of key-value pairs is possible
	 * 
	 * @param \stdClass $value
	 */
	public function setMetadata(\stdClass $value) {
		$this->metadata = $value;
		return $this;
	}

	/**
	 * Setter for the notification_email attribute.
	 * email address that we should notify once there's an update for this shipment
	 * 
	 * @param string $value
	 */
	public function setNotificationEmail($value) {
		$this->notification_email = $value;
		return $this;
	}

	/**
	 * Setter for the package attribute.
	 * 
	 * @param Package $value
	 */
	public function setPackage(Package $value) {
		$this->package = $value;
		return $this;
	}

	/**
	 * Setter for the pickup attribute.
	 * pickup request for this shipment
	 * 
	 * @param Pickup $value
	 */
	public function setPickup(Pickup $value) {
		$this->pickup = $value;
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
	 * The service that should be used for the shipment. standard: The standard (ground) service for each carrier. returns: Returns service. one_day: express delivery. one_day_early: express delivery until 10am.
	 * 
	 * @param string $value
	 */
	public function setService($value) {
		$this->service = $value;
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
	 * Provides the to attribute.
	 * the receivers address
	 */
	public function to() {
		return $this->to;
	}

	public function  addAdditonalService(AdditionalService $as)
	{
		$this->additional_services[] = $as;
		return $this;
	}
}