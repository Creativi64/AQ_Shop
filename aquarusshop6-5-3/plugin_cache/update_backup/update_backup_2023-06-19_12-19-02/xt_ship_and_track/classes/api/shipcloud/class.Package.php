<?php 

namespace Shipcloud;

/**
 * defines package dimensions
 */
class Package  extends JsonSerializable
{
	/**
	 * @var int
	 */
	protected $height;

	/**
	 * @var int
	 */
	protected $length;

	/**
	 * @var int
	 */
	protected $weight;

	/**
	 * @var int
	 */
	protected $width;

	protected $type;
	/**
	 * Declare amount of goods for higher ensurans (dhl)
	 *
	 * @var string
	 */
	protected $declared_value;

    /**
     * description of content
     *
     * @var string
     */
    protected $description;

	public static function create()
	{
		return new static();
	}

	/**
	 * @return DeclaredValue
	 */
	public function declaredValue()
	{
		return $this->declared_value;
	}

	/**
	 * @param DeclaredValue $declared_value
	 */
	public function setDeclaredValue($declared_value)
	{
		$this->declared_value = $declared_value;
		return $this;
	}

	/**
	 * Provides the height attribute.
	 */
	public function height() {
		return $this->height;
	}

	/**
	 * Provides the length attribute.
	 */
	public function length() {
		return $this->length;
	}

	/**
	 * Setter for the height attribute.
	 * 
	 * @param int $value
	 */
	public function setHeight($value) {
		$this->height = $value;
		return $this;
	}

	/**
	 * Setter for the length attribute.
	 * 
	 * @param int $value
	 */
	public function setLength($value) {
		$this->length = $value;
		return $this;
	}

	/**
	 * Setter for the type attribute.
	 * defines packages of being of a certain type - if no value is given, parcel will be used
	 * 
	 * @param string $value
	 */
	public function setType($value) {
		$this->type = $value;
		return $this;
	}
	public function type() {
		return $this->type;
	}

	/**
	 * Setter for the weight attribute.
	 * 
	 * @param int $value
	 */
	public function setWeight($value) {
		$this->weight = $value;
		return $this;
	}

	/**
	 * Setter for the width attribute.
	 * 
	 * @param int $value
	 */
	public function setWidth($value) {
		$this->width = $value;
		return $this;
	}

	/**
	 * Provides the weight attribute.
	 */
	public function weight() {
		return $this->weight;
	}

	/**
	 * Provides the width attribute.
	 */
	public function width() {
		return $this->width;
	}

    /**
     * Setter for the description attribute.
     *
     * @param int $value
     */
    public function setDescription($value) {
        $this->description = $value;
        return $this;
    }

    /**
     * Provides the description attribute.
     */
    public function description() {
        return $this->description;
    }
}