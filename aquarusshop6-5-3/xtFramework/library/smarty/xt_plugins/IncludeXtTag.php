<?php
/**
 * Smarty Internal Plugin Compile Include
 * Compiles the {include} tag
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
use Smarty\Compiler\Template;
use Smarty\Data;
use Smarty\Smarty;
use Smarty\Template\Compiled;

/**
 * Smarty Internal Plugin Compile Include Class
 *
       TEST TAG ÜBERSCHREIBUNG
       WIRD zZ NICHT VERWENDET
 */
class IncludeXtTag extends IncludeTag {

	/**
	 * Compiles code for the {include} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param Template $compiler compiler object
	 *
	 * @return string
	 * @throws \Exception
	 * @throws \Smarty\CompilerException
	 * @throws \Smarty\Exception
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		$uid = $t_hash = null;
		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);

        $_file = str_replace('"', '', $_attr[ 'file' ]);

        if(file_exists(constant('_SRV_WEBROOT')._SRV_WEB_TEMPLATES.constant('_STORE_TEMPLATE').'/'.$_file))
        {
            $_attr[ 'file' ] = '"'.constant('_SRV_WEBROOT')._SRV_WEB_TEMPLATES.constant('_STORE_TEMPLATE').'".'.$_attr[ 'file' ];
        }
        else if(file_exists(constant('_SRV_WEBROOT')._SRV_WEB_TEMPLATES.constant('_SYSTEM_TEMPLATE').'/.'.$_file))
        {
            $_attr[ 'file' ] = '"'.constant('_SRV_WEBROOT')._SRV_WEB_TEMPLATES.constant('_SYSTEM_TEMPLATE').'".'.$_attr[ 'file' ];
        }

        return parent::compile($args, $compiler, $parameter, $tag, $function);
	}

}
