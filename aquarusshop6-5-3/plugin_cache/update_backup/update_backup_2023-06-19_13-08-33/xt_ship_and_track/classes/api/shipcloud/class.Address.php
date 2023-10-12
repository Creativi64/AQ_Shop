<?php 

namespace Shipcloud;

/**
 * An address for a shipments to or from address
 */
class Address  extends JsonSerializable
{

	/**
	 * @var string
	 */
	protected $care_of;

	/**
	 * @var string
	 */
	protected $city;

	/**
	 * @var string
	 */
	protected $company;

	/**
	 * Country as uppercase ISO 3166-1 alpha-2 code
	 * 
	 * @var string
	 */
	protected $country;

	/**
	 * @var string
	 */
	protected $first_name;

	/**
	 * @var string
	 */
	protected $last_name;

	/**
	 * telephone number (mandatory when using UPS and the following terms apply: service is one_day or one_day_early or ship to country is different than ship from country)
	 * 
	 * @var string
	 */
	protected $phone;

	/**
	 * @var string
	 */
	protected $state;

	/**
	 * @var string
	 */
	protected $street;

	/**
	 * @var string
	 */
	protected $street_no;

	/**
	 * @var string
	 */
	protected $zip_code;

	public static function create()
	{
		return new static();
	}

	/**
	 * Provides the care_of attribute.
	 */
	public function careOf() {
		return $this->care_of;
	}

	/**
	 * Provides the city attribute.
	 */
	public function city() {
		return $this->city;
	}

	/**
	 * Provides the company attribute.
	 */
	public function company() {
		return $this->company;
	}

	/**
	 * Provides the country attribute.
	 * Country as uppercase ISO 3166-1 alpha-2 code
	 */
	public function country() {
		return $this->country;
	}

	/**
	 * Provides the first_name attribute.
	 */
	public function firstName() {
		return $this->first_name;
	}

	/**
	 * Provides the last_name attribute.
	 */
	public function lastName() {
		return $this->last_name;
	}

	/**
	 * Provides the phone attribute.
	 * telephone number (mandatory when using UPS and the following terms apply: service is one_day or one_day_early or ship to country is different than ship from country)
	 */
	public function phone() {
		return $this->phone;
	}

	/**
	 * Setter for the care_of attribute.
	 * 
	 * @param string $value
	 */
	public function setCareOf($value) {
		$this->care_of = $value;
		return $this;
	}

	/**
	 * Setter for the city attribute.
	 * 
	 * @param string $value
	 */
	public function setCity($value) {
		$this->city = $value;
		return $this;
	}

	/**
	 * Setter for the company attribute.
	 * 
	 * @param string $value
	 */
	public function setCompany($value) {
		$this->company = $value;
		return $this;
	}

	/**
	 * Setter for the country attribute.
	 * Country as uppercase ISO 3166-1 alpha-2 code
	 * 
	 * @param string $value
	 */
	public function setCountry($value) {
		$this->country = $value;
		return $this;
	}

	/**
	 * Setter for the first_name attribute.
	 * 
	 * @param string $value
	 */
	public function setFirstName($value) {
		$this->first_name = $value;
		return $this;
	}

	/**
	 * Setter for the last_name attribute.
	 * 
	 * @param string $value
	 */
	public function setLastName($value) {
		$this->last_name = $value;
		return $this;
	}

	/**
	 * Setter for the phone attribute.
	 * telephone number (mandatory when using UPS and the following terms apply: service is one_day or one_day_early or ship to country is different than ship from country)
	 * 
	 * @param string $value
	 */
	public function setPhone($value) {
		$this->phone = $value;
		return $this;
	}

	/**
	 * Setter for the state attribute.
	 * 
	 * @param string $value
	 */
	public function setState($value) {
		$this->state = $value;
		return $this;
	}

	/**
	 * Setter for the street attribute.
	 * 
	 * @param string $value
	 */
	public function setStreet($value) {
		$this->street = $value;
		return $this;
	}

	/**
	 * Setter for the street_no attribute.
	 * 
	 * @param string $value
	 */
	public function setStreetNo($value) {
		$this->street_no = $value;
		return $this;
	}

	/**
	 * Setter for the zip_code attribute.
	 * 
	 * @param string $value
	 */
	public function setZipCode($value) {
		$this->zip_code = $value;
		return $this;
	}

	/**
	 * Provides the state attribute.
	 */
	public function state() {
		return $this->state;
	}

	/**
	 * Provides the street attribute.
	 */
	public function street() {
		return $this->street;
	}

	/**
	 * Provides the street_no attribute.
	 */
	public function streetNo() {
		return $this->street_no;
	}

	/**
	 * Provides the zip_code attribute.
	 */
	public function zipCode() {
		return $this->zip_code;
	}

	public function __toString()
	{
		return '';
	}

	public function buildString($glue = '|', $omitEmty = true)
	{
		foreach ($this as $key => $value)
		{
			$result[$key] = object_to_array($value);
		}
		$array = json_decode(json_encode($this), true);
		$objaa = (array) $this;
		foreach($objaa as $k => $v)
		{
			$a = 0;
		}
		//$objVars =  $vals = array_values(get_object_vars($obj));
		//$vals = array_values(get_object_vars($objVars));
		$a = 0;
	}
}