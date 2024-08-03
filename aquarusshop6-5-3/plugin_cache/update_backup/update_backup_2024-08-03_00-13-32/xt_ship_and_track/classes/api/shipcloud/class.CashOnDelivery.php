<?php 

namespace Shipcloud;


class CashOnDelivery extends AdditionalService
{
	protected $name = "cash_on_delivery";


	/**
	 * @return mixed
	 */
	public function reference1()
	{
		return $this->properties['reference1'];
	}

	/**
	 * @param mixed $reference1
	 */
	public function setReference1($reference1)
	{
		$this->setProp('reference1', $reference1);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function bankCode()
	{
		return $this->properties['bank_code'];
	}

	/**
	 * @param mixed $bank_code
	 */
	public function setBankCode($bank_code)
	{
		$this->setProp('bank_code', $bank_code);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function bankAccountNumber()
	{
		return $this->properties['bank_account_number'];
	}

	/**
	 * @param mixed $bank_account_number
	 */
	public function setBankAccountNumber($bank_account_number)
	{
		$this->setProp('bank_account_number', $bank_account_number);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function bankName()
	{
		return $this->properties['bank_name'];
	}

	/**
	 * @param mixed $bank_name
	 */
	public function setBankName($bank_name)
	{
		$this->setProp('bank_name', $bank_name);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function bankAccountHolder()
	{
		return $this->properties['bank_account_holder'];
	}

	/**
	 * @param mixed $bank_account_holder
	 */
	public function setBankAccountHolder($bank_account_holder)
	{
		$this->setProp('bank_account_holder', $bank_account_holder);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function currency()
	{
		return $this->properties['currency'];
	}

	/**
	 * @param mixed $currency
	 */
	public function setCurrency($currency)
	{
		$this->setProp('currency', $currency);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function amount()
	{
		return $this->proprties['amount'];
	}

	/**
	 * @param mixed $amount
	 */
	public function setAmount($amount)
	{
		$this->setProp('amount', $amount);
		return $this;
	}

}