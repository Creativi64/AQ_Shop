<?php 

namespace Shipcloud;


class DropAuthorization extends AdditionalService
{
	protected $name = "drop_authorization";


	/**
	 * @return mixed
	 */
	public function message()
	{
		return $this->properties['message'];
	}

	/**
	 * @param mixed $reference1
	 */
	public function setMessage($message)
	{
		$this->setProp('message', $message);
		return $this;
	}


}