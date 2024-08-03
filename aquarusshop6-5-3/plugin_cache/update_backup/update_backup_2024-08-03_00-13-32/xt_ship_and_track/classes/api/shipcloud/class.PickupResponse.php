<?php 

namespace Shipcloud;

/**
 * PickupResponse
 */
class PickupResponse {

	/**
	 * carrier used for this pickup request
	 * 
	 * @var string
	 */
	private $carrier;

	/**
	 * shipcloud identifier for this pickup request
	 * 
	 * @var string
	 */
	private $id;

	/**
	 * address where the carrier should pick up shipments
	 * 
	 * @var \stdClass
	 */
	private $pickup_address;

	/**
	 * defined time window when the carrier should pickup shipments
	 * 
	 * @var \stdClass
	 */
	private $pickup_time;

	/**
	 * Provides the carrier attribute.
	 * carrier used for this pickup request
	 */
	public function carrier() {
		return $this->carrier;
	}

	/**
	 * Provides the id attribute.
	 * shipcloud identifier for this pickup request
	 */
	public function id() {
		return $this->id;
	}

	/**
	 * Provides the pickup_address attribute.
	 * address where the carrier should pick up shipments
	 */
	public function pickupAddress() {
		return $this->pickup_address;
	}

	/**
	 * Provides the pickup_time attribute.
	 * defined time window when the carrier should pickup shipments
	 */
	public function pickupTime() {
		return $this->pickup_time;
	}

	/**
	 * Setter for the carrier attribute.
	 * carrier used for this pickup request
	 * 
	 * @param string $value
	 */
	public function setCarrier($value) {
		$this->carrier = $value;
		return $this;
	}

	/**
	 * Setter for the id attribute.
	 * shipcloud identifier for this pickup request
	 * 
	 * @param string $value
	 */
	public function setId($value) {
		$this->id = $value;
		return $this;
	}

	/**
	 * Setter for the pickup_address attribute.
	 * address where the carrier should pick up shipments
	 * 
	 * @param \stdClass $value
	 */
	public function setPickupAddress(\stdClass $value) {
		$this->pickup_address = $value;
		return $this;
	}

	/**
	 * Setter for the pickup_time attribute.
	 * defined time window when the carrier should pickup shipments
	 * 
	 * @param \stdClass $value
	 */
	public function setPickupTime(\stdClass $value) {
		$this->pickup_time = $value;
		return $this;
	}
}