<?php 

namespace Shipcloud;

/**
 * Carrier
 */
class CarrierResponse extends JsonSerializable
{

	/**
	 * name of the carrier you can use to display it in your application
	 * 
	 * @var string
	 */
	private $display_name;

	/**
	 * key for referencing the carrier within shipcloud
	 * 
	 * @var string
	 */
	private $name;

	/**
	 * @var ArrayObject
	 */
	private $package_types;

	/**
	 * @var ArrayObject
	 */
	private $services;

	/**
	 * Provides the display_name attribute.
	 * name of the carrier you can use to display it in your application
	 */
	public function displayName() {
		return $this->display_name;
	}

	/**
	 * Provides the name attribute.
	 * key for referencing the carrier within shipcloud
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Provides the package_types attribute.
	 */
	public function packageTypes() {
		return $this->package_types;
	}

	/**
	 * Provides the services attribute.
	 */
	public function services() {
		return $this->services;
	}

	/**
	 * Setter for the display_name attribute.
	 * name of the carrier you can use to display it in your application
	 * 
	 * @param string $value
	 */
	public function setDisplayName($value) {
		$this->display_name = $value;
		return $this;
	}

	/**
	 * Setter for the name attribute.
	 * key for referencing the carrier within shipcloud
	 * 
	 * @param string $value
	 */
	public function setName($value) {
		$this->name = $value;
		return $this;
	}

	/**
	 * Setter for the package_types attribute.
	 * 
	 * @param ArrayObject $value
	 */
	public function setPackageTypes(ArrayObject $value) {
		$this->package_types = $value;
		return $this;
	}

	/**
	 * Setter for the services attribute.
	 * 
	 * @param ArrayObject $value
	 */
	public function setServices(ArrayObject $value) {
		$this->services = $value;
		return $this;
	}
}