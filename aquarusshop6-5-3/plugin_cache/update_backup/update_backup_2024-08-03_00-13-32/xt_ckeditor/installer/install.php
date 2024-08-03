<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$sql = "UPDATE " . TABLE_CONFIGURATION . " SET `sort_order`=7 WHERE `config_key`= '_SYSTEM_USE_WYSIWYG'";
$db->Execute($sql);

$sql = "INSERT INTO " . TABLE_CONFIGURATION . " " .
    " (`config_key`, `config_value`, `group_id`, `sort_order`, `type`)" .
    " VALUES ('_SYSTEM_CKEDITOR_CONFIG', '', 25, 8, 'hidden' )";
$db->Execute($sql);


if (version_compare(_SYSTEM_VERSION, '5.1.4', '>='))
{
    echo "
<script>
function enablePlugin()
{

                var conn = new Ext.data.Connection();
                conn.request({
                    url: 'adminHandler.php',
                    method:'GET',
                    params: {
                        pg:             'overview',
                        load_section:   'plugin_installed',
                        sec:         '"._SYSTEM_SECURITY_KEY."',
                        multiFlag_setStatus: 'true',
                        m_ids: '{$product_id}'
                    },
                    success: function(responseObject)
                    {
                        location.reload();;
                    },
                    failure: function(responseObject)
                    {
                        var title = responseObject.statusText ? 'Error '+responseObject.status : 'Error ';
                        var msg = 'Fehler beim aktivieren des Plugins. ' + responseObject.statusText ? responseObject.statusText : 'No Details available';
                        Ext.MessageBox.alert(title,msg);
                    }
                });
}
</script>
<div style=' padding:10px;'>
            <input type='button' onclick='enablePlugin();this.disabled=\"disabled\"; ' value='Plugin aktivieren und Seite neuladen'/>
        </div>

        ";
}