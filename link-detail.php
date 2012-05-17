<?php
require_once 'init.php';
session_start();

preg_match ('#(.*)(_|-)(\d+)\.htm[l]?$#i', request_uri(), $matches);

$id = (!empty ($matches[3]) ? intval ($matches[3]) : 0);
$linkdetail = $db->GetAll("SELECT * FROM PLD_LINK WHERE ID ='$id'");
$tpl->assign('linkdetail', $linkdetail);

$path = array ();
$path[] = array ('ID' => '0', 'TITLE' => _L('Home'), 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => '');
$path[] = array ('ID' => '0', 'TITLE' => _L('Link Detail'), 'TITLE_URL' => '', 'DESCRIPTION' => _L('Link Detail'));

$tpl->assign('path', $path);

$categs = get_categs_tree(0);
$tpl->assign('categs', $categs);
$tpl->assign($data);
$tpl->assign('LINK_TYPE', $link_type);

/* Top level Categories */
$topcats = $db->GetAll("SELECT * FROM `{$tables['category']['name']}` WHERE `STATUS` = 2 AND `PARENT_ID` = 0 ORDER BY `TITLE`");
$tpl->assign('topcats', $topcats);

echo $tpl->fetch('link-detail.tpl');
?>