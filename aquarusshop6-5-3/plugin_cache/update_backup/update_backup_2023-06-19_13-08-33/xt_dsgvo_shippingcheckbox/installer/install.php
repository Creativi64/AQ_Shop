<?php
if (!$this->_FieldExists('dsgvo_shipping_status',TABLE_SHIPPING))
    $db->Execute("ALTER TABLE ".TABLE_SHIPPING." ADD `dsgvo_shipping_status` INT( 1 ) NOT NULL DEFAULT '1';");

if (!$this->_FieldExists('dsgvo_shipping_required_status',TABLE_SHIPPING))
    $db->Execute("ALTER TABLE ".TABLE_SHIPPING." ADD `dsgvo_shipping_required_status` INT( 1 ) NOT NULL DEFAULT '0';");

if (!$this->_FieldExists('dsgvo_shipping_optin',TABLE_ORDERS))
    $db->Execute("ALTER TABLE ".TABLE_ORDERS." ADD `dsgvo_shipping_optin` INT( 1 ) NOT NULL DEFAULT '0';");
