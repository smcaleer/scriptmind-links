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

var popWnd=null;
var closeWnd=true;

function pop_list_init(){
	 var a = document.getElementsByTagName("a");
 for(i=0; i<a.length; i++){
 	if(a[i].id != "" && a[i].className=="pop")
 		sp_addEvt(a[i], 'click', sp_showWnd);
 }
 var d = document.getElementsByTagName("div");
 for(i=0; i<d.length; i++){
 	if(d[i].className == "pop-list"){
	 	sp_addEvt(d[i], 'mouseout', sp_closeWnd);
	 	sp_addEvt(d[i], 'mouseover', sp_keepWnd);
	}
 }
}

function sp_showWnd(){
	 if(popWnd!=null){
	 	sp_closeWnd2(true);
	 }
	 closeWnd=false;
	 popWnd = sp_getObj("p"+this.id);
	 sp_show(popWnd);
	 x = sp_getPageX(this);
	 y = sp_getPageY(this);
	 w = sp_getW(popWnd);
	 h = sp_getH(popWnd);
	 pw = sp_getDocW()+sp_getScrollX();
	 ph = sp_getDocH()+sp_getScrollY();
	 if((w+x)>pw)
	 	x = pw-w;
	 if((h+y)>ph)
	 	y = ph-h;
	 sp_moveTo(popWnd,x, y);
	 return false;
}

function sp_closeWnd(){
	if(popWnd!=null){
	 closeWnd=true;
	 window.setTimeout("sp_closeWnd2(false)", 1000);
	}
}

function sp_closeWnd2(force){
	if((force||closeWnd)&&popWnd!=null){
		sp_hide(popWnd);
		popWnd=null;
		}
}
function sp_keepWnd(){
	closeWnd=false;
}