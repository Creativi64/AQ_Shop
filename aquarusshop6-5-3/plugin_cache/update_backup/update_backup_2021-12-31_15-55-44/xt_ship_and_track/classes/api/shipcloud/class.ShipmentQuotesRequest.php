<?php 

namespace Shipcloud;

/**
 * ShipmentQuotesRequest
 */
class ShipmentQuotesRequest extends JsonSerializable
{
	/**
	 * acronym of the carrier you want to use
	 * 
	 * @var Carrier
	 */
	protected $carrier;

	/**
	 * the senders address
	 *
	 * @var Address
	 */
	protected $from;

	/**
	 * @var Package
	 */
	protected $package;

	/**
	 * additional service. With 'one_day' you get express delivery, 'one_day_early' is express delivery until 10am.
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


	/**
	 * the package type (parcel, letter ..)
	 *
	 * @var package_type
	 */
	//protected $package_type;


	public static function create()
	{
		return new static();
	}

	/**
	 * Provides the carrier attribute.
	 * acronym of the carrier you want to use
	 */
	public function carrier() {
		return $this->carrier;
	}

	/**
	 * Provides the from attribute.
	 * the senders address
	 */
	public function from() {
		return $this->from;
	}

	/**
	 * Provides the package attribute.
	 */
	public function package() {
		return $this->package;
	}

	/**
	 * Provides the service attribute.
	 * additional service. With 'one_day' you get express delivery, 'one_day_early' is express delivery until 10am.
	 */
	public function service() {
		return $this->service;
	}

	public function packageType() {
		return $this->package_type();
	}

	/**
	 * Setter for the carrier attribute.
	 * acronym of the carrier you want to use
	 * 
	 * @param Carrier $value
	 */
	public function setCarrier($value) {
		$this->carrier = $value;
		return $this;
	}

	/**
	 * Setter for the from attribute.
	 * the senders address
	 * 
	 * @param Address $value
	 */
	public function setFrom(Address $value) {
		$this->from = $value;
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
	 * Setter for the service attribute.
	 * additional service. With 'one_day' you get express delivery, 'one_day_early' is express delivery until 10am.
	 * 
	 * @param string $value
	 */
	public function setService($value) {
		$this->service = $value;
		return $this;
	}

	/**
	 * Setter for the service attribute.
	 * additional service. With 'one_day' you get express delivery, 'one_day_early' is express delivery until 10am.
	 *
	 * @param string $value
	 */
	public function setPackageType($value) {
		$this->package_type = $value;
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

}