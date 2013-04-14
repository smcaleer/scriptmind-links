{*
# ######################################################################
# Project:     ScriptMind::Plugins: Version 0.0.1
# **********************************************************************
# Copyright (C) 2013 Bruce Clement. (http://www.clement.co.nz/)
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public License (LGPL)
# as published by the Free Software Foundation; either version 3
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received copies of the GNU General Public License and the
# GNU Lesser General Public License along with this program; if not, write to
# the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
# MA  02110-1301, USA.
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
# ###################################################################### *}
{* Error and confirmation messages *}
   {include file="../messages.tpl"}

{strip}
{assign var=opt_bool value=[1=>"{l}Yes{/l}",0=>"{l}No{/l}"]}

{if $posted}
   <div class="alert">
      {l}Settings updated.{/l}
   </div>
{/if}

<form method="post" action="">
<table border="0" class="formPage">
{assign var="categ" value="0"}
{include file="plugin_block.tpl" title="Active Plugins" plugins=$activePlugins canDelete=false}
{include file="plugin_block.tpl" title="Inactive Plugins" plugins=$inactivePlugins canDelete=true}

{if $availablePlugins}
{assign var=opt_bool value=[1=>"{l}Yes{/l}",0=>"{l}No{/l}"]}
<tr>
<td colspan="5">Available Plugins</td>
</tr>
{foreach from=$availablePlugins item=plugin}
    {assign var="name" value=$plugin->name()}
    {assign var="id" value="_"|cat:$plugin->name()}
    <tr>
        <td colspan="1">{$name}</td>
        <td colspan="3">{$plugin->describe()}</td>
        <td><input type="submit" name="Install" value="Install {$name}" class="btn" /></td>
    </tr>
{/foreach}
{/if}
   <tr>
      <td colspan="3">&nbsp;</td>
      <td><input type="submit" name="submit" value="Save" class="btn" /></td>
   </tr>
</table>
</form>
{/strip}