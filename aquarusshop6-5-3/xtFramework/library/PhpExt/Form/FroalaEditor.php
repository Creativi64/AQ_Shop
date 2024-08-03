<?php
/**
 * PHP-Ext Library
 * http://php-ext.googlecode.com
 * @author Sergei Walter <sergeiw[at]gmail[dot]com>
 * @copyright 2008 Sergei Walter
 * @license http://www.gnu.org/licenses/lgpl.html
 * @link http://php-ext.googlecode.com
 * 
 * Reference for Ext JS: http://extjs.com
 * 
 */

/**
 * @see PhpExt_Form_Field
 */
include_once 'PhpExt/Form/TextArea.php';

/**
 * Provides a lightweight HTML Editor component.
 * <p><b>Note: The focus/blur and validation marking functionality inherited from Ext.form.Field is NOT supported by this editor.</b></p>
 * <p>An Editor is a sensitive component that can't be used in all spots standard fields can be used. Putting an Editor within any element that has display set to 'none' can cause problems in Safari and Firefox due to their default iframe reloading bugs.</p>
 * @package PhpExt
 * @subpackage Form
 */
class PhpExt_Form_FroalaEditor extends PhpExt_Form_TextArea
{	

	// AutoCreate
    /**
     * A DomHelper element spec, or true for a default element spec (defaults to {tag: "textarea", style: "width:100px;height:60px;", autocomplete: "off"})
     * @param PhpExt_Config_ConfigObject $value 
     * @return PhpExt_Form_TextArea
     */
    public function setAutoCreate(PhpExt_Config_ConfigObject $value) {
    	return parent::setAutoCreate($value);
    }	
    /**
     * A DomHelper element spec, or true for a default element spec (defaults to {tag: "textarea", style: "width:100px;height:60px;", autocomplete: "off"}) 
     * @return PhpExt_Config_ConfigObject
     */
    public function getAutoCreate() {
    	return parent::getAutoCreate();
    }
    
    // GrowMax
    /**
     * The maximum height to allow when grow = true (defaults to 1000)
     * @param integer $value 
     * @return PhpExt_Form_TextArea
     */
    public function setGrowMax($value) {
    	return parent::setGrowMax($value);
    }	
    /**
     * The maximum height to allow when grow = true (defaults to 1000)
     * @return integer
     */
    public function getGrowMax() {
    	return parent::getGrowMax();
    }
    
    // GrowMin
    /**
     * The minimum height to allow when grow = true (defaults to 60)
     * @param integer $value 
     * @return PhpExt_Form_TextArea
     */
    public function setGrowMin($value) {
    	return parent::setGrowMin($value);
    }	
    /**
     * The minimum height to allow when grow = true (defaults to 60)
     * @return integer
     */
    public function getGrowMin() {
    	return parent::getGrowMin();
    }
    
    // PreventScrollbars
    /**
     * True to prevent scrollbars from appearing regardless of how much text is in the field (equivalent to setting overflow: hidden, defaults to false)
     * @param boolean $value 
     * @return PhpExt_Form_TextArea
     */
    public function setPreventScrollbars($value) {
    	$this->setExtConfigProperty("preventScrollbars", $value);
    	return $this;
    }	
    /**
     * True to prevent scrollbars from appearing regardless of how much text is in the field (equivalent to setting overflow: hidden, defaults to false)
     * @return boolean
     */
    public function getPreventScrollbars() {
    	return $this->getExtConfigProperty("preventScrollbars");
    }

	public function __construct() {
		parent::__construct();
		$this->setExtClassInfo("Ext.form.FroalaEditor","froalaeditor");
		
		$validProps = array(
		    "autoCreate",
		    "growMax",
		    "growMin",
		    "preventScrollbars"
		);
		$this->addValidConfigProperties($validProps);
	}	 

    /**
	 * Helper function to create an HtmlEditor.  Useful for quick adding it to a ComponentCollection
	 *
	 * @param string $name The field's HTML name attribute.
	 * @param string $label The label text to display next to this field (defaults to '')
	 * @param string $id The unique id of this component (defaults to an auto-assigned id).
	 * @return PhpExt_Form_HtmlEditor
	 */
	public static function createFroalaEditor($name, $label = null, $id = null) {
	    $c = new PhpExt_Form_FroalaEditor();
	    $c->setName($name);
	    if ($label !== null)
	      $c->setFieldLabel($label);
	    if ($id !== null)
	      $c->setId($id);
        return $c;
	}
	
}

