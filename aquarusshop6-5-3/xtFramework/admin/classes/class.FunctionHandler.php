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
/*


type = Tab | Grid | Accordion | new window | dropdown

grouping => by Lang | Price | Image whatever..

value = array ( name,
                url,
                params,



*/


////////////////////////////////////////////////////////////////////////////////
/* handle functions data */
class FunctionHandler {
    var $function_stack;
    var $js_stack;
    var $defaultPanel;
    var $defaultLangPanel;
    var $noMasterPanel = false;
    function __construct() {
        $this->function_stack = array();
        $this->js_stack = array();

    }

    function setNoMasterPanel ($panel) {
        $this->noMasterPanel = $panel;
    }
    function getNoMasterPanel () {
        $this->noMasterPanel = $panel;
    }

    function addDefaultPanel($panel) {
        $this->defaultPanel = $panel;
    }
    function getDefaultPanel() {
        return $this->defaultPanel;
    }
    function addDefaultLangPanel($panel) {
        $this->defaultLangPanel = $panel;
    }
    function getDefaultLangPanel() {
        return $this->defaultLangPanel;
    }

    function setStack($value, $type = 'Tab', $grouping = 0) {
        if (!$key = $this->checkStack($value, $type, $grouping)) {
            $this->function_stack[$type][$grouping][] = $value;
        } else {
            $this->function_stack[$type][$grouping][] = $value;
        }
    }

    function getStack($type = '', $grouping = '') {
        if ($type && $grouping)
        return $this->function_stack[$type][$grouping];
        if ($type)
        return $this->function_stack[$type];

        return $this->function_stack;
    }

    function checkStack($value, $type = 'Tab', $grouping = 0) {
        $stack = $this->function_stack[$type][$grouping];
        if (is_array($stack)) {
            foreach ($stack as $key => $val) {
                $diff = array_diff($stack[$key], $value);
                if (count($diff) == 0) {
                    return $key;
                }
            }
        }
        return false;
    }

    function _setJsStack ($value) {
           $this->js_stack[] = $value;
    }

    function _getJsStack () {
           return $this->js_stack;
    }

    // this types must be a class like  F_<typename> (for example F_Tab)
    function _getTypes () {
        $types = array('Tab', 'Accordion', 'Window', 'Grid');
        return $types;
    }

    function _getHandler () {
        $stack = $this->getStack();

        $types = $this->_getTypes();

        foreach ($types as $key => $type) {
            if (is_array($stack[$type])) {
                $current_grouping = '';
                foreach ($stack[$type] as $grouping => $values) {
                    if ($current_grouping !== $grouping) {
                        $current_grouping = $grouping;

                        $class_name = "F_".$type;
                        $f_all = new $class_name;
                        $f_all->settings();
                        $f_all->setData($stack[$type][$grouping]);
                        $f_all->setNoMasterPanel($this->getNoMasterPanel());
                        $f_all->addDefaultPanel($this->getDefaultPanel());
                        $f_all->addDefaultLangPanel($this->getDefaultLangPanel());
                        $this->_setJsStack($f_all->_get());
                    }
                }
            }
        }
    }
}
/* handle functions data */
////////////////////////////////////////////////////////////////////////////////



////////////////////////////////////////////////////////////////////////////////
/* prepare functions data */
class F_all extends FunctionHandler {
    var $data;
    function __construct() {

    }

    function paramsToArray($params = "") {
        $_params = preg_split('/&/', $params);
        foreach ($_params as $paramterm) {
            list($key,$val) = preg_split('/=/', $paramterm);
            $_new_params[$key] = $val;

        }
        return $_new_params;
    }

    function setData ($data) {
        $this->data = $data;
    }

    function getData () {
        return $this->data;
    }
    function _getTab($title, $url, $params = "") {
        if (!is_array($params))
        $params = $this->paramsToArray($params);

        $p = new PhpExt_Panel();
        $p->setTitle($title)
            ->setAutoHeight(true)
//            ->setHeight(300)
//        ->setCssStyle("padding: 5px;")
          ->setBodyStyle("padding: 10px")
          ->setLayout(new PhpExt_Layout_FitLayout())
        	 ->setAutoLoad(new PhpExt_AutoLoadConfigObject($url, $params))
        	 ->getAutoLoad()
    		      ->setScripts(true)
    		      ->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_GET);
        return $p;
    }
}
/* prepare functions data end */
////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////
/* tab groupungs */
class F_Tab extends F_all{
    function __construct() {

    }

    function settings() {
        // tabpanel settings
        $this->settings['TabPanel']['autoHeight'] = true;
        $this->settings['TabPanel']['bodyStyle'] = "margin:10px";
        $this->settings['TabPanel']['activeTab'] = 0;

        // panel settings for tab
        $this->settings['TPanel']['BodyStyle'] = "padding: 5px;";

    }



    function _get () {

         foreach ($this->getData () as $key => $val) {
            $tabPanel[] = $this->_getTab($val['name'], $val['url'], $val['params']);
         }
         return $tabPanel;
    }
}
/* tab groupungs end */
////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////
/* accordion groupungs */
class F_Accordion extends F_all{
    function __construct() {

    }

    function settings() {
        // tabpanel settings
        $this->settings['AccordionPanel']['autoHeight'] = true;
        $this->settings['AccordionPanel']['bodyStyle'] = "margin 20px 10px";
        $this->settings['AccordionPanel']['activeItem'] = 0;

        // panel settings for tab
        $this->settings['TPanel']['BodyStyle'] = "padding: 5px;";

    }


    function _get () {

        $accordion = new PhpExt_Layout_AccordionLayout();
        $accordion->setTitleCollapse(true)
        		  ->setAnimate(true);
        $accordionPanel = new PhpExt_Panel();
        $accordionPanel->setLayout($accordion)
            ->setActiveItem($this->settings['AccordionPanel']['activeItem'])
//            ->setContainerCssClass("x-form-element")

//        ->setCssStyle("")
//          ->setBodyStyle("margin 5px; padding:10px")


            ->setHeight(250)

            ->setAutoHeight(true)
//                   ->setAutoShow(true)
                   ;


        $accordionPanel->setActiveItem($this->settings['AccordionPanel']['activeItem'])
                 ->setDefaults(new PhpExt_Config_ConfigObject(array("autoHeight"=>$this->settings['AccordionPanel']['autoHeight'],
                                                                    "bodyStyle"=>$this->settings['AccordionPanel']['bodyStyle'])
                                                              ));
        if ($default = $this->getDefaultPanel())
        $accordionPanel->addItem($default);
        if ($defaultLang = $this->getDefaultLangPanel())
        $accordionPanel->addItem($defaultLang);
         foreach ($this->getData () as $key => $val) {
            $accordionPanel->addItem($this->_getTab($val['name'], $val['url'], $val['params']));
         }
         return $accordionPanel;
    }
}
/* accordion groupungs end */
////////////////////////////////////////////////////////////////////////////////

?>