<?php 

namespace Shipcloud;

/**
 * WebhookCreateResponse
 */
class WebhookCreateResponse {

	/**
	 * is set to true, if the call of the webhook url fails ten times.
	 * 
	 * @var bool
	 */
	private $deactivated;

	/**
	 * the subscripted event types
	 * 
	 * @var ArrayObject
	 */
	private $event_types;

	/**
	 * the webhook id that can be used for requesting info about a webhook or deleting it
	 * 
	 * @var string
	 */
	private $id;

	/**
	 * the URL of the webhook
	 * 
	 * @var string
	 */
	private $url;

	/**
	 * Provides the deactivated attribute.
	 * is set to true, if the call of the webhook url fails ten times.
	 */
	public function deactivated() {
		return $this->deactivated;
	}

	/**
	 * Provides the event_types attribute.
	 * the subscripted event types
	 */
	public function eventTypes() {
		return $this->event_types;
	}

	/**
	 * Provides the id attribute.
	 * the webhook id that can be used for requesting info about a webhook or deleting it
	 */
	public function id() {
		return $this->id;
	}

	/**
	 * Setter for the deactivated attribute.
	 * is set to true, if the call of the webhook url fails ten times.
	 * 
	 * @param bool $value
	 */
	public function setDeactivated($value) {
		$this->deactivated = $value;
		return $this;
	}

	/**
	 * Setter for the event_types attribute.
	 * the subscripted event types
	 * 
	 * @param ArrayObject $value
	 */
	public function setEventTypes(ArrayObject $value) {
		$this->event_types = $value;
		return $this;
	}

	/**
	 * Setter for the id attribute.
	 * the webhook id that can be used for requesting info about a webhook or deleting it
	 * 
	 * @param string $value
	 */
	public function setId($value) {
		$this->id = $value;
		return $this;
	}

	/**
	 * Setter for the url attribute.
	 * the URL of the webhook
	 * 
	 * @param string $value
	 */
	public function setUrl($value) {
		$this->url = $value;
		return $this;
	}

	/**
	 * Provides the url attribute.
	 * the URL of the webhook
	 */
	public function url() {
		return $this->url;
	}
}