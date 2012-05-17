<?php
/**
# ######################################################################
# Project:     PHPLinkDirectory: Version 2.1.2
#
# **********************************************************************
# Copyright (C) 2004-2006 NetCreated, Inc. (http://www.netcreated.com/)
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
# For questions, help, comments, discussion, etc., please join the
# PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
#
# @link           http://www.phplinkdirectory.com/
# @copyright      2004-2006 NetCreated, Inc. (http://www.netcreated.com/)
# @projectManager David DuVal <david@david-duval.com>
# @package        PHPLinkDirectory
# ######################################################################
*/



/**
 * Calculate password field length for user table,
 * depending on PHP version for eigther use "sha1" or "md5" hash function
 */
if (version_compare (phpversion(), "4.3.0", ">=") && function_exists ('sha1'))
   $PasswFieldLength = 40 + strlen ("{sha1}"); /* sha1 hash + {sha1} prefix */
else
   $PasswFieldLength = 32 + strlen ("{md5}");  /* md5 hash + {md5} prefix   */

$tables = array();

/**
 * ADOdb Data Dictionary Library for PHP (Full Documentation)
 * http://phplens.com/lens/adodb/docs-datadict.htm
 */
 $tables['link'] = array (
   'name'   => TABLE_PREFIX.'LINK'                  ,
   'fields' => array (
      'ID'                 => 'I KEY AUTO'          ,
      'TITLE'              => 'C(255) NOTNULL'      ,
      'DESCRIPTION'        => 'X2 NULL'             ,
      'URL'                => 'C(255) NOTNULL'      ,
      'CATEGORY_ID'        => 'I NOTNULL'           ,
      'RECPR_URL'          => 'C(255) NULL'         ,
      'RECPR_REQUIRED'     => 'L NOTNULL DEFAULT 0' ,
      'STATUS'             => 'I NOTNULL DEFAULT 0' ,
      'VALID'              => 'L NOTNULL DEFAULT 0' ,
      'RECPR_VALID'        => 'L NOTNULL DEFAULT 0' ,
      'OWNER_ID'           => 'I NULL'              ,
      'OWNER_NAME'         => 'C(255) NULL'         ,
      'OWNER_EMAIL'        => 'C(255) NULL'         ,
      'OWNER_NOTIF'        => 'I NOTNULL DEFAULT 0' ,
      'DATE_MODIFIED'      => 'T DEFDATE'           ,
      'DATE_ADDED'         => 'T DEFDATE'           ,
      'HITS'               => 'I NOTNULL DEFAULT 0' ,
      'LAST_CHECKED'       => 'T'                   ,
      'RECPR_LAST_CHECKED' => 'T'                   ,
      'PAGERANK'           => 'I NOTNULL DEFAULT -1',
      'RECPR_PAGERANK'     => 'I NOTNULL DEFAULT -1',
      'FEATURED_MAIN'      => 'I NOTNULL DEFAULT 0' ,
      'FEATURED'           => 'I NOTNULL DEFAULT 0' ,
      'EXPIRY_DATE'        => 'T'                   ,
      'NOFOLLOW'           => 'L NOTNULL DEFAULT 0' ,
      'PAYED'              => 'I NOTNULL DEFAULT -1',
      'LINK_TYPE'          => 'I NOTNULL DEFAULT 0' ,
      'IPADDRESS'          => 'C(15) NULL',
	  // Deeplinks
	  'TITLE1' => 'C(255) NULL' ,
      'URL1' => 'C(255) NULL' ,
      'TITLE2' => 'C(255) NULL' ,
      'URL2' => 'C(255) NULL' ,
      'TITLE3' => 'C(255) NULL' ,
      'URL3' => 'C(255) NULL' ,
	  'TITLE4' => 'C(255) NULL' ,
      'URL4' => 'C(255) NULL' ,
	  'TITLE5' => 'C(255) NULL' ,
      'URL5' => 'C(255) NULL' ,
      'DESCRIPTION1'        => 'X2 NULL'  ,
      'DESCRIPTION2'        => 'X2 NULL'  ,
      'DESCRIPTION3'        => 'X2 NULL'  ,
      'DESCRIPTION4'        => 'X2 NULL'  ,
	  'DESCRIPTION5'        => 'X2 NULL'
   ),
   'indexes' => array (
      'TITLE'              => 'TITLE'                               ,
      'DESCRIPTION'        => array ('DESCRIPTION'     , 'FULLTEXT'),
      'URL'                => 'URL'                                 ,
      'CATEGORY_ID'        => 'CATEGORY_ID'                         ,
      'STATUS_CATEGORY_ID' => 'STATUS, CATEGORY_ID'                 ,
      'HITS'               => 'HITS'                                ,
      'FEATURED'           => 'FEATURED'                            ,
      'EXPIRY_DATE'        => 'EXPIRY_DATE'
   )
 );

 $tables['category'] = array (
   'name'   => TABLE_PREFIX.'CATEGORY'              ,
   'fields' => array (
      'ID'               => 'I KEY AUTO'            ,
      'TITLE'            => 'C(255) NOTNULL'        ,
      'TITLE_URL'        => 'C(255) NULL'           ,
      'DESCRIPTION'      => 'X2 NULL'               ,
      'PARENT_ID'        => 'I NOTNULL'             ,
      'STATUS'           => 'I NOTNULL DEFAULT 1'   ,
      'DATE_ADDED'       => 'T DEFDATE'             ,
      'HITS'             => 'I NOTNULL DEFAULT 0'   ,
      'SYMBOLIC'         => 'I NOTNULL DEFAULT 0'   ,
      'SYMBOLIC_ID'      => 'I NOTNULL DEFAULT 0'
   ),
   'indexes' => array (
      'TITLE'            => 'TITLE'                               ,
      'TITLE_URL'        => 'TITLE_URL'                           ,
      'DESCRIPTION'      => array ('DESCRIPTION'     , 'FULLTEXT'),
      'PARENT_ID'        => 'PARENT_ID'                           ,
      'STATUS'           => 'STATUS'                              ,
      'HITS'             => 'HITS'
   )
 );

 $tables['email'] = array (
   'name'   => TABLE_PREFIX.'EMAIL'                 ,
   'fields' => array (
      'ID'        => 'I KEY AUTO'                   ,
      'EMAIL'     => 'C(255) NOTNULL'               ,
      'TITLE'     => 'C(255) NOTNULL'               ,
      'NAME'      => 'C(255)'                       ,
      'URL'       => 'C(255) NOTNULL'               ,
      'DATE_SENT' => 'T DEFDATE'
   ),
 );

  $tables['email_tpl'] = array (
   'name'   => TABLE_PREFIX.'EMAIL_TPL'             ,
   'fields' => array (
      'ID'       => 'I KEY AUTO'                    ,
      'TPL_TYPE' => 'I DEFAULT 1'                   ,
      'TITLE'    => 'C(255) NOTNULL'                ,
      'SUBJECT'  => 'C(255) NOTNULL'                ,
      'BODY'     => 'B'
   ),
 );

 $tables['config'] = array (
   'name'   => TABLE_PREFIX.'CONFIG'                ,
   'fields' => array (
      'ID'    => 'C(255) KEY'                       ,
      'VALUE' => 'C(255) NULL'
   ),
   'data' => array (
      array ('ID' => 'SITE_NAME'                , 'VALUE' => 'Site Name'                  ),
      array ('ID' => 'SITE_URL'                 , 'VALUE' => 'http://www.yourdomain.com/' ),
      array ('ID' => 'SITE_DESC'                , 'VALUE' => 'Site description'           ),
      array ('ID' => 'LINKS_PER_PAGE'           , 'VALUE' => '20'                         ),
      array ('ID' => 'MAILS_PER_PAGE'           , 'VALUE' => '20'                         ),
      array ('ID' => 'VISUAL_CONFIRM'           , 'VALUE' => '1'                          ),
      array ('ID' => 'REQUIRE_RECIPROCAL'       , 'VALUE' => '0'                          ),
      array ('ID' => 'ALLOW_MULTIPLE'           , 'VALUE' => '1'                          ),
      array ('ID' => 'CATS_PER_ROW'             , 'VALUE' => '2'                          ),
      array ('ID' => 'ENABLE_REWRITE'           , 'VALUE' => '0'                          ),
      array ('ID' => 'VERSION'                  , 'VALUE' => CURRENT_VERSION              ),
      array ('ID' => 'RECPR_NOFOLLOW'           , 'VALUE' => '1'                          ),
      array ('ID' => 'EMAIL_METHOD'             , 'VALUE' => 'mail'                       ),
      array ('ID' => 'EMAIL_SERVER'             , 'VALUE' => 'localhost'                  ),
      array ('ID' => 'EMAIL_USER'               , 'VALUE' => ''                           ),
      array ('ID' => 'EMAIL_PASS'               , 'VALUE' => ''                           ),
      array ('ID' => 'EMAIL_SENDMAIL'           , 'VALUE' => '/usr/bin/sendmail'          ),
      array ('ID' => 'CATS_PREVIEW'             , 'VALUE' => '3'                          ),
      array ('ID' => 'CATS_COUNT'               , 'VALUE' => '1'                          ),
      array ('ID' => 'DIRECTORY_TITLE'          , 'VALUE' => 'Directory'                  ),
      array ('ID' => 'ENABLE_RSS'               , 'VALUE' => '1'                          ),
      array ('ID' => 'ENABLE_PAGERANK'          , 'VALUE' => '1'                          ),
      array ('ID' => 'DEBUG'                    , 'VALUE' => '0'                          ),
      array ('ID' => 'SHOW_PAGERANK'            , 'VALUE' => '1'                          ),
      array ('ID' => 'DEFAULT_SORT'             , 'VALUE' => 'P'                          ),
      array ('ID' => 'ENABLE_NEWS'              , 'VALUE' => '1'                          ),
      array ('ID' => 'ADMIN_LANG'               , 'VALUE' => 'en'                         ),
      array ('ID' => 'FRONTEND_LANG'            , 'VALUE' => 'en'                         ),
      array ('ID' => 'LINKS_TOP'                , 'VALUE' => '20'                         ),
      array ('ID' => 'NTF_SUBMIT_TPL'           , 'VALUE' => ''                           ),
      array ('ID' => 'NTF_APPROVE_TPL'          , 'VALUE' => ''                           ),
      array ('ID' => 'NTF_REJECT_TPL'           , 'VALUE' => ''                           ),
      array ('ID' => 'NTF_PAYMENT_TPL'          , 'VALUE' => ''                           ),
      array ('ID' => 'FTR_ENABLE'               , 'VALUE' => '0'                          ),
      array ('ID' => 'FTR_MAX_LINKS'            , 'VALUE' => '5'                          ),
      array ('ID' => 'PAY_ENABLE'               , 'VALUE' => '0'                          ),
      array ('ID' => 'PAY_UM'                   , 'VALUE' => '0'                          ),
      array ('ID' => 'PAY_NORMAL'               , 'VALUE' => '0'                          ),
      array ('ID' => 'PAY_FEATURED'             , 'VALUE' => '0'                          ),
      array ('ID' => 'PAY_RECPR'                , 'VALUE' => '0'                          ),
      array ('ID' => 'PAY_NORMAL_PLUS'			, 'VALUE' => '0'                          ),
      array ('ID' => 'PAY_FEATURED_PLUS'			, 'VALUE' => '0'                          ),
      array ('ID' => 'PAY_AUTO_ACCEPT'          , 'VALUE' => ''                           ),
      array ('ID' => 'PAY_ENABLE_FREE'          , 'VALUE' => '0'                          ),
      array ('ID' => 'PAYPAL_ENABLE'            , 'VALUE' => '0'                          ),
      array ('ID' => 'PAYPAL_ACCOUNT'           , 'VALUE' => ''                           ),
      # Pager Mod
      array ('ID' => 'PAGER_LPP'                , 'VALUE' => '20'                         ),
      array ('ID' => 'PAGER_GROUPINGS'          , 'VALUE' => '20'                         ),
      # Links open in blank window
      array ('ID' => 'ENABLE_BLANK'             , 'VALUE' => '0'                          ),
      # reCaptcha
      array ('ID' => 'RECAPTCHA_PUBLIC_KEY' , VALUE => '6Lfi6ggAAAAAAFJ8xKWLVFxSjQS_zOcmYXZJfjAf'),
      array( 'ID' => 'RECAPTCHA_PRIVATE_KEY', VALUE => '6Lfi6ggAAAAAAI8O6WVGzX25rRy71oYKlKeoIniT'),
   )
 );

 $tables['payment'] = array (
   'name'   => TABLE_PREFIX.'PAYMENT'               ,
   'fields' => array (
      'ID'                => 'I KEY AUTO'             ,
      'LINK_ID'           => 'C(15) NOTNULL'          ,
      'NAME'              => 'C(255)'                 ,
      'EMAIL'             => 'C(255)'                 ,
      'IPADDRESS'         => 'C(15) NOTNULL'          ,
      'AMOUNT'            => 'N(8.2) NOTNULL'         ,
      'QUANTITY'          => 'I NOTNULL'              ,
      'TOTAL'             => 'N(8.2) NOTNULL'         ,
      'PAYED_TOTAL'       => 'N(8.2) NOTNULL'         ,
      'PAYED_QUANTITY'    => 'I NOTNULL'              ,
      'UM'                => 'I NOTNULL'              ,
      'CONFIRMED'         => 'I NOTNULL DEFAULT 0'    ,
      'PAY_DATE'          => 'T NOTNULL DEFDATE'      ,
      'CONFIRM_DATE'      => 'T'                      ,
      'RAW_LOG'           => 'X2'
   )
 );

 $tables['user'] = array (
   'name'   => TABLE_PREFIX.'USER'                  ,
   'fields' => array (
      'ID'                 => 'I KEY AUTO'          ,
      'LOGIN'              => 'C(100) NOTNULL'      ,
      'NAME'               => 'C(255) NOTNULL'      ,
      'PASSWORD'           => 'C('.$PasswFieldLength.') NOTNULL',
      'EMAIL'              => 'C(255) NOTNULL'      ,
      'ADMIN'              => 'L NOTNULL DEFAULT 0' ,
      'SUBMIT_NOTIF'       => 'L NOTNULL DEFAULT 1' ,
      'PAYMENT_NOTIF'      => 'L NOTNULL DEFAULT 1' ,

   )
 );

 $tables['user_permission'] = array (
   'name'   => TABLE_PREFIX.'USER_PERMISSION'       ,
   'fields' => array (
      'ID'          => 'I KEY AUTO'                 ,
      'USER_ID'     => 'I NOTNULL'                  ,
      'CATEGORY_ID' => 'I NOTNULL'
   )
 );

?>