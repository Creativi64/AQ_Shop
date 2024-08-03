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

class FormFilter{
    
    public static $dropdownUrl = "../xtAdmin/DropdownData.php";
    public static $iniValue = array(); // static cache


    /**
     * @param PhpExt_BoxComponent $f1
     * @param PhpExt_BoxComponent $f2
     * @return PhpExt_Panel
     * @throws Exception
     */
    protected static function twoCol($f1, $f2){
        
        $columnPanel = new PhpExt_Panel();
        $columnPanel->setLayout(new PhpExt_Layout_ColumnLayout())->setWidth("268px")->setBorder(false);
        $firstColumn = new PhpExt_Panel();
        $firstColumn->setLayout(new PhpExt_Layout_FormLayout())->setBorder(false);
        $firstColumn->addItem(
	            $f1 ->setWidth("65px")
	          );
        
        $secondColumn = new PhpExt_Panel();
        $secondColumn->setLayout(new PhpExt_Layout_FormLayout())->setBorder(false);
        $secondColumn->addItem(
	            $f2->setWidth("68px")->setHideLabel(true)
	          );
        $columnPanel->addItem($firstColumn, new PhpExt_Layout_ColumnLayoutData(0.70));
        $columnPanel->addItem($secondColumn, new PhpExt_Layout_ColumnLayoutData(0.30));
        
        return $columnPanel;
    }

    /**
     * @param PhpExt_BoxComponent $field
     * @param string $width
     * @return mixed
     */
    protected static function setWidth($field, $width="auto"){
        
       $field->setCssStyle(new PhpExt_Config_ConfigObject(array("width"=>$width))); 
       return $field;
    }

    /**
     * @param string $field field in $_SESSION['filters_'.$type]
     * @param string $type  filter type, eg order
     * @return bool
     * @todo remove all occurencies, change them to setText/iss
     */
    public static function setTxt_XT5($field, $type)
    {
        $ses_value = $_SESSION['filters_'.$type][$field] ?? null;
        return self::isSetTxtValue($ses_value);
    }

    /**
     * @param string $field
     * @return bool
     */
    public static function setTxt($field)
    {
        $ses_value = $_SESSION[$field];
        return self::isSetTxtValue($ses_value);
    }

    protected static function isSetTxtValue($value)
    {
        /** @var $rc ADORecordSet */
        global $db;

        if(trim($value) == "") return false;

        if(!sizeof(self::$iniValue)){
            $sql = "select language_value from ".TABLE_LANGUAGE_CONTENT." where  language_key in ('TEXT_INCLUDES','TEXT_TO', 'TEXT_FROM')";
            $rc = $db->Execute($sql);
            while(!$rc->EOF){
                self::$iniValue[] = $rc->fields['language_value'];
                $rc->MoveNext();

            } $rc->Close();
        }

        if(in_array($value,self::$iniValue )) return false;

        else return true;
    }

    /**
     * @param string $date  the date string to be transformed
     * @param string $newDelimeter the string to be used as delimeter in return
     * @param bool $guessInputFormat  if true, tries to guess format of input $date by testing exploded date for strlen of [0]
     * if array in form of eg ['Y' => 0, 'm' => 1, 'd' => 2]
     * @todo check all xt usages of function, inform devs
     * @return string
     */
    public static function date_trans($date, $guessInputFormat = true, $newDelimeter = '-'){

        // set defaults
        $delimeter = '/';

        if(strpos($date, '-'))
            $delimeter = '-';
        else if(strpos($date, '.'))
            $delimeter = '.';

        $d = explode($delimeter, $date);
        $d = array_map('trim', $d);

        if(is_array($guessInputFormat))
            $inputFormat = $guessInputFormat;
        else {
            if(strlen($d[0]) == 4)
                $inputFormat = ['Y' => 0, 'm' => 1, 'd' => 2];
            else
                $inputFormat = ['Y' => 2, 'm' => 1, 'd' => 0];
        }
        // other formats are not reasonable ?!  12/18/12 01/2020/01 no way, no clue, no guess

        return  $d[$inputFormat['Y']] .$newDelimeter. $d[$inputFormat['m']]. $newDelimeter. $d[$inputFormat['d']];
    }

    /**
     * @param $date
     * @return string
     * @deprecated use data_trans
     */
	public static function date_trans_int($date)
    {
        return self::date_trans($date, true, '.');
    }
   
}
