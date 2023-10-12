<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class KlarnaApiException extends Exception
{
    /** @var $data array */
    protected $data;

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}