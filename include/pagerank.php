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

/*
        Written and contributed by
        Alex Stapleton,
        Andy Doctorow,
        Tarakan,
        Bill Zeller,
        Vijay "Cyberax" Bhatter
        traB
    This code is released into the public domain
Xor32 class created by MagicBeanDip
*/
define ('GOOGLE_MAGIC', 0xE6359A60);

   //This class should work on most servers
   function zeroFill($a, $b)
   {
      $z = hexdec (80000000);
      if ($z & $a)
      {
         $a = ($a>>1);
         $a &= (~$z);
         $a |= 0x40000000;
         $a = ($a>>($b-1));
       }
       else
       {
         $a = ($a>>$b);
       }

       return $a;
    }

   function xor32($a, $b)
   {
      return int32($a) ^ int32($b);
   }

   //return least significant 32 bits
   //works by telling unserialize to create an integer even though we provide a double value
   function int32($x)
   {
      return unserialize ("i:$x;");
      //return intval($x); // This line doesn't work on all servers.
   }

   function mix($a,$b,$c)
   {
      $a -= $b; $a -= $c; $a = xor32($a,zeroFill($c,13));
      $b -= $c; $b -= $a; $b = xor32($b,$a<<8);
      $c -= $a; $c -= $b; $c = xor32($c,zeroFill($b,13));
      $a -= $b; $a -= $c; $a = xor32($a,zeroFill($c,12));
      $b -= $c; $b -= $a; $b = xor32($b,$a<<16);
      $c -= $a; $c -= $b; $c = xor32($c,zeroFill($b,5));
      $a -= $b; $a -= $c; $a = xor32($a,zeroFill($c,3));
      $b -= $c; $b -= $a; $b = xor32($b,$a<<10);
      $c -= $a; $c -= $b; $c = xor32($c,zeroFill($b,15));

      return array($a,$b,$c);
   }

   function GoogleCH($url, $length=null, $init=GOOGLE_MAGIC)
   {
      if (is_null ($length))
      {
         $length = sizeof ($url);
      }
      $a = $b = 0x9E3779B9;
      $c = $init;
      $k = 0;
      $len = $length;

      while ($len >= 12)
      {
         $a += ($url[$k+0] +($url[$k+1]<<8) +($url[$k+2]<<16) +($url[$k+3]<<24));
         $b += ($url[$k+4] +($url[$k+5]<<8) +($url[$k+6]<<16) +($url[$k+7]<<24));
         $c += ($url[$k+8] +($url[$k+9]<<8) +($url[$k+10]<<16)+($url[$k+11]<<24));
         $mix = mix($a,$b,$c);
         $a = $mix[0]; $b = $mix[1]; $c = $mix[2];
         $k += 12;
         $len -= 12;
     }
     $c += $length;
     switch ($len)
     {
         case 11: $c+=($url[$k+10]<<24);
         case 10: $c+=($url[$k+9]<<16);
         case 9 : $c+=($url[$k+8]<<8);
         /* the first byte of c is reserved for the length */
         case 8 : $b+=($url[$k+7]<<24);
         case 7 : $b+=($url[$k+6]<<16);
         case 6 : $b+=($url[$k+5]<<8);
         case 5 : $b+=($url[$k+4]);
         case 4 : $a+=($url[$k+3]<<24);
         case 3 : $a+=($url[$k+2]<<16);
         case 2 : $a+=($url[$k+1]<<8);
         case 1 : $a+=($url[$k+0]);
      }
      $mix = mix($a,$b,$c);
      /* report the result */
      return $mix[2];
   }

   //converts a string into an array of integers containing the numeric value of the char
   function strord($string)
   {
      for ($i=0; $i < strlen ($string); $i++)
      {
            $result[$i] = ord ($string{$i});
      }
      return $result;
   }

   //returns -1 if no page rank was found
   function get_page_rank($url)
   {
        $ch = "6".GoogleCH(strord("info:" . $url));

        $pagerank = -1;
        $fp = @ fsockopen ("www.google.com", 80, $errno, $errstr, 10);
        if (!$fp)
        {
            echo "$errstr ($errno)<br />\n";
        }
        else
        {
            $out  = "GET /search?client=navclient-auto&ch=" . $ch .  "&features=Rank&q=info:" . $url . " HTTP/1.1\r\n" ;
            $out .= "Host: www.google.com\r\n" ;
            $out .= "Connection: Close\r\n\r\n" ;
            @ fwrite ($fp, $out);

            while (!feof ($fp))
            {
                $data = @ fgets ($fp, 128);
                $pos  = strpos ($data, "Rank_");

                if ($pos !== false)
                {
                  $pagerank = trim (substr ($data, $pos + 9));
                }
            }
            @ fclose ($fp);
        }
        return $pagerank;
    }
?>