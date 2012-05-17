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

//Make additional spam protection checks

function check_post_rules($ressource='', $tplpath='', $returnVal=false)
{
   global $tpl;

   if (isset ($ressource) && is_array ($ressource) && !empty ($ressource))
   {
      //Check if submitter is using an user-agent
      if ($ALLOW_EMPTY_USERAGENT != 1)
      {
         //Determine user-agent
         $userAgent = (isset ($_SERVER['HTTP_USER_AGENT']) && !empty ($_SERVER['HTTP_USER_AGENT']) ? filter_white_space($_SERVER['HTTP_USER_AGENT']) : '');

         if (empty ($userAgent))
         {
            //No user-agent available,
            //further access blocked

            unset ($_POST, $_GET, $_REQUEST);

            //Provide a reason why access was unautorised
            $reason = _L('You have no or an invalid useragent').'!';

            if ($returnVal)
            {
               return gotoUnauthorized($reason, $tplpath.'unauthorized.tpl', true);
            }
            else
            {
               gotoUnauthorized($reason, $tplpath.'unauthorized.tpl', false);
               exit();
            }
         }
      }

      //Check if submission is comming from
      //the current server or somewhere else
      if ($ALLOW_FOREIGN_REFERER != 1)
      {
         //Determine server hostname
         $serverHostTemp = (isset ($_SERVER['SERVER_NAME']) && !empty ($_SERVER['SERVER_NAME']) ? trim ($_SERVER['SERVER_NAME']) : (isset ($_SERVER['HTTP_HOST']) && !empty ($_SERVER['HTTP_HOST']) ? trim ($_SERVER['HTTP_HOST']) : ''));
         //Get only domain
         //(usually not needed but server configs are not always correct)
         $serverHost     = trim (parseDomain($serverHostTemp));

         if (empty ($serverHost))
         {
            //Could not determine server hostname,
            //usually if it's an IP address
            $serverPath = parseURL($serverHostTemp);
            $serverHost = (!empty ($serverPath['path']) ? $serverPath['path'] : $serverHostTemp);

            unset ($serverPath);
         }

         //Determine page where post came from
         $refererHostTemp = (isset ($_SERVER['HTTP_REFERER']) && !empty ($_SERVER['HTTP_REFERER']) ? trim ($_SERVER['HTTP_REFERER']) : '');
         $refererHost     = parseDomain($refererHostTemp);

         $pattern     = array ('`^http[s]?:`', '`^ftp:`', '`^mailto:`', '`^www\.`', '`^\.`', '`\.$`', '`[^\w\d-\.]`');
         $serverHost  = preg_replace ($pattern, '', $serverHost);
         $refererHost = preg_replace ($pattern, '', $refererHost);

         //Check if hostnames are identical
         if (!empty ($serverHost) && !empty ($refererHost) && $serverHost != $refererHost)
         {
            //Hostnames do not match,
            //Submission is not allowed!

            //Provide a reason why access was unautorised
            $reason = _L('You are now allowed to submit using foreign pages or scripts').'!';

            if ($returnVal)
            {
               return gotoUnauthorized($reason, $tplpath.'unauthorized.tpl', true);
            }
            else
            {
               gotoUnauthorized($reason, $tplpath.'unauthorized.tpl', false);
               exit();
            }
         }

         unset ($serverHost, $serverHostTemp, $refererHost, $refererHostTemp);
      }
   }

   unset ($ressource, $tplpath, $returnVal);
   return false;
}
?>