<?php 

namespace Shipcloud;

class AdvanceNoticeSms extends AdvanceNotice
{

	/**
	 * @return mixed
	 */
	public function getSms()
	{
		return $this->properties['sms'];
	}

	/**
	 * @param mixed $sms
	 */
	public function setSms($sms)
	{
		return $this->setProp('sms', $sms);
	}

}