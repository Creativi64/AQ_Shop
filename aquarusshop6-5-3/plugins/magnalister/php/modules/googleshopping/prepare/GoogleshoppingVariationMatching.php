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

defined('TABLE_MAGNA_GOOGLESHOPPING_VARIANTMATCHING') or define('TABLE_MAGNA_GOOGLESHOPPING_VARIANTMATCHING', 'magnalister_googleshopping_variantmatching');

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/prepare/VariationMatching.php');
require_once(DIR_MAGNALISTER_MODULES.'googleshopping/GoogleshoppingHelper.php');
require_once(DIR_MAGNALISTER_MODULES.'googleshopping/prepare/GoogleshoppingCategoryMatching.php');

class GoogleshoppingVariationMatching extends VariationMatching {
    protected function getAttributesMatchingHelper() {
        return GoogleshoppingHelper::gi();
    }

    protected function getCategoryMatchingHandler() {
        return new GoogleshoppingCategoryMatching();
    }

    protected function renderCategoryOptions($sType, $sCategory) {
        return '';
    }

    public function renderAjax() {
        if (    isset($_GET['where']) && ($_GET['where'] == 'prepareView')
            && isset($_GET['view'])  && ($_GET['view'] == 'varmatch')) {
            $this->oCategoryMatching = $this->getCategoryMatchingHandler();
            echo $this->oCategoryMatching->renderAjax();
        } else {
            parent::renderAjax();
        }
    }

    protected function renderJs()
    {
        ob_start();
        ?>
        <script type="text/javascript" src="js/variation_matching2.js?<?php echo CLIENT_BUILD_VERSION?>"></script>
        <script type="text/javascript" src="js/marketplaces/<?php echo $this->marketplace?>/variation_matching.js?<?php echo CLIENT_BUILD_VERSION?>"></script>
        <script>
            var ml_vm_config = {
                url: '<?php echo toURL($this->resources['url'], array('where' => 'prepareView', 'kind' => 'ajax'), true);?>',
                viewName: 'varmatchView',
                formName: '#matchingForm',
                handleCategoryChange: <?php echo $this->getCategoryMatchingHandler() !== null ? 'true' : 'false'?>,
                i18n: <?php echo json_encode($this->getAttributesMatchingHelper()->getVarMatchTranslations());?>,
                shopVariations: <?php echo json_encode($this->getAttributesMatchingHelper()->getShopVariations()); ?>
            };
        </script>
        <?php
        return ob_get_clean();
    }
}
