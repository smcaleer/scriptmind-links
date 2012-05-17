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


/* Build confirmation box for link removal */
function link_rm_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove this link?\n Note: links can not be restored after removal!";
   var answer = confirm (message);

   if (!answer)
      return false;

   return true;
}

/* Build confirmation box for category removal */
function categ_rm_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove this category and all it's subcategories?\n Note: categories can not be restored after removal!";
   answer = confirm (message);

   if (!answer)
      return false;

   return true;
}

/* Build confirmation box for user account removal */
function user_rm_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove this user account?\n Note: user accounts can not be restored after removal!";
   answer = confirm (message);

   if (!answer)
      return false;

   return true;
}

/* Build confirmation box for payment listing removal */
function payment_rm_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove this payment?\n Note: payment listings can not be restored after removal!";
   answer = confirm (message);

   if (!answer)
      return false;

   return true;
}