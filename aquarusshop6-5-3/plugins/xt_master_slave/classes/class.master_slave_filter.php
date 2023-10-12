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

class master_slave_filter implements IFilter {

    const MASTER_SLAVE_FILTER_NAME = "master_slave_filter_";

    /**
     * Return available filters for this category that will be displayed.
     * The implementation is responsible to cache, order and return the results
     * in the following format (that filter controller can render):
     * array(
     *      [0] => class extending base_filter_field
     * )
     * @param $category_id
     * @return array
     */
    public function getFiltersForCategory($category_id)
    {
        global $db, $language;

        static $widgets = array();

        if (isset($widgets[$category_id])) {
            return $widgets[$category_id];
        } else {
            $widgets[$category_id] = array();
        }

        $query = "SELECT DISTINCT " .
                    "pa.attributes_id, pad.attributes_name, par.attributes_name as parent_name " .
                "FROM " .
                TABLE_PRODUCTS_ATTRIBUTES . " pa " .
                "INNER JOIN (" .
                    "SELECT attributes_id FROM " . TABLE_PRODUCTS_TO_ATTRIBUTES . " subpa " .
                        "INNER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON (ptc.categories_id='{$category_id}' " .
                        "AND subpa.products_id=ptc.products_id)" .
                ") cpa ON pa.attributes_id=cpa.attributes_id " .
                "LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION . " pad ON pad.attributes_id=pa.attributes_id AND ".
                "pad.language_code='{$language->code}' " .
                "LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION . " par ON (pa.attributes_parent=par.attributes_id) ".
                "AND par.language_code='{$language->code}'";

        $rs = $db->CacheExecute($query);
        $result = array();

        if ($rs->RecordCount() > 0) {
            while (!$rs->EOF) {
                if (!isset($result[$rs->fields['parent_name']])) {
                    $result[$rs->fields['parent_name']] = array();
                }
                $result[$rs->fields['parent_name']][$rs->fields['attributes_id']] = $rs->fields['attributes_name'];
                $rs->MoveNext();
            }
            $rs->Close();
        }

        $i = 0;
        foreach ($result as $group_name => $group_options) {
            ++$i;
            $widgets[$category_id][] = new checkbox_filter(self::MASTER_SLAVE_FILTER_NAME . $i, IFilterField::FILTER_TYPE_CHECKBOX, $group_name, $group_options);
        }

        return $widgets[$category_id];
    }

    /**
     * Add filter conditions to the query before it is executed
     * @param getProductSQL_query $query
     * @return mixed
     */
    public function filter(getProductSQL_query $query)
    {
        global $current_category_id;

        $attribute_conditions = array();
        $widgets = $this->getFiltersForCategory($current_category_id);

        foreach ($widgets as $widget) {
            if ($widget->hasFilter()) {
                $attribute_conditions = array_merge($attribute_conditions, $widget->getFilterValue());
            }
        }

        if (!empty($attribute_conditions)) {
            /* Very fast, but shows only slaves ....
            $query->setSQL_TABLE("INNER JOIN " . TABLE_PRODUCTS_TO_ATTRIBUTES . " pta ON p.products_id = pta.products_id AND pta.attributes_id IN (" . join(',', $attribute_conditions) . ")");
            $query->setSQL_GROUP("p.products_id");
            $query->setSQL_HAVING("COUNT(pta.products_id)='" . count($attribute_conditions) . "'");
            */

            // Slow ...
            $join = " LEFT JOIN " . TABLE_PRODUCTS . " slaves ON slaves.products_master_model=p.products_model ";
            $join .= " LEFT JOIN " . TABLE_PRODUCTS_TO_ATTRIBUTES . " spa ON slaves.products_id=spa.products_id AND spa.attributes_id IN (" . join(',', $attribute_conditions) . ")";
            $query->setSQL_HAVING("COUNT(DISTINCT spa.attributes_id)='" . count($attribute_conditions) . "'");
            $query->setSQL_TABLE($join);
            $query->setSQL_GROUP("p.products_id");
        }
    }


}