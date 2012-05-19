<?php
/******************
 * PageRankXor32 class created by MagicBeanDip
 * PageRank class was created by others
 * Look for updates at http://v1.magicbeandip.com/mbd-file/PageRankReport.php
 * This code is released into the Public Domain
 *
 * Sample use:
 * include('PageRankXor32.php');
 * $oPR=new PageRankXor32();
 * echo $oPR->getRank('http://www.amazon.com');
 *
 ******************/

define('GOOGLE_MAGIC', 0xE6359A60);

// Use this class if your server is having problems with bitwise operations
class PageRankXor32 extends PageRank {
}

//This class should work on most servers
class PageRank {
    function HashURL($url) {
	$SEED = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
	$Result = 0x01020345;
	for ($i=0; $i<strlen($url); $i++)
	{
	    $Result ^= ord($SEED{$i%87}) ^ ord($url{$i});
	    $Result = (($Result >> 23) & 0x1FF) | $Result << 9;
	}
	return sprintf("8%x", $Result);
    }

    //returns -1 if no page rank was found
    function getRank($url, $data_center = "www.google.com")
    {
    $centre='toolbarqueries.google.com';
	$StartURL = "http://$centre/tbr?client=navclient-auto&features=Rank:&q=info:";
	$GoogleURL = $StartURL.$url. '&ch='.$this->HashURL($url);

	$fcontents = file_get_contents("$GoogleURL");
	$pagerank = substr($fcontents,9);
	if ($pagerank) {
	    $pagerank=preg_replace("/[^\w\d]/","",$pagerank);
	    return $pagerank;
	}else {
	    print preg_replace("/[^\w\d]/","",$fcontents) . ' ';
	    return "-1";
	}
        return $pagerank;
    }
}

function get_page_rank($url) {
    $p= new PageRank;
    return $p->getRank($url);
}
