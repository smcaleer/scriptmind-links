<?php
/**
# ######################################################################
# Project:     ScriptMind::Links version 0.2.0
#
# **********************************************************************
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
#
# @link           http://www.scriptmind.org/
# @copyright      2013 Bruce Clement (http://www.clement.co.nz)
# @projectManager Bruce Clement
# @package        ScriptMind::Links
# ######################################################################
*/

require_once 'init.php';

class category {
    public $childcats;
    public $links;
    public $ID;
    public $PARENT_ID;
    public $values;
    public function __construct( array & $values) {
        $this->childcats = array();
        $this->links=array();
        $this->values=array();
        foreach( $values as $key => $value ) {
            if( ! empty($value)) {
                $this->values[xml_utf8_encode($key)]=xml_utf8_encode($value);
            }
        }
        if( empty( $this->values['PARENT_ID']))
            $this->PARENT_ID = 0;
        else
            $this->PARENT_ID = $this->values['PARENT_ID'];
        $this->ID = $this->values['ID']+0;
    }
}

class link {
    public $ID;
    public $categoryID;
    public $values;
    public $name;
    public function __construct( array & $values) {
        $this->ID = $values['ID']+0;

        if( empty( $values['CATEGORY_ID']))
            $this->categoryID = 0;
        else
            $this->categoryID = $values['CATEGORY_ID']+0;

        $this->values=array();
        foreach( $values as $key => $value ) {
            if( $key != 'ID' && $key != 'CATEGORY_ID' && ! empty($value)) {
                $this->values[xml_utf8_encode($key)]=xml_utf8_encode($value);
            }
        }
    }
}


function exportTable( $db, $table, $sql, $entityName, $translations, $subqueries='', $indent="\t", $idField="ID"  ) {
    global $tables,$tablesToProcess;
    unset( $tablesToProcess[$table] );
    $rs = $db->Execute($sql);
    if( !$rs )
        return false;
    $noSubquery =  ! is_array($subqueries);
    while (!$rs->EOF)
    {
        $fields = $rs->fields;
        $id = $rs->Fields($idField);
        $rs->MoveNext();
        $entity=$indent."<$entityName ID=\"$id\"";
        $inner=array();
        foreach( $fields as $key => $value ) {
            if( $key == $idField)
                continue;
            if( empty($translations[$key]) ) {
                if( empty($value) )
                    continue;
            } else {
                $translate=$translations[$key];
                if( !empty( $translate['ignore'] ))
                    continue;
                if( empty($value) && empty( $translate['includeEmpty'] ) )
                    continue;
                if( !empty( $translate['name'] ))
                    $key=$translate['name'];
                if( !empty( $translate['cdata'])) {
                    $inner[]=array($key=>'<![CDATA['.$value.']]>');
                    continue;
                }
                $value=xml_utf8_encode($value);
                if( !empty( $translate['inner'])) {
                    $inner[]=array($key=>$value);
                    continue;
                }
            }
            $entity .= " $key=\"$value\"";
        }
        if( $noSubquery && empty($inner)) {
            print $entity."/>\n";
        } else {
            print $entity.">\n";
            foreach ($inner as $values) {
                foreach($values as $key=>$value ) {
                    print "$indent\t<$key>$value</$key>\n";
                }
            }
            if( ! $noSubquery ) {
                foreach( $subqueries as $subquery ) {
                    $subtable=$tables[$subquery['table']]['name'];
                    $linkField=$subquery['link'];
                    $subsql="SELECT * FROM `$subtable` WHERE `$linkField` = $id ORDER BY ID";
                    if( empty($subquery['translation']) ) {
                        $omit=array();
                    } else {
                        $omit=$subquery['translation'];
                    }
                    if( empty( $omit[$linkField])) {
                        $omit[$linkField]=array('ignore'=>1);
                    }
                    exportTable( $db, $subquery['table'], $subsql, $subquery['entity'], $omit, '', $indent."\t" );
                }
            }
            print "$indent</$entityName>\n";
        }
    }
    return true;
}

function exportCategories( $categories, $prefix) {
    $subprefix=$prefix."\t";
    /** @var category  */
    foreach( $categories as $category ) {
        print $prefix."<Category ID=\"{$category->values['ID']}\" NAME=\"{$category->values['TITLE']}\">\n";
        foreach( $category->values as $key => $value ) {
            if( $key!='ID' && ! empty($value)) {
                print $subprefix."<".$key.">".$value."</".$key.">\n";
            }
        }
        if( ! empty($category->childcats ) ) {
            exportCategories($category->childcats,$subprefix);
        }

        foreach( $category->links as $link ) {
            print $subprefix."<Link ID=\"{$link->ID}\">\n";
            foreach( $link->values as $key=>$value ) {
                print $subprefix."\t<{$key}>{$value}</{$key}>\n";
            }
            print $subprefix."</Link>\n";
        }
        print $prefix."</Category>\n";
    }
}

$tablesToProcess=array();
foreach( $tables as $key=>$value) {
    $tablesToProcess[$key] = 1;
}

$db->SetFetchMode(ADODB_FETCH_ASSOC);

@ header('Content-type: application/xml');
print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
print "<ScriptMindLinksExport>\n";
print "\t<Source>\n";
if( !defined('PROJECT_NAME') ) {
    if( CURRENT_VERSION < '2.0.0' ) {
        define ('PROJECT_NAME', 'GplLd');
    } else {
        define ('PROJECT_NAME', 'PhpLd');
    }
}
print "\t\t<Package>".PROJECT_NAME."</Package>\n";
print "\t\t<Version>".CURRENT_VERSION."</Version>\n";
print "\t\t<ExportedOn>".date('c')."</ExportedOn>\n";
print "\t</Source>\n";


// Write config
exportTable($db, 'config', "SELECT * FROM `{$tables['config']['name']}` WHERE VALUE <> '' and VALUE <> '0' ORDER BY `ID`", 'Config', array() );

// Build the table of categories
$categories=array();
$deferred=array();
$rootCats=array();
$sql = "SELECT * FROM `{$tables['category']['name']}` WHERE SYMBOLIC=0 ORDER BY ID";
unset( $tablesToProcess['category'] );
$rs = $db->Execute($sql);
while (!$rs->EOF)
{
    $category=new category($rs->fields);
    $categories[$category->ID]=$category;
    if( $category->PARENT_ID == 0 ) {
        $rootCats[]=$category;
    } else if( empty($categories[$category->PARENT_ID])) {
        $deferred[]=$category;
    } else {
        $categories[$category->PARENT_ID]->childcats[]=$category;
    }
    $rs->MoveNext();
}

// Now handle the deferred categories
foreach( $deferred as $category) {
    if( empty($categories[$category->PARENT_ID])) { // Orphan
        $rootCats[]=$category;
    } else {
        $category->childcats[]=$category;
    }
}

// Let's get the links
$sql = "SELECT * FROM `{$tables['link']['name']}` ORDER BY ID";
unset( $tablesToProcess['link'] );
$rs = $db->Execute($sql);
while (!$rs->EOF)
{
    $link=new link($rs->fields);
    $category=$link->categoryID;
    if( ! empty($categories[$category])) {
        $categories[ $category ]->links[] = $link;
    }
    $rs->MoveNext();
}

// OK, built, let's output what we have
exportCategories( $rootCats, "\t");


// Symbolic categories
$sql = "SELECT * FROM `{$tables['category']['name']}` WHERE SYMBOLIC=1 ORDER BY ID";
exportTable( $db, 'category', $sql, 'SymLink', array(
  'TITLE_URL'   => array('ignore'=>1) ,
  'DESCRIPTION' => array('ignore'=>1) ,
  'SYMBOLIC'    => array('ignore'=>1)
  ));
  exportTable($db, 'email_tpl', "SELECT * FROM `{$tables['email_tpl']['name']}` ORDER BY ID", 'EmailTemplate', array(
      'TITLE'  =>array('inner'=>1),
      'SUBJECT'=>array('inner'=>1),
      'BODY'   =>array('cdata'=>1),
  ));
  exportTable($db, 'user', "SELECT * FROM `{$tables['user']['name']}` ORDER BY ID", 'User', array(),
          array( array( 'table'=>"user_permission", 'link'=>"USER_ID", 'entity'=>'UserPermission' ) ) );

  // Now mop-up any unprocessed tables
  // NB: Does not include *_SEQ files as these aren't in $tables
  $WkTablesToProcess=$tablesToProcess;
  foreach( $WkTablesToProcess as $table=>$value ) {
      exportTable($db, $table, "SELECT * FROM `{$tables[$table]['name']}`", ucfirst($table), array() );
  }

print "</ScriptMindLinksExport>\n";
