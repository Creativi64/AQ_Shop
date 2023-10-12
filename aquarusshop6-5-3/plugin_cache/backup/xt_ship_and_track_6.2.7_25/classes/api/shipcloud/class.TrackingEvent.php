<?php 

namespace Shipcloud;

/**
 * defines a trackin event
 */
class TrackingEvent extends JsonSerializable
{

	/**
	 * message the carrier sends to describe the package status
	 * 
	 * @var string
	 */
	protected $details;

	/**
	 * location of the package at this moment
	 * 
	 * @var string
	 */
	protected $location;

	/**
	 * key describing the status
	 * 
	 * @var string
	 */
	protected $status;

	/**
	 * timestamp of when this event occured
	 * 
	 * @var DateTime
	 */
	protected $timestamp;

	/**
	 * Provides the details attribute.
	 * message the carrier sends to describe the package status
	 */
	public function details() {
		return $this->details;
	}

	/**
	 * Provides the location attribute.
	 * location of the package at this moment
	 */
	public function location() {
		return $this->location;
	}

	/**
	 * Setter for the details attribute.
	 * message the carrier sends to describe the package status
	 * 
	 * @param string $value
	 */
	public function setDetails($value) {
		$this->details = $value;
		return $this;
	}

	/**
	 * Setter for the location attribute.
	 * location of the package at this moment
	 * 
	 * @param string $value
	 */
	public function setLocation($value) {
		$this->location = $value;
		return $this;
	}

	/**
	 * Setter for the status attribute.
	 * key describing the status
	 * 
	 * @param string $value
	 */
	public function setStatus($value) {
		$this->status = $value;
		return $this;
	}

	/**
	 * Setter for the timestamp attribute.
	 * timestamp of when this event occured
	 * 
	 * @param DateTime $value
	 */
	public function setTimestamp(DateTime $value) {
		$this->timestamp = $value;
		return $this;
	}

	/**
	 * Provides the status attribute.
	 * key describing the status
	 */
	public function status() {
		return $this->status;
	}

	/**
	 * Provides the timestamp attribute.
	 * timestamp of when this event occured
	 */
	public function timestamp() {
		return $this->timestamp;
	}
}