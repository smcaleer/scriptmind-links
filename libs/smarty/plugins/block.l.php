<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {l}{/l} block plugin
 *
 * Type:     block function<br>
 * Name:     l<br>
 * Purpose:  dummy to allow temporary removal of intSmarty
 * @author Bruce Clement
 * @param array
 * @param string contents of the block
 * @param Smarty clever simulation of a method
 * @return string string $content re-formatted
 */
// $params, $content, Smarty_Internal_Template $template, &$repeat ??
function smarty_block_l($params, $content, &$smarty)
{
    if (is_null($content)) {
        return;
    }

    return $content;

}

/* vim: set expandtab: */

?>
