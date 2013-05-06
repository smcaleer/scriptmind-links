<?php
/**
# ######################################################################
# Project:     ScriptMind::Links version 0.2.0
#
# **********************************************************************
# Copyright (C) 2004-2006 NetCreated, Inc. (http://www.netcreated.com/)
# Copyright (C) 2013 Bruce Clement (http://www.clement.co.nz)
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
# **********************************************************************
#
# @link           http://www.phplinkdirectory.com/
# @copyright      2004-2006 NetCreated, Inc. (http://www.netcreated.com/)
# @projectManager David DuVal <david@david-duval.com>
# @package        PHPLinkDirectory
#
# @link           http://www.scriptmind.org/
# @copyright      2013 Bruce Clement (http://www.clement.co.nz)
# @projectManager Bruce Clement
# @package        ScriptMind::Links
# ######################################################################
*/

require_once 'libs/adodb/adodb.inc.php';
/**
 * creates or updates the database structure based on the structure defined in tables.php
 *
 * @param string $db_type database type
 * @param string $db_host database host
 * @param string $db_name dabase name
 * @param string $db_user database login
 * @param string $db_password database password
 * @return int 0 if succesfull, error code otherwise
 */
function create_db($insertData, $db_type=DB_DRIVER, $db_host=DB_HOST, $db_name=DB_NAME,
                   $db_user=DB_USER, $db_password=DB_PASSWORD )
{
   global $tables;
   /* @var $db0 ADOConnection */
   $db0 = ADONewConnection($db_type);
   $db_created = 0;
   if (!$db0->Connect($db_host, $db_user, $db_password))
      return array (false, 'INSTALL_ERROR_CONNECT', $db0->ErrorMsg());
   unset( $db0 );

   /* @var $db ADOConnection */
   $db = ADONewConnection($db_type);
   if (!$db->Connect($db_host, $db_user, $db_password, $db_name))
   {
      $db = ADONewConnection($db_type);
      if ($db->Connect($db_host, $db_user, $db_password))
      {
         $dict = NewDataDictionary($db);
         $sql_array = $dict->CreateDatabase($db_name);
         if ($sql_array)
            $db_created = $dict->ExecuteSQLArray($sql_array);
      }
      if ($db_created != 2) {
         return array (false, 'INSTALL_ERROR_CREATE_DB', $db->ErrorMsg());
      }

      $db->SelectDB($db_name);
   }
   #$db->debug = true;
   $tables_existing = $db->MetaTables('TABLES');
   $dict = NewDataDictionary($db);
   foreach ($tables as $table_key => $table)
   {
      $table_name = $table['name'];

      //Drop all previous indexes
      $ListIndex = $db->GetAll("SHOW INDEX FROM `{$table_name}`");
      if (is_array ($ListIndex) && !empty ($ListIndex))
      {
         foreach ($ListIndex as $index_key => $index)
         {
            //Keep primary keys
            if ($index['Key_name'] != 'PRIMARY')
               $db->Execute("DROP INDEX `{$index['Key_name']}` ON `{$table_name}`");

            unset ($index, $ListIndex[$index_key]);
         }
      }

      if (is_array ($table['fields']))
      {
         $fields = array ();
         foreach ($table['fields'] as $field_name => $field_def)
            $fields[] = $field_name.' '.$field_def;

         $created = 0;
         if ($sql_array = $dict->ChangeTableSQL($table_name, implode(',', $fields)))
            $created = $dict->ExecuteSQLArray($sql_array);

         if ($created != 2) {
            return array (false, 'INSTALL_ERROR_CREATE', $db->ErrorMsg());
         }
      }
      if( is_array ($table['key'])) {
          $db->Execute("DROP INDEX `PRIMARY` ON `{$table_name}`"); // Ignore result
          $sql_array = $dict->CreateIndexSQL('PRIMARY', $table_name, $table['key'], 'UNIQUE');
          $created=$dict->ExecuteSQLArray($sql_array);
      }
      if (is_array ($table['indexes']))
      {
         $indexes_existing = $db->MetaIndexes($table_name);
         foreach ($table['indexes'] as $index_name => $index_def)
         {
            $index_name = $table_name.'_'.$index_name.'_IDX';
            $index_opts = array ();
            if (is_array ($index_def))
            {
               $index_fields = $index_def[0];
               $index_opts = explode(' ', $index_def[1]);
            }
            else
               $index_fields = $index_def;

            if (array_key_exists ($index_name, $indexes_existing) || array_key_exists (strtolower ($index_name), $indexes_existing))
               if ($sql_array = $dict->CreateIndexSQL($index_name, $table_name, $index_fields, array_merge($index_opts, array ('DROP'))))
                  $dict->ExecuteSQLArray($sql_array);

            $created = 0;
            if ($sql_array = $dict->CreateIndexSQL($index_name, $table_name, $index_fields, $index_opts))
               $created = $dict->ExecuteSQLArray($sql_array);

            if($created != 2) {
               return array (false, 'INSTALL_ERROR_CREATE', $db->ErrorMsg());
            }
         }
      }
      if ($insertData && is_array ($table['data']))
      {
         if (is_array( $table['key'])) {
             $key=$table['key'];
         } else {
             $key=array('ID');
         }
         foreach ($table['data'] as $row)
         {
            $check_where='';
            foreach( $key as $check_part) {
                $check_where .= ' AND `'.$check_part.'` = ' . $db->qstr($row[$check_part]);
            }
            $check_where = substr( $check_where, 5);
            $sql = "SELECT `ID` FROM `{$table_name}` WHERE {$check_where}";
            $rs = $db->SelectLimit($sql, 1);
            if ($rs && $rs->EOF)
               if (!$db->AutoExecute($table_name, $row, 'INSERT', false, true, true)) {
                  return array (false, 'INSTALL_ERROR_CREATE', $db->ErrorMsg());
               }
         }
      }
   }
   return array (true, $db_created == 0 ? 'INSTALL_DB_UPDATED' : 'INSTALL_DB_CREATED');
}
