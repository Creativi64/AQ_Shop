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


class multistore_options {

    /**
     *    framework impl
     */
    private $_master_key = 'id';

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();

        $params = array();
        $params['header'] = $header;

        return $params;
    }

    function _get()
    {
    }

    function _unset($data)
    {
    }

    /**
     *    admin frontend functions
     */

    public function disableAllCategoriesForShop($data)
    {
        $expl = explode('_', $data['shop_id']);
        $shopId = intval($expl[1]);
        if ($shopId)
        {
            global $db;
            $sql = "SELECT `categories_id` FROM ". TABLE_CATEGORIES ;
            $catIdsRs = $db->Execute($sql);
            while (!$catIdsRs->EOF)
            {
                $catId = $catIdsRs->fields['categories_id'];
                $pgroup = "shop_$shopId";
                if (_SYSTEM_GROUP_PERMISSIONS=='blacklist')
                {
                    $sql = "DELETE FROM ".TABLE_CATEGORIES_PERMISSION. " WHERE `pid`=? AND `pgroup`=?";
                    $db->Execute($sql, array($catId, $pgroup));
                    $sql = "INSERT INTO ".TABLE_CATEGORIES_PERMISSION. " (`pid`,`permission`,`pgroup`) VALUES ( ?, 1, ?)";
                    $db->Execute($sql, array($catId, $pgroup));
                }
                else {
                    $sql = "DELETE FROM ".TABLE_CATEGORIES_PERMISSION. " WHERE `pid`=? AND `pgroup`=?";
                    $db->Execute($sql, array($catId, $pgroup));
                }
                $catIdsRs->MoveNext();
            }
            $catIdsRs->Close();
        }
        return true;
    }

    public function enableAllCategoriesForShop($data)
    {
        $expl = explode('_', $data['shop_id']);
        $shopId = intval($expl[1]);
        if ($shopId)
        {
            global $db;
            $sql = "SELECT `categories_id` FROM ". TABLE_CATEGORIES ;
            $catIdsRs = $db->Execute($sql);
            while (!$catIdsRs->EOF)
            {
                $catId = $catIdsRs->fields['categories_id'];
                $pgroup = "shop_$shopId";
                if (_SYSTEM_GROUP_PERMISSIONS=='blacklist')
                {
                    $sql = "DELETE FROM ".TABLE_CATEGORIES_PERMISSION. " WHERE `pid`=? AND `pgroup`=?";
                }
                else {
                    $sql = "REPLACE INTO ".TABLE_CATEGORIES_PERMISSION. " (`pid`,`permission`,`pgroup`) VALUES (?, 1, ?)";
                }
                $db->Execute($sql, array($catId, $pgroup));
                $catIdsRs->MoveNext();
            }
            $catIdsRs->Close();
        }
        return true;
    }

    public function disableAllProductsForShop($data)
    {
        $expl = explode('_', $data['shop_id']);
        $shopId = intval($expl[1]);
        if ($shopId)
        {
            global $db;
            $sql = "SELECT `products_id` FROM ". TABLE_PRODUCTS ;
            $rs = $db->Execute($sql);
            while (!$rs->EOF)
            {
                $pId = $rs->fields['products_id'];
                $pgroup = "shop_$shopId";
                if (_SYSTEM_GROUP_PERMISSIONS=='blacklist')
                {
                    $sql = "DELETE FROM ".TABLE_PRODUCTS_PERMISSION. " WHERE `pid`=? AND `pgroup`=?";
                    $db->Execute($sql, array($pId, $pgroup));
                    $sql = "INSERT INTO ".TABLE_PRODUCTS_PERMISSION. " (`pid`,`permission`,`pgroup`) VALUES ( ?, 1, ?)";
                    $db->Execute($sql, array($pId, $pgroup));
                }
                else {
                    $sql = "DELETE FROM ".TABLE_PRODUCTS_PERMISSION. " WHERE `pid`=? AND `pgroup`=?";
                    $db->Execute($sql, array($pId, $pgroup));
                }
                $rs->MoveNext();
            }
            $rs->Close();
        }
        return true;
    }

    public function enableAllProductsForShop($data)
    {
        $expl = explode('_', $data['shop_id']);
        $shopId = intval($expl[1]);
        if ($shopId)
        {
            global $db;
            $sql = "SELECT `products_id` FROM ". TABLE_PRODUCTS ;
            $rs = $db->Execute($sql);
            while (!$rs->EOF)
            {
                $pId = $rs->fields['products_id'];
                $pgroup = "shop_$shopId";
                if (_SYSTEM_GROUP_PERMISSIONS=='blacklist')
                {
                    $sql = "DELETE FROM ".TABLE_PRODUCTS_PERMISSION. " WHERE `pid`=? AND `pgroup`=?";
                }
                else{
                    $sql = "REPLACE INTO ".TABLE_PRODUCTS_PERMISSION. " (`pid`,`permission`,`pgroup`) VALUES (?, 1, ?)";
                }
                $db->Execute($sql, array($pId, $pgroup));
                $rs->MoveNext();
            }
            $rs->Close();
        }
        return true;
    }

    public function disableAllManufacturersForShop($data)
    {
        $expl = explode('_', $data['shop_id']);
        $shopId = intval($expl[1]);
        if ($shopId)
        {
            global $db;
            $sql = "SELECT `manufacturers_id` FROM ". TABLE_MANUFACTURERS ;
            $rs = $db->Execute($sql);
            while (!$rs->EOF)
            {
                $mId = $rs->fields['manufacturers_id'];
                $pgroup = "shop_$shopId";
                if (_SYSTEM_GROUP_PERMISSIONS=='blacklist')
                {
                    $sql = "DELETE FROM ".TABLE_MANUFACTURERS_PERMISSION. " WHERE `pid`=? AND `pgroup`=?";
                    $db->Execute($sql, array($mId, $pgroup));
                    $sql = "INSERT INTO ".TABLE_MANUFACTURERS_PERMISSION. " (`pid`,`permission`,`pgroup`) VALUES ( ?, 1, ?)";
                    $db->Execute($sql, array($mId, $pgroup));
                }
                else {
                    $sql = "DELETE FROM ".TABLE_MANUFACTURERS_PERMISSION. " WHERE `pid`=? AND `pgroup`=?";
                    $db->Execute($sql, array($mId, $pgroup));
                }
                $rs->MoveNext();
            }
            $rs->Close();
        }
        return true;
    }

    public function enableAllManufacturersForShop($data)
    {
        $expl = explode('_', $data['shop_id']);
        $shopId = intval($expl[1]);
        if ($shopId)
        {
            global $db;
            $sql = "SELECT `manufacturers_id` FROM ". TABLE_MANUFACTURERS ;
            $rs = $db->Execute($sql);
            while (!$rs->EOF)
            {
                $mId = $rs->fields['manufacturers_id'];
                $pgroup = "shop_$shopId";
                if (_SYSTEM_GROUP_PERMISSIONS=='blacklist')
                {
                    $sql = "DELETE FROM ".TABLE_MANUFACTURERS_PERMISSION. " WHERE `pid`=? AND `pgroup`=?";
                }
                else {
                    $sql = "REPLACE INTO ".TABLE_MANUFACTURERS_PERMISSION. " (`pid`,`permission`,`pgroup`) VALUES (?, 1, ?)";
                }
                $db->Execute($sql, array($mId, $pgroup));
                $rs->MoveNext();
            }
            $rs->Close();
        }
        return true;
    }
} 