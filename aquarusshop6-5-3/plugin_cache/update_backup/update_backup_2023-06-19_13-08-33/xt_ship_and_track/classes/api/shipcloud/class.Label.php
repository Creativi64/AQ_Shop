<?php 

namespace Shipcloud;

/**
 * label specific definitions
 */
class Label extends JsonSerializable
{

	/**
	 * defines the DIN size that the returned label should have
	 * 
	 * @var string
	 */
	protected $size;

	public static function create()
	{
		return new static();
	}

	/**
	 * Setter for the size attribute.
	 * defines the DIN size that the returned label should have
	 * 
	 * @param string $value
	 */
	public function setSize($value) {
		$this->size = $value;
		return $this;
	}

	/**
	 * Provides the size attribute.
	 * defines the DIN size that the returned label should have
	 */
	public function size() {
		return $this->size;
	}
}