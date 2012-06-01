<?php
/**
# ######################################################################
# Project:     ScriptMind::Links: Version 0.1.8
#
# **********************************************************************
# Copyright (C) 2012 Bruce Clement http://www.clement.co.nz
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
# @link           http://www.scriptmind.org/
# @copyright      2012 Bruce Clement http://www.clement.co.nz
# @projectManager Bruce Clement
# @package        ScriptMind::Links
# ######################################################################
*/

require_once 'init.php';

/**
 * Class representing objects linked from the sitemap page
 */
class SitemapObject{

    public $children;
    public $id;
    public $title;
    public $title_url;
    public $built_url;
    public $description;
    public $parent_id;
    public $linkable = true;
    public $parent = NULL;
    public $depth = 0;
    /**
     * Make this object a child of the parent and adjust URLs
     */
    public function SetParent( SitemapObject $parent ) {
        $this->parent = $parent;
        if( ! is_null( $parent )) {
            $this->depth = $parent->depth+1;
            if( ENABLE_REWRITE ) {
                $this->built_url = $parent->built_url . $this->title_url;
            }
            foreach( $this->children as $child ) {
                $child->SetParent( $this );
            }
        }
    }

    /**
     * Return an array consisting of this and all descendents
     */
    public function flatten( &$answer ) {
        $answer[] = $this;
        foreach( $this->children as $child ) {
            $child->flatten( $answer );
        }
    }
    public function __construct( $title, $title_url, $description, $id=-1, $parent_id = 0 ) {
        $this->children = array();
        $this->title = $title;
        $this->title_url = $title_url;
        $this->built_url = $title_url;
        $this->description = $description;
        $this->id = $id;
        $this->parent_id = $parent_id;
    }
}

/**
 * Class representing categories linked from the sitemap page
 * Really just a specialised constructor
 */
class SitemapDirectory extends SitemapObject{
    public function __construct( $row ) {
        $x=ENABLE_REWRITE;
        parent::__construct( $row['TITLE'],
                             ENABLE_REWRITE ? ( $row['TITLE_URL'].'/' )
                                        : ( BASE_URL . 'index.php?c='.$row['ID'] ),
                             $row['DESCRIPTION'],
                             $row['ID'],
                             $row[ 'PARENT_ID'] );
    }
}

/**
 * Find all active categories and build into a tree of SitemapObjects
 */
function getCategories() {
    global $db, $tables;
    $top = new SitemapObject( 'Categories', BASE_URL, '', 0, NULL );
    $top->linkable = false; // Just give it as a header
    $allcats=array($top);
    $unprocessed=array();
    $rs = $db->Execute("SELECT * FROM `{$tables['category']['name']}` WHERE `STATUS` = 2 and `SYMBOLIC` = 0 ORDER BY `TITLE`");
    while (!$rs->EOF)
    {
        $row = $rs->FetchRow();
        $cat=new SitemapDirectory( $row );
        $id = $cat->id;
        $parent_id = $cat->parent_id;
        $allcats[$id] = $cat;

        if(array_key_exists( $parent_id, $allcats)) {
            $parent = $allcats[$parent_id];
            $parent->children[] = $cat;
            $cat->SetParent($parent);
        } else {
            $unprocessed[] = $cat;
        }
    }

    // Now fix the forward references
    foreach( $unprocessed as $cat ) {
        $parent_id = $cat->parent_id;
        if(array_key_exists( $parent_id, $allcats)) {
            $parent = $allcats[$parent_id];
            $parent->children[] = $cat;
            $cat->SetParent($parent);
        }
    }
    return $top;
}

$Links = array();
define( 'BASE_URL',  DOC_ROOT . ( (substr( DOC_ROOT,-1) == '/' ) ? '' : '/' ) );

$page_tree = new SitemapObject( 'Pages', BASE_URL, '');
$page_tree->linkable = false;
foreach( array( array( 'Submit', 'submit.php', 'a new page' ),
                array( 'Latest', 'index.php?p=d', 'links added to the directory' ),
                array( 'Top Hits', 'index.php?p=d', 'most commonly selected pages' ),
            ) as $pagedata ) {
    $page=new SitemapObject( $pagedata[0], $pagedata[1], $pagedata[2] );
    $page_tree->children[] = $page;
    $page->SetParent($page_tree);
}
$page_tree->flatten($Links);


$cat_tree = getCategories();
$cat_tree->flatten($Links);

$tpl->assign( 'Categories', $Links);
//Make output
echo $tpl->fetch('sitemap.tpl', 'sitemap');
?>