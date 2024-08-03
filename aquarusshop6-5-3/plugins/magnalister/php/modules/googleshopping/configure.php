<?php
/**
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
 * (c) 2010 - 2019 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/configure.php');

class GoogleshoppingConfigure extends MagnaCompatibleConfigure {

    /**
     * Checks if customer is authenticated on googleshopping and pops up the oauth window if not
     */
    protected function processAuth() {
        $auth = getDBConfigValue($this->marketplace.'.authed', $this->mpID, false);
        $missingKeys = array();
        if ((!is_array($auth) || !$auth['state'])
            && allRequiredConfigKeysAvailable($this->authConfigKeys, $this->mpID, false, $missingKeys)
            && !(
                array_key_exists('conf', $_POST)
                && allRequiredConfigKeysAvailable($this->authConfigKeys, $this->mpID, $_POST['conf'])
            )
        ) {
            $this->boxes .= $this->renderAuthError();
        }
        if (!array_key_exists('conf', $_POST)) {
            $this->isAuthed = is_array($auth) && isset($auth['state']) && $auth['state'];
            return;
        }

	    if ($this->shouldForceTokenRegeneration()) {
            try {
                $r = MagnaConnector::gi()->submitRequest(array(
                    'ACTION' => 'GetTokenCreationLink',
                ));

                $link = $r['DATA']['tokenCreationLink'];
                echo "<script>window.open('".$link."');</script>";
                return;
            } catch (MagnaException $e) {
                $e->setCriticalStatus(false);
                setDBConfigValue($this->marketplace.'.autherror', $this->mpID, $e->getErrorArray(), false);
                $this->boxes .= $this->renderAuthError();
                return;
            }
        }
		try {
            $r = MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'IsAuthed',
            ));
		} catch (MagnaException $e) {
			$e->setCriticalStatus(false);
			setDBConfigValue($this->marketplace.'.autherror', $this->mpID, $e->getErrorArray(), false);
			$this->boxes .= $this->renderAuthError();
			return;
		}
    }

    protected function getFormFiles()
    {
        return array (
            'login', 'prepare', 'checkin', 'shipping',
            'price', 'inventorysync', 'orders',
            'setImagePath'
        );
    }


    protected function finalizeForm() {
        parent::finalizeForm();
        if (!$this->isAuthed) {
            $this->form = array(
                'login' => $this->form['login']
            );
            return;
        }

        if (empty($_POST['conf']['googleshopping.merchantid']) || !is_numeric($_POST['conf']['googleshopping.merchantid'])) {
            unset($_POST['conf']['googleshopping.merchantid']);
        }
    }

    protected function loadChoiseValuesAfterProcessPOST() {
        parent::loadChoiseValuesAfterProcessPOST();
        mlGetLanguages($this->form['prepare']['fields']['lang']['morefields']['webshop']);
        $this->mlGetSupportedLanguagesForTargetCountry($this->form['prepare']['fields']['lang']['morefields']['googleshopping']);
        $this->mlGetTargetCountries($this->form['login']['fields'][3]);
        $this->mlGetShopCurrencies($this->form['login']['fields'][4]);
        $this->mlGetShippingLabels($this->form['shipping']['fields']['shippinglabel']);
    }
    
    private function mlGetTargetCountries(&$form){
        global $magnaConfig;
        $countries =  $magnaConfig['googleshopping']['targetCountry'];
        $form['values'] = array();
        foreach ($countries as $key => $country) {
            $form['values'][$key] = $country;
        }
    }

    private function isPostRequest() {
        return !empty($_POST);
    }

    private function shouldForceTokenRegeneration() {
        return $this->isPostRequest() && isset($_POST['forceTokenRegeneration']);
    }

    public static function renderForceRegenerateTokenButton($params) {
        return '<button type="submit" name="forceTokenRegeneration" class="ml-button mlbtn-action">'.$params[0]['button'].'</button>';
    }

    /**
     *
     * @param $form
     */
    public function mlGetShopCurrencies(&$form) {
        $currencies = MagnaDB::gi()->fetchArray('SELECT * FROM ' . TABLE_CURRENCIES);
        $form['values'] = array();
        foreach ($currencies as $value) {
            $form['values'][$value['code']] = $value['title'];
        }
    }

    /**
     * On GoogleShopping configuration tab load
     *
     * @param $form
     */
    private function mlGetSupportedLanguagesForTargetCountry(&$form) {
        global $magnaConfig;
        $languages = $magnaConfig['googleshopping']['languages'];
        if ($selectedTargetCountry = getDBConfigValue($this->marketplace.'.targetCountry', $this->mpID, false)) {
            $targetCountryLanguages = $languages[$selectedTargetCountry];
            foreach ($targetCountryLanguages as $value) {
                $form['values'][$value['code']] = $value['title'] . " ({$value['code']}) ";
            }
        }
        if ($selectedTargetCountry !== 'UA') {
            $form['values'][$languages['GB'][0]['code']] = $languages['GB'][0]['title']." ({$languages['GB'][0]['code']}) ";
        }
    }

    private function mlGetShippingLabels(&$form) {
        if ($sSelectedTargetCountry = getDBConfigValue($this->marketplace . '.targetCountry', $this->mpID, false)) {
            $aShippingServices = GoogleshoppingApiConfigValues::gi()->getShippingLabels();
            $form['values']['no_label'] = ML_GOOGLESHOPPING_NO_SHIPPING_LABEL;
            foreach ((array)$aShippingServices['services'] as $aShippingService) {
                if ($aShippingService['deliveryCountry'] === $sSelectedTargetCountry) {
                    foreach ((array)$aShippingService['rateGroups'] as $aRateGroup) {
                        foreach ((array)$aRateGroup['applicableShippingLabels'] as $sLabel) {
                            $form['values'][$sLabel] = $sLabel;
                        }
                    }
                }
            }
        }
    }
}