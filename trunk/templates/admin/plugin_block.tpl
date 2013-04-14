{if $plugins}
{assign var=opt_bool value=[1=>"{l}Yes{/l}",0=>"{l}No{/l}"]}
{if $canDelete}{assign var=descCols value=3}{else}{assign var=descCols value=4}{/if}
<tr>
<td colspan="5">{$title}</td>
</tr>
{foreach from=$plugins item=plugin}
    {assign var="name" value=$plugin->name()}
    {assign var="id" value="_"|cat:$plugin->ID}
    {if $id eq "_0"}{assign var="id" value="_"|cat:$plugin->name()}{/if}
    <tr>
        <td colspan="1">{$name}</td>
        <td colspan="{$descCols}">{$plugin->describe()}</td>
        {if $canDelete}<td><input type="submit" name="Delete{$id}" value="Delete" class="btn"/></td>{/if}
    </tr>
    {assign var="options" value=$plugin->enumerate_options()}
    {foreach from=$options item=option}
        {assign var="op_id" value=$option[0]|cat:$id}
        <tr>
            <td></td>
            <td>{$option[1]}</td>
            <td>{if $option[3] eq 'Bool' }{html_options options=$opt_bool selected=$option[2] name=$op_id}
            {elseif $option[3]|is_array}{html_options options=$option[3] selected=$option[2] name=$op_id}
            {else}<input name="{$op_id}" value="{$option[2]}">
            {/if}</td>
            <td colspan="2">{$option[4]}</td>
        </tr>
    {/foreach}
{/foreach}
{/if}