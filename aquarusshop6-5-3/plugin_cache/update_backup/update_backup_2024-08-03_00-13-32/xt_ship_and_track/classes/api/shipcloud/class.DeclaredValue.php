<?php 

namespace Shipcloud;

/**
 * declared value for higher insurance (dhl)
 */
class DeclaredValue extends JsonSerializable
{

	protected $amount;
	protected $currency;

	public static function create()
	{
		return new static();
	}
	
	public function setAmount($value) {
		$this->amount = $value;
		return $this;
	}
	
	public function amount() {
		return $this->amount;
	}

	public function setCurrency($value) {
		$this->currency = $value;
		return $this;
	}

	public function currency() {
		return $this->currency;
	}
}