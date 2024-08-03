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

defined('_VALID_CALL') or die('Direct Access is not allowed.');


class xt_backend_cls
{
    public $position = null;
    public $url_data = [];
    public $sql_limit;
    /**
     * @var ExtAdminHandler
     */
    public $_AdminHandler;
    public $sql_search;

    public $perm_array = [];

    public item_permission $permission;

    function __construct()
    {
        $this->permission = new item_permission();
    }

    /**
     * default setPosition function
     *
     * @param string $position
     */
    public function setPosition ($position) {
        $this->position = $position;
    }

    /**
     * @return array
     */
    public function _getParams (){
        return ['header' => [], 'params' => []];
    }

    /**
     * @param $data
     * @param string $set_type
     * @return stdClass
     */
    public function _set ($data, $set_type = 'edit'){
        $obj = new stdClass;
        $obj->success = true;
        return $obj;
    }

    /**
     * @param int $id
     * @return stdClass
     */
    public function _get($id = 0){
        return new stdClass();
    }
}
