<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Modifies all product data
 */

use ew_adventury\plugin as ew_adventury_plugin;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    // manufacturer information
    if (isset($_GET['page']) && $_GET['page'] == 'product' && isset($this->data['manufacturers_id'])) {
        if (($manufacturerId = (int)$this->data['manufacturers_id']) !== 0) {
            if (!isset($this->data['manufacturer'])) {
                global $manufacturer;
                $manufacturerData = $manufacturer->getManufacturerData($manufacturerId);

                if (is_array($manufacturerData) && $manufacturerData['manufacturers_status'] == 1) {
                    $this->data['manufacturer'] = array(
                        'manufacturers_name'        => $manufacturerData['manufacturers_name'],
                        'manufacturers_image'       => (isset($manufacturerData['manufacturers_image']) && !empty($manufacturerData['manufacturers_image'])) ? $manufacturerData['manufacturers_image'] : null,
                        'manufacturers_link'        => $xtLink->_link(array('page' => 'manufacturers', 'type' => 'manufacturer', 'name' => $manufacturerData['manufacturers_name'], 'id' => $manufacturerData['manufacturers_id'], 'seo_url' => $manufacturerData['url_text'])),
                        'manufacturers_description' => (isset($manufacturerData['manufacturers_description']) && trim(strip_tags($manufacturerData['manufacturers_description'])) != '') ? $manufacturerData['manufacturers_description'] : null,
                    );
                }
            }

            // fallback for old evelations code
            if (isset($this->data['manufacturer']) && is_array($this->data['manufacturer'])) {
                $this->data['this_manufacturer'] = array_merge(
                    $this->data['manufacturer'],
                    array(
                        'name'        => isset($this->data['manufacturer']['manufacturers_name']) ? $this->data['manufacturer']['manufacturers_name'] : null,
                        'image'       => isset($this->data['manufacturer']['manufacturers_image']) ? $this->data['manufacturer']['manufacturers_image'] : null,
                        'link'        => isset($this->data['manufacturer']['manufacturers_link']) ? $this->data['manufacturer']['manufacturers_link'] : null,
                        'description' => isset($this->data['manufacturer']['manufacturers_description']) ? $this->data['manufacturer']['manufacturers_description'] : null,
                    )
                );
            }
        }
    }

    // add main image to more images on product page
    if (isset($_GET['page']) && $_GET['page'] == 'product') {
        if (isset($this->data['more_images']) && !empty($this->data['more_images'])) {
            if (isset($this->data['products_image']) && !empty($this->data['products_image'])) {
                $this->data['more_images'] = array_merge(
                    array(
                        array(
                            'file' => $this->data['products_image'],
                            'data' => array(),
                        ),
                    ), $this->data['more_images']);
            }
        }
    }

}
