<?php
/**
 * PHP-Ext Library
 * http://php-ext.googlecode.com
 * @author Matthias Benkwitz <mb[at]bui-hinsche[dot]de>
 * @copyright 2008 Matthias Benkwitz
 * @license http://www.gnu.org/licenses/lgpl.html
 * @link http://php-ext.googlecode.com
 *
 * Reference for Ext JS:
 * /**
 * Ext.ux.form.LovCombo, List of Values Combo
 *
 * @author    Ing. Jozef Sakáloš
 * @copyright (c) 2008, by Ing. Jozef Sakáloš
 * @date      16. April 2008
 * @version   $Id: Ext.ux.form.LovCombo.js 285 2008-06-06 09:22:20Z jozo $
 *
 * @license Ext.ux.form.LovCombo.js is licensed under the terms of the Open Source
 * LGPL 3.0 license. Commercial use is permitted to the extent that the
 * code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 *
 * License details: http://www.gnu.org/licenses/lgpl.html
 */

/**
 * @see PhpExt_Form_ComboBox
 */
include_once 'PhpExt/Form/ComboBox.php';

/**
 * @package PhpExt
 * @subpackage Form
 */
class PhpExt_Form_LovCombo2 extends PhpExt_Form_LovCombo
{

	public function __construct() {
		parent::__construct();
		$this->setExtClassInfo("Ext.ux.form.LovCombo2", "lovcombo2");
	}

    /**
	 * Helper function to create a ComboBox.  Useful for quick adding it to a ComponentCollection
	 *
	 * @param string $name The field's HTML name attribute.
	 * @param string $labelThe label text to display next to this field (defaults to '')
	 * @param string $id The unique id of this component (defaults to an auto-assigned id).
	 * @param string $hiddenName If specified, a hidden form field with this name is dynamically generated to store the field's data value (defaults to the underlying DOM element's name). Required for the combo's value to automatically post during a form submission.
	 * @return PhpExt_Form_ComboBox
	 */
	public static function createLovCombo2($name, $label = null, $id = null, $hiddenName = null) {
	    $c = new PhpExt_Form_LovCombo2();
	    $c->setName($name);
	    if ($label !== null)
	      $c->setFieldLabel($label);
	    if ($id !== null)
	      $c->setId($id);
	    if ($hiddenName !== null)
	      $c->setHiddenName($hiddenName);
	    return $c;
	}
}