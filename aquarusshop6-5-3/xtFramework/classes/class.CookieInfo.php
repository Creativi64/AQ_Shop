<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

use GuzzleHttp\Cookie\SetCookie;

defined('_VALID_CALL') or die('Direct Access is not allowed.');


class CookieInfo implements JsonSerializable
{
    /** @var string one of CookieType */
    protected $_type;

    /** @var string */
    protected $_issuer;

    /** @var string */
    protected $_info;

    /** @var string */
    protected $_info_url;

    /** @var SetCookie[] */
    protected $_cookies;

    public function __construct(string $type, string $issuer, SetCookie $cookie = null, $info = '', $info_url = '')
    {
        $this->setType($type)
            ->setIssuer($issuer)
            ->setInfo($info)
            ->setInfoUrl($info_url)
            ->addCookie($cookie);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * @param string $type
     * @return CookieInfo
     */
    public function setType(string $type)
    {
        if(!CookieType::valid($type))
            throw new InvalidArgumentException('['.$type.'] is not an accepted cookie type');
        $this->_type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getIssuer(): string
    {
        return empty($this->_issuer) ? '' : $this->_issuer;
    }

    /**
     * @param string $issuer
     * @return CookieInfo
     */
    public function setIssuer(string $issuer)
    {
        if(empty($issuer))
            throw new InvalidArgumentException('name of cookie issuer required');
        $this->_issuer = $issuer;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo(): string
    {
        return empty($this->_info) ? '' : $this->_info;
    }

    /**
     * @param string $info
     * @return CookieInfo
     */
    public function setInfo(string $info)
    {
        $this->_info = $info;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfoUrl(): string
    {
        return empty($this->_info_url) ? '' : $this->_info_url;
    }

    /**
     * @param string $info_url
     * @return CookieInfo
     */
    public function setInfoUrl(string $info_url)
    {
        $this->_info_url = $info_url;
        return $this;
    }

    /**
     * @return SetCookie[]
     */
    public function getCookies()
    {
        return $this->_cookies;
    }

    /**
     * @param SetCookie $cookie
     * @return CookieInfo
     */
    public function addCookie(SetCookie $cookie = null)
    {
        if($cookie)
            $this->_cookies[] = $cookie;
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $cookies = [];
        foreach($this->getCookies() as $c)
            $cookies[] = $c->toArray();

        return
            [
                'type'      => $this->getType(),
                'issuer'    => $this->getIssuer(),
                'info'      => $this->getInfo(),
                'infoUrl'   => $this->getInfoUrl(),
                'cookies'   => $cookies
            ];
    }
}