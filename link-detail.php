<?php
require_once 'init.php';
session_start();

preg_match ('#(.*)(_|-)(\d+)\.htm[l]?$#i', request_uri(), $matches);

$id = (!empty ($matches[3]) ? intval ($matches[3]) : 0);
$linkdetail = $db->GetAll("SELECT * FROM PLD_LINK WHERE ID ='$id'");

// Deep links are currently stored in a 3*5 array in the link record
// expressed as 15 individual fields.
// We transform that into a true array before passing to the template

function get_field_or_empty( &$linkdetail, $key ) {
    if(array_key_exists($key, $linkdetail) ) {
        $answer = $linkdetail[ $key ];
        if( ! is_null( $answer ) ) {
            return $answer;
        }
    }
    return '';
}

function maybe_add_deeplink( &$deeplinks, $linkdetail, $index ) {
    $url= get_field_or_empty( $linkdetail, 'URL'.$index );
    $title=get_field_or_empty( $linkdetail, 'TITLE'.$index );
    $description=get_field_or_empty( $linkdetail, 'DESCRIPTION'.$index);
    if( '' != ($url.$title.$description) ) {
        $deeplinks[] = array(
            'URL' => $url,
            'TITLE' => ( $title == '' ) ? $url : $title,
            'DESCRIPTION' => $description );
    }
}

$deeplinks=array();
maybe_add_deeplink( $deeplinks, $linkdetail[0], '1' );
maybe_add_deeplink( $deeplinks, $linkdetail[0], '2' );
maybe_add_deeplink( $deeplinks, $linkdetail[0], '3' );
maybe_add_deeplink( $deeplinks, $linkdetail[0], '4' );
maybe_add_deeplink( $deeplinks, $linkdetail[0], '5' );
$tpl->assign('deeplinks', $deeplinks);
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