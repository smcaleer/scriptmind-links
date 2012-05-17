<?php


/*
* Smarty plugin
*
* Type: function
* Name: pager
* Purpose: create a paging output to be able to browse long lists
* Version: 1.0
* Date: September 29, 2002
* Last Modified: July 7, 2003
* Install: Drop into the plugin direadasctory
* Author: Peter Dudas ager_mail at netrendorseg dot hu>
* HTTP: http://smarty.hu/plugins/duda/
*
*
* example:
* <{pager rowcount=$LISTDATA.rowcount limit=$LISTDATA.limit txt_first=$L_MORE class_num="fl" class_numon="fl" class_text="fl"}>
*
* CHANGES: 2003.03.14: positionable prev/next string. can use image instead of text
* CHANGES: 2003.03.21: Bugfixes
* CHANGES: 2003.04.14: Ability to show page number instead of row number, shift parameter
* CHANGES: 2003.07.07: prepared for negativ limits (unlimited), bugfix
*
*
*/
define('MAX_PAGES', 20);

function smarty_function_pager($params, & $smarty) {
	/* displays paging links to be able to browse in bit set of records
	@param mixed $rowcount - total number of items to page in between (if array=>numeer of lines)
	@param string $show - 'page' - to show page numbers, 'record' - to show record numbers (default records)
	@param int $limit - number of items on a page (if <0 unlimited)
	@param string $posvar - name of the php variable that contains the position data ($_REQUEST)
	@param string $forwardvars - comma separated list of php variablenames to forward in the links (only from $_REQUEST[] !!!)
	@param string $txt_first - on the first page don't print out all pages, just a this text, if set empty prints all page numbers
	@param string $img_first - on the first page don't print out all pages, just a this text, if set empty prints all page numbers
	@param boolean $no_first - print out all the pages, do not start with txt_firts, equals to txt_first set empty
	@param string $txt_prev - script to go to the prev page
	@param string $img_prev - button image to the prev page
	@param string $txt_next - script to go to the next page
	@param string $img_next - button image to go to the next page
	@param string $txt_pos - text position = 'top', 'bottom', 'middle/side'
	@param string $class_num - class for the page numbers <A> tag!
	@param string $class_numon - class for the aktive page!
	@param string $wrap_numon - wrap text for the aktive page. example: [|]
	@param string $class_text - class for the texts
	@param string $separator - string to put between the 1 2 3 pages (1 separator 2 separator);
	@param int $firstpos - record number of the first position
	@param int $shift - shift the record numbers with this value (useful if the position variable is printed, 0. page look bad, but 1. page!)
	@param string $url_pattern - Pattern used to generate page URLs. $ is replaced with the page no.
	*/
	// START INIT
	$show = 'record';
	$posvar = 'pos';
	$separator = ' - ';
	$class_text = 'nav';
	$class_num = 'small';
	$class_numon = 'big';
	$txt_pos = 'middle';
	$txt_prev = '<'; // previous
	$txt_next = '>'; // next
	$txt_first = 'More'; // archive, more articles
	$shift = 0;
	foreach ($params as $key => $value) {
		$tmps[strtolower($key)] = $value;
		$tmp = strtolower($key);
		if (!(${ $tmp } = $value)) {
			${ $tmp } = '';
		}
	}
	settype($shift, 'integer');
	// START data check
	$minVars = array ('limit');
	foreach ($minVars as $tmp) {
		if (empty ($params[$tmp])) {
			$smarty->trigger_error('plugin "pager": missing or empty parameter: "'.$tmp.'"');
		}
	}
	// END data check
	if ($txt_pos == 'middle') {
		$txt_pos = 'side';
	}
	if (!in_array($txt_pos, array ('side', 'top', 'bottom'))) {
		$smarty->trigger_error('plugin "pager": bad value for : "txt_pos"');
	}
	// if there is no need for paging at all
	if (is_array($rowcount)) {
		$rowcount = count($rowcount);
	}
	elseif (!is_int($rowcount)) {
		ceil($rowcount);
	}
	if ($rowcount <= $limit) {
		return '';
	}
	if ($limit < 1) {
		$limit = $rowcount +1;
	}
	if (!empty ($no_first)) {
		unset ($txt_first);
	}
	// determine the real position if the diplayed numbers were shifted (eg: showing 1 instead of 0)
	if ($shift > 0) {
		$pos = $_REQUEST[$posvar] * ($show == 'page' ? $limit : 1) - $shift;
		if ($pos < 0) {
			$pos = 0;
		}
	} else {
		$pos = $_REQUEST[$posvar] * ($show == 'page' ? $limit : 1);
	}
	// END INIT
	// print '<h1>pos:'.$pos.'</h1>';
	// remove these vars from the request_uri - only for beauty
	$removeVars = array ($posvar, '_rc');
	// START remove the unwanted variables from the query string
	parse_str($_SERVER['QUERY_STRING'], $urlVars);
	// add the forward vars
	if (!is_array($forwardvars)) {
		$forwardvars = preg_split('/[,;\s]/', $forwardvars, -1, PREG_SPLIT_NO_EMPTY);
	}
	$urlVars = array_merge($urlVars, $forwardvars);
	foreach ($urlVars as $key => $value) {
		if (in_array($key, $removeVars)) {
			unset ($urlVars[$key]);
		}
	}
	// END remove the unwanted variables from the query string
	// START build up the link
	$tmp = '';
	foreach ($urlVars as $key => $value) {
		if (is_array($value)) {
			foreach ($value as $val) {
				$tmp .= '&'.$key.'[]='.urlencode($val);
			}
		}
		elseif (!empty ($value)) {
			$tmp .= '&'.$key.'='.urlencode($value);
		} else {
			$tmp .= '&'.$key;
		}
	}
	if (!empty ($tmp)) {
		$url = $_SERVER['SCRIPT_NAME'].'?'.substr($tmp, 1);
		;
		$link = '&';
	} else {
		$url = $_SERVER['SCRIPT_NAME'];
		$link = '?';
	}
	// END build up the link
	// if there is no position (or 0) prepare the link for the second page
	if ((empty ($pos) OR ($pos < 1)) AND ($rowcount > $limit)) {
		if (!empty ($firstpos)) {
			$short['first'] .= $url.$link.$posvar.'='.$firstpos;
		}
		elseif ($pos == -1) {
			$short['first'] .= $url.$link.$posvar.'='. (1 + $shift);
		} else {
			$short['first'] = $url.$link.$posvar.'='. ($limit + $shift);
		}
	}
	// START create data to print
	if ($rowcount > $limit) {
		if ($rowcount < ($limit * MAX_PAGES)) {
			for ($i = 1; $i < $rowcount +1; $i += $limit) {
				if (($pos +1 >= $i) and ($pos +1 < ($i + $limit))) {
					$short['now'] = $i;
				}
				if ($url_pattern) {
				// Quick and dirty by Ap0s7le... please, no holding it against me!!
					if ($i == 1) {
						if (!strstr($_SERVER['REQUEST_URI'], '?')) {
							$pages[$i] = $_SERVER['REQUEST_URI'];
						} else {
							$pages[$i] = substr($_SERVER['REQUEST_URI'], 0, -strlen($url_pattern));
						}
					} else {
						$pages[$i] = str_replace('$', floor(($i -1) / ($show == 'page' ? $limit : 1)) + $shift, $url_pattern);
					}
				// End of hack
				} else {
					$pages[$i] = $url.$link.$posvar.'='. (floor(($i -1) / ($show == 'page' ? $limit : 1)) + $shift);
				}
			}
		} else { // if there a lot of records to page in beetween
			$start_page = ($pos -floor(MAX_PAGES / 2) * $limit) > 0 ? ($pos -floor(MAX_PAGES / 2) * $limit) : 1;
			$start_page = $start_page+MAX_PAGES * $limit>$rowcount?$rowcount-$limit*MAX_PAGES:$start_page;
			for ($i = $start_page; $i < $rowcount +1 && $i< ($start_page+MAX_PAGES * $limit); $i += $limit) {
				if (($pos +1 >= $i) and ($pos +1 < ($i + $limit))) {
					$short['now'] = $i;
				}
				if ($url_pattern) {
					$pages[$i] = str_replace('$', floor(($i -1) / ($show == 'page' ? $limit : 1)) + $shift, $url_pattern);
				} else {
					$pages[$i] = $url.$link.$posvar.'='. (floor(($i -1) / ($show == 'page' ? $limit : 1)) + $shift);
				}
			}
		}
		// previous - next stepping
		if ($pos >= $limit) {
			if ($url_pattern) {
			// Hack baby hack... get it done...
				$count = floor(($pos - $limit) / ($show == 'page' ? $limit : 1)) + $shift;
				if ($count == 1) {
					$short['prev'] = str_replace(str_replace('$', '2', $url_pattern), '', $_SERVER['REQUEST_URI']);
				} else {
					$short['prev'] = str_replace('$', floor(($pos - $limit) / ($show == 'page' ? $limit : 1)) + $shift, $url_pattern);
				}
			// End hack
			} else {
				$short['prev'] = $url.$link.$posvar.'='. (floor(($pos - $limit) / ($show == 'page' ? $limit : 1)) + $shift);
			}
		}
		if ($pos < $rowcount) {
			if ($url_pattern) {
				$short['next'] = str_replace('$', floor(($pos + $limit) / ($show == 'page' ? $limit : 1)) + $shift, $url_pattern);
			} else {
				$short['next'] = $url.$link.$posvar.'='. (floor(($pos + $limit) / ($show == 'page' ? $limit : 1)) + $shift);
			}
		}
	}
	// END preparing the arrays to print
	// START DISPLAY
	// all neccesary data are in $pages, and in $short
	/*if (($pos == 0) AND (!empty ($txt_first) OR !empty ($img_first))) {
		print '<a class="'.$class_text.'" href="'.$short['first'].'">';
		if (!empty ($img_first)) {
			if (preg_match('/\</', $img_first)) {
				// image tag
				print $img_first;
			} else {
				// image url
				print '<img src="'.$img_first.'" border="0" />';
			}
		} else {
			print $txt_first;
		}
		print '</a>'."\n";
	} else*/ {
		//
		// START prepare the prev and next string/image, make it a link ....
		if ($pos >= $limit) {
			$cache['prev'] = '<a class="'.$class_text.'" href="'.$short['prev'].'">';
			if (!empty ($img_prev)) {
				if (preg_match('/\</', $img_prev)) {
					// image tag
					$cache['prev'] .= $img_prev;
				} else {
					// image url
					$cache['prev'] .= '<img src="'.$img_prev.'" border="0" />';
				}
			} else {
				$cache['prev'] .= $txt_prev;
			}
			$cache['prev'] .= '</a>&nbsp;';
		} else {
			if (!empty ($img_prev)) {
				if (preg_match('/\</', $img_prev)) {
					// image tag
					$cache['prev'] .= $img_prev;
				} else {
					// image url
					$cache['prev'] .= '<img src="'.$img_prev.'" border="0" />';
				}
			} else {
				$cache['prev'] .= $txt_prev;
			}
			$cache['prev'] .= '&nbsp;';
		}
		// next
		if ($pos < ($rowcount)) {
			$cache['next'] = '&nbsp;<a class="'.$class_text.'" href="'.$short['next'].'">';
			if (!empty ($img_next)) {
				if (preg_match('/\</', $img_next)) {
					// image tag
					$cache['next'] .= $img_next;
				} else {
					// image url
					$cache['next'] .= '<img src="'.$img_next.'" border="0" />';
				}
			} else {
				$cache['next'] .= $txt_next;
			}
			$cache['next'] .= '</a>';
		} else {
			$cache['next'] = '&nbsp;';
			if (!empty ($img_next)) {
				if (preg_match('/\</', $img_next)) {
					// image tag
					$cache['next'] .= $img_next;
				} else {
					// image url
					$cache['next'] .= '<img src="'.$img_next.'" border="0" />';
				}
			} else {
				$cache['next'] .= $txt_next;
			}
			
		}
		// END prepare the prev and next string/image, make it a link ....
		//
		// START PRININT
		if ($txt_pos == 'top') {
			print $cache['prev'].$cache['next']."\n";
		}
		if (($txt_pos == 'side') AND (!empty ($cache['prev']))) {
			print $cache['prev'];
		}
		foreach ($pages as $num => $url) {
			if ($num > $limit) {
				print ' '.$separator.' ';
			}
			if ($show == 'record') { // show record number for paging
				$tmp = $num;
			} else { // show page number for paging
				if ($limit == 1) {
					$tmp = floor($num / $limit);
				} else {
					$tmp = floor($num / $limit) + 1;
				}
			}
			if ($num == $short['now'] && $wrap_numon) {
				$tmp = str_replace('|', $tmp, $wrap_numon);
			}
			print '<a class="'. (($num == $short['now']) ? $class_numon : $class_num).'" href="'.$url.'">'.$tmp.'</a>';
		}
		if (($txt_pos == 'side') AND (!empty ($cache['next']))) {
			print $cache['next'];
		}
		print "\n";
		// END NUMBERS
		// START PREVIOUS, NEXT paging
		if ($txt_pos == 'bottom') {
			print $cache['prev'].$cache['next']."\n";
		}
		// END PREVIOUS, NEXT paging
	}
	// END DISPLAY
	return '';
}
?>