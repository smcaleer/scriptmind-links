<?php
/**
# ######################################################################
# Project:     ScriptMind::Plugins: Version 0.0.1
# **********************************************************************
# Copyright (C) 2013 Bruce Clement. (http://www.clement.co.nz/)
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public License
# as published by the Free Software Foundation; either version 3
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
# **********************************************************************
#
# For questions, help, comments, discussion, etc., please join the
# ScriptMind::Links Forum
#
# @link           http://www.scriptmind.org/
# @copyright      2013 Bruce Clement. (http://www.clement.co.nz/)
# @license http://URL LGPLv3 or later
# @projectManager Bruce Clement
# @package        ScriptMind::Plugins
# ######################################################################
*/

require_once __DIR__.'/plugins.inc';

/**
 * Base class for filters run as SmartyValidate criteria
 *
 * @author Bruce Clement
 */
abstract class SmartyPluginValidator extends Plugin {
    public $ErrorMessage;
    public function __construct( $anchor ) {
        parent::__construct( $anchor );
        $this->ErrorMessage='';
    }
    public function enumerate_options() {
        return array_merge( parent::enumerate_options(),
                            array( array( 'ErrorMessage', 'Error Message',
                                          $this->ErrorMessage, 'Text',
                                          "Message to display on error. Blank gets a generated message") ) );
    }
    public function SetError( PluginFilterParameters $args, $defaultMessage, $stop=false) {
        $args->error = true;
        if( $this->ErrorMessage=='')
            $args->errorMessage.=$defaultMessage.'. ';
        else
            $args->errorMessage.=$this->ErrorMessage.'. ';
        if( $stop )
            $args->stop = true;
    }
}
/**
 * Interface to allow plugin filters to be run as validators from Smarty template
 * engine on known fields.
 * The filters to be called should have registered themselves as either
 * 'SmartyValidateByFieldXXX' where XXX is the form field name or
 * 'SmartyValidateAll'
 */

function SmartyValidateByPlugin($value, $empty, &$params, &$form)
{
    /* @var $ans PluginFilterParameters */
    $ans = Plugin::runFilters('SmartyValidateByField'.$params['field'], $value, $empty, $params, $form);
    if( ! $ans->stop ) {
        $ans->callersArgs[0]='SmartyValidateAny';
        Plugin::runFiltersInternal('SmartyValidateAny', $ans);
    }
    if( $ans->error ) {
        if( strlen( $ans->errorMessage ) > 0 ) {
            if( !array_key_exists('originalMessage', $params) ) $params['originalMessage'] = $params['message'];
            $params['message'] = str_replace( 'ERROR', $ans->errorMessage, $params['originalMessage'] );
        }
        return 0;
    }
    return 1;
}