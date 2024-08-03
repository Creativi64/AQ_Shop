<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');


class shipcloud_carrier
{
    public $name = '';
    public $display_name = '';
    public $services = array();
    public $package_types = array();

    /**
     * @param array $services
     * @param array $package_types
     */
    public function __construct($services = array(), $package_types = array())
    {
        $this->services = $services;
        $this->package_types = $package_types;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * @param string $display_name
     */
    public function setDisplayName($display_name)
    {
        $this->display_name = $display_name;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param array $services
     */
    public function setServices($services)
    {
        $this->services = $services;
    }

    /**
     * @return array
     */
    public function getPackageTypes()
    {
        return $this->package_types;
    }

    /**
     * @param array $package_types
     */
    public function setPackage_Types($package_types)
    {
        $this->package_types = $package_types;
    }
}