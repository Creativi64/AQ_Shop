<?php 

namespace Shipcloud;

class AdvanceNoticeEmail extends AdvanceNotice
{

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->properties['email'];
	}

	/**
	 * @param mixed $sms
	 */
	public function setEmail($email)
	{
		return $this->setProp('email', $email);
	}


	/**
	 * @return mixed
	 */
	public function getLang()
	{
		return $this->properties['language'];
	}

	/**
	 * @param mixed $sms
	 */
	public function setLang($lang)
	{
		return $this->setProp('language', $lang);
	}

}