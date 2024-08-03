<?php 

namespace Shipcloud;

/**
 * PickupRequest
 */
class PickupRequest  extends JsonSerializable
{
	/**
	 * acronym of the carrier you want to use
	 * 
	 * @var string
	 */
	protected $carrier;

	/**
	 * address where the carrier should pick up shipments
	 * 
	 * @var Address
	 */
	protected $pickup_address;

	/**
	 * deprecated: please use pickup_time instead.
	 * 
	 * @var string
	 */
	protected $pickup_date;

	/**
	 * defines a time window in which the carrier should pickup shipments
	 * 
	 * @var \stdClass
	 */
	protected $pickup_time;

	/**
	 * Provides the carrier attribute.
	 * acronym of the carrier you want to use
	 */
	public function carrier() {
		return $this->carrier;
	}

	/**
	 * Provides the pickup_address attribute.
	 * address where the carrier should pick up shipments
	 */
	public function pickupAddress() {
		return $this->pickup_address;
	}

	/**
	 * Provides the pickup_date attribute.
	 * deprecated: please use pickup_time instead.
	 */
	public function pickupDate() {
		return $this->pickup_date;
	}

	/**
	 * Provides the pickup_time attribute.
	 * defines a time window in which the carrier should pickup shipments
	 */
	public function pickupTime() {
		return $this->pickup_time;
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
	 * Setter for the pickup_address attribute.
	 * address where the carrier should pick up shipments
	 * 
	 * @param Address $value
	 */
	public function setPickupAddress(Address $value) {
		$this->pickup_address = $value;
		return $this;
	}

	/**
	 * Setter for the pickup_date attribute.
	 * deprecated: please use pickup_time instead.
	 * 
	 * @param string $value
	 */
	public function setPickupDate($value) {
		$this->pickup_date = $value;
		return $this;
	}

	/**
	 * Setter for the pickup_time attribute.
	 * defines a time window in which the carrier should pickup shipments
	 * 
	 * @param \stdClass $value
	 */
	public function setPickupTime(\stdClass $value) {
		$this->pickup_time = $value;
		return $this;
	}
}