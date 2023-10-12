<?php
if ($this->_FieldExists('dsgvo_shipping_status',TABLE_SHIPPING))
    $db->Execute("ALTER TABLE ".TABLE_SHIPPING." DROP `dsgvo_shipping_status`");

if ($this->_FieldExists('dsgvo_shipping_required_status',TABLE_SHIPPING))
    $db->Execute("ALTER TABLE ".TABLE_SHIPPING." DROP `dsgvo_shipping_required_status`");

if ($this->_FieldExists('dsgvo_shipping_optin',TABLE_ORDERS))
    $db->Execute("ALTER TABLE ".TABLE_ORDERS." DROP `dsgvo_shipping_optin`");

