<?php
/*
 * 888888ba                 dP  .88888.                    dP
 * 88    `8b                88 d8'   `88                   88
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b.
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P'
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * (c) 2010 - 2022 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/configure.php');

class CdiscountConfigure extends MagnaCompatibleConfigure {

    /** @var null|array $deliveryModes */
    private static $deliveryModes = null;

	protected function getAuthValuesFromPost() {
		$nMPUser = trim($_POST['conf'][$this->marketplace.'.mpusername']);
		$nMPPass = trim($_POST['conf'][$this->marketplace.'.mppassword']);
		$nMPPass = $this->processPasswordFromPost('mppassword', $nMPPass);
		
		if (empty($nMPUser)) {
			unset($_POST['conf'][$this->marketplace.'.mpusername']);
		}
		
		if ($nMPPass === false) {
			unset($_POST['conf'][$this->marketplace.'.mppassword']);
			return false;
		}
		
		$data = array (
			'MPUSERNAME' => $nMPUser,
			'MPPASSWORD' => $nMPPass,
		);
		#echo print_m($data);
		return $data;
	}
	
	protected function getFormFiles() {
		$forms = parent::getFormFiles();
		$forms[] = 'prepareadd';
		$forms[] = 'orderStatus';
		$forms[] = 'email_template_generic';
	
		return $forms;
	}

    public function process() {
        parent::process(); // TODO: Change the autogenerated stub
        if (!isset($_POST['kind']) || ($_POST['kind'] !== 'ajax' && $_POST['action'] !== 'duplicate')) {
            $this->carrierScript();
        }
        $this->selectFieldOptionRemoverScript();
    }

    /* returns a list with extra options (show as optgroups) */
    public function loadCarrierCodesExtended() {
        $carrierCodes = self::getDeliveryModes();
        //        array_shift($carrierCodes); // remove the 'none' entry)
        $carrierSelection = array(
            '' => ML_LABEL_CHOOSE,
            ML_SELECT_MARKETPLACE_SUGGESTED_CARRIER => $carrierCodes,
            ML_ADDITIONAL_OPTIONS => array(
                'shipmodulematch' => ML_MATCH_CDISCOUNT_CARRIER_TO_SHIPPING_MODULE,
                'dbmatch' => ML_MATCH_CARRIER_TO_DB,
            ),
        );

        return $carrierSelection;
    }

	protected function loadChoiseValues() {
		parent::loadChoiseValues();
		if ($this->isAuthed) {
			CdiscountHelper::GetConditionTypesConfig($this->form['prepare']['fields']['condition']);
			mlGetOrderStatus($this->form['orderSyncState']['fields']['cancelstatus']);
			mlGetOrderStatus($this->form['orderSyncState']['fields']['shippedstatus']);
            $this->form['orderSyncState']['fields']['send.carrier']['values'] = $this->loadCarrierCodesExtended();

			unset($this->form['checkin']['fields']['leadtimetoship']);
		}
	}
	
	protected function finalizeForm() {
		parent::finalizeForm();
		if (!$this->isAuthed) {
			$this->form = array (
				'login' => $this->form['login']
			);
			return;
		}
	}

	protected function loadChoiseValuesAfterProcessPOST() {
		if (!$this->isAuthed) {
			global $magnaConfig;

			unset($magnaConfig['db'][$this->mpID]['cdiscount.secretkey']);
			unset($magnaConfig['db'][$this->mpID]['cdiscount.mppassword']);
		}
	}

    /**
     * Get delivery modes from API (and cache it during runtime)
     *
     * @return array|mixed|string[]
     */
    public static function getDeliveryModes() {
        // use cached value
        if (self::$deliveryModes !== null) {
            return self::$deliveryModes;
        }

        try {
            $deliveryModesResponse = MagnaConnector::gi()->submitRequest(array('ACTION' => 'GetDeliveryModes'));
            $deliveryModes = isset($deliveryModesResponse['DATA']) ? $deliveryModesResponse['DATA'] : array();
        } catch (MagnaException $me) {
            $deliveryModes = array();
        }

        // set runtime cache
        self::$deliveryModes = $deliveryModes;

        return $deliveryModes;
    }


    public static function shippingProfile($args, &$value = '') {
        global $_MagnaSession;
        $sHtml = '<table><tr>';
        $form = array();
        $deliveryModes = self::getDeliveryModes();

        $cG = new MLConfigurator($form, $_MagnaSession['mpID'], 'conf_cdiscount');

        foreach ($args['subfields'] as $key => $item) {
            $idkey = str_replace('.', '_', $item['key']);
            $configValue = getDBConfigValue($item['key'], $_MagnaSession['mpID'], '');
            $value = '';

            if (isset($item['selectedValues'])) {
                $value = $item['selectedValues'][$args['currentIndex']];
            } elseif (isset($configValue[$args['currentIndex']])) {
                $value = $configValue[$args['currentIndex']];
            }

            $item['key'] .= '][';
            if (isset($item['params'])) {
                $item['params']['value'] = $value;
            }

            if ($key === 'name' || $key === 'method') {
                $item['values'] = $deliveryModes;
            }

            $sHtml .= '<td>'.$cG->renderLabel($item['label'], $idkey).':</td><td>'.$cG->renderInput($item, $value).'</td>';

        }
        $sHtml .= '</tr></table>';
        return $sHtml;
    }

    public static function CdiscountCarrierCdiscountToShopMatchConfig($args) {
        global $_MagnaSession;
        $sHtml = '<table><tr>';
        $form = array();

        $cG = new MLConfigurator($form, $_MagnaSession['mpID'], 'conf_cdiscount');
        foreach ($args['subfields'] as $item) {
            $idkey = str_replace('.', '_', $item['key']);
            $configValue = getDBConfigValue($item['key'], $_MagnaSession['mpID'], '');
            $value = '';
            if (isset($configValue[$args['currentIndex']])) {
                $value = $configValue[$args['currentIndex']];
            }
            $item['key'] .= '][';
            if (isset($item['params'])) {
                $item['params']['value'] = $value;
            }
            $sHtml .= '<td>'.$cG->renderInput($item, $value).'</td>';
        }
        $sHtml .= '</tr></table>';
        return $sHtml;
    }

    public static function CdiscountCarriersConfig($args) {
        global $_MagnaSession;
        $sHtml = '<select name="conf['.$args['key'].']">';
        $deliveryModes = self::getDeliveryModes();
        //        echo print_m($deliveryModes);
        foreach ($deliveryModes as $iso => $name) {
            $sHtml .= '<option '.($args['value'] == $name ? 'selected=selected' : '').' value="'.$name.'">'.fixHTMLUTF8Entities($name).'</option>';
        }
        $sHtml .= '</select>';
        return $sHtml;
    }

    public static function CdiscountShopCarriersConfig($args) {
        $aShopCarriers = array('values' => null);
        mlGetShippingModules($aShopCarriers); $aShopCarriers = $aShopCarriers['values'];
        if (empty($aShopCarriers)) $aShopCarriers = array('&mdash;');
        $sHtml = '<select name="conf['.$args['key'].']">';
        foreach ($aShopCarriers as $ckey => $name) {
            $sHtml .= '<option '.($args['value'] == $ckey ? 'selected=selected' : '').' value="'.$ckey.'">'.fixHTMLUTF8Entities($name).'</option>';
        }
        $sHtml .= '</select>';
        return $sHtml;
    }

    public function carrierScript() {
        ?>
        <script>
            $(document).ready(function () {
                    if ($('select[id="config_cdiscount_send_carrier"]').val() == 'dbmatch') {
                        $('#config_cdiscount_send_carrier_DBMatching_table_table').parents().eq(1).css('visibility', 'visible');
                        $('#cdiscount_send_carrier_cdiscountToShopMatching').parents().eq(1).css('visibility', 'collapse');
                    } else if ($('select[id="config_cdiscount_send_carrier"]').val() == 'shipmodulematch') {
                        $('#config_cdiscount_send_carrier_DBMatching_table_table').parents().eq(1).css('visibility', 'collapse');
                        $('#cdiscount_send_carrier_cdiscountToShopMatching').parents().eq(1).css('visibility', 'visible');
                    } else {
                        $('#config_cdiscount_send_carrier_DBMatching_table_table').parents().eq(1).css('visibility', 'collapse');
                        $('#cdiscount_send_carrier_cdiscountToShopMatching').parents().eq(1).css('visibility', 'collapse');
                    }
                }
            )
            // switch on/off carrier + shipping method extra fields
            $('select[id="config_cdiscount_send_carrier"]').change(function() {
                console.log($('select[id="config_cdiscount_send_carrier"]').val())
                if($('select[id="config_cdiscount_send_carrier"]').val() == 'dbmatch') {
                    $('#config_cdiscount_send_carrier_DBMatching_table_table').parents().eq(1).css('visibility', 'visible');
                    $('#cdiscount_send_carrier_cdiscountToShopMatching').parents().eq(1).css('visibility', 'collapse');
                } else if($('select[id="config_cdiscount_send_carrier"]').val() == 'shipmodulematch'){
                    $('#config_cdiscount_send_carrier_DBMatching_table_table').parents().eq(1).css('visibility', 'collapse');
                    $('#cdiscount_send_carrier_cdiscountToShopMatching').parents().eq(1).css('visibility', 'visible');
                } else {
                    $('#config_cdiscount_send_carrier_DBMatching_table_table').parents().eq(1).css('visibility', 'collapse');
                    $('#cdiscount_send_carrier_cdiscountToShopMatching').parents().eq(1).css('visibility', 'collapse');
                }
            });
        </script>
        <?php
    }
    public function selectFieldOptionRemoverScript() {
        ?>
        <script>
            $(document).ready(function() {
                let shippingSelector = "*#config_cdiscount_shippingprofile_name\\]\\[";

                const hideUsedDeliveryModes = function () {
                    let allSelectedOptions = $(shippingSelector).find('option:selected').toArray()
                    let selectedDeliveryModes = $.map(allSelectedOptions, function (option, i) {
                        return $(option).val()
                    });

                    // iterate over dropdowns
                    $(shippingSelector).each(function () {
                        // selected option from dropdown
                        let selectedOption = $(this).find('option:selected')[0]

                        // iterate over options of each dropdown
                        $(this).find('option').each(function(i, option) {
                            if (option === selectedOption) {
                                return true;
                            }
                            if (in_array($(option).val(), selectedDeliveryModes)){
                                $(option).hide();
                            } else {
                                $(option).show();
                            }
                        });
                    });
                };

                hideUsedDeliveryModes();
                $(document).on('click', '.ml-button.minus, .ml-button.plus', function(){
                    hideUsedDeliveryModes();
                });

                $('select[id="config_cdiscount_shippingprofile_name\\]\\["]').on('change', function () {
                    hideUsedDeliveryModes();
                });

            })
        </script>
        <?php
    }
}