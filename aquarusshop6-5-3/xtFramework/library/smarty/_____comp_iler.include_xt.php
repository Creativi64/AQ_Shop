<?php

use Smarty\Compile\Tag\IncludeTag;
use Smarty\Smarty;

defined('_VALID_CALL') or die('Direct Access is not allowed.');

function smarty_compiler_include_xt($params, Smarty $smarty)
{

    $tag = new IncludeTag();

    //return $tag->compile($params, $smarty->getSmarty())

    $args = [];
    foreach ($params as $k => $v)
    {
        $args[] = [$k => $v];
    }



    $tpl = new \Smarty\Compiler\Template($smarty);

    $tpl->setTemplate($smarty->get);

    ;

    return $tag->compile($args, $tpl);


    return "<?php\necho '" . $smarty->_current_file . " compiled at " . date('Y-m-d H:M'). "';\n?>";
}