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

var TTWnd=null;

function tooltip_init(){
	 var a = document.getElementsByTagName("a");
 for(i=0; i<a.length; i++){
 	if(a[i].className == "htt" && a[i].id != ""){
 		sp_addEvt(a[i], 'mouseover', sp_showTT);
 		sp_addEvt(a[i], 'mouseout', sp_closeTT);
 	}
 }
}

function sp_showTT(){
	 if(TTWnd!=null){
	 	sp_closeTT();
	 }
	 TTWnd = sp_getObj("t"+this.id);
	 sp_show(TTWnd);
	 x = sp_getPageX(this);
	 y = sp_getPageY(this);
	 w = sp_getW(TTWnd);
	 h = sp_getH(TTWnd);
	 th = sp_getH(this);
	 y = y + th+7;
	 pw = sp_getDocW()+sp_getScrollX();
	 ph = sp_getDocH()+sp_getScrollY();
	 if((w+x)>pw)
	 	x = pw-w;
	 if((h+y)>ph)
	 	y = ph-h;
	 sp_moveTo(TTWnd,x, y);
	 return false;
}

function sp_closeTT(){
	if(TTWnd!=null){
		sp_hide(TTWnd);
		TTWnd=null;
		}
}