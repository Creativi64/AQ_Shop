<?php 

namespace Shipcloud;


class AdditionalService extends JsonSerializable
{

	/**
	 * defines the name for a service
	 * 
	 * @var string
	 */
	protected $name;

	/**
	 * defines the different properties for a service
	 *
	 * @var string
	 */
	protected $properties = array();

	public static function create()
	{
		return new static();
	}

	/**
	 * Setter for the name attribute.
	 * possible values advance_notice|cash_on_delivery|drop_authorization|saturday_delivery
	 * 
	 * @param string $value
	 */
	public function setName($value) {
		$this->name = $value;
		return $this;
	}

	/**
	 * Provides the name attribute.
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Sets an property of the service
	 * @param $name
	 * @param $value
	 * @return $this
	 */
	public function setProp($name, $value)
	{
		$this->properties[$name] = $value;
		return $this;
	}
}