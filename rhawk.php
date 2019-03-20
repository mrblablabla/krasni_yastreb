<?php
# NOTE(dan): This code partly written by php hater so bugs expected.

error_reporting(0);

require 'functions.php';
require 'var.php';
echo $cln;

if (extension_loaded('curl') || extension_loaded('dom')) {

} else {
    if (!extension_loaded('curl')) {
        echo $bold . $red . "\n[!] cURL Module Is Missing! Try 'fix' command OR Install php-curl" . $cln;
    }
    if (!extension_loaded('dom')) {
        echo $bold . $red . "\n[!] DOM Module Is Missing! Try 'fix' command OR Install php-xml\n" . $cln;
    }
}
echo "\n";

$in = @fopen($argv[1], "r");
$out = @fopen($argv[2], "w");


if ($in) {
	$line_num = 1;
	while (($line= fgets($in)) !== false) {
		if (strpos($line, "http://") === 0) {
			$ip = str_replace("http://", '', $line);
			$ip = str_replace(PHP_EOL, '', $ip);
			$ipsl = "http://";
		} else if (strpos($line, "https://") === 0) {
			$ip = str_replace("https://", '', $line);
			$ip = str_replace(PHP_EOL, '', $ip);
			$ipsl = "https://";
		} else {
			# TODO(dan): When line is empty this block executes too, fix it
			echo $red . "Wrong url or empty on line: $line_num, url: $line\n";
			fclose($in); 
			fclose($out); 
			exit();
		}

		$line_num++;

		echo "\n";

		$reallink = $ipsl . $ip;
		$srccd    = file_get_contents($reallink);
		$lwwww    = str_replace("www.", "", $ip);
		echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
		echo $blue . $bold . "[i] Scanning Site:\e[92m $ipsl" . "$ip \n";
		echo $bold . $yellow . "[S] Scan Type : SQL Vulnerability Scanner" . $cln;
		echo "\n\n";
		$lulzurl = $reallink;
		$html    = file_get_contents($lulzurl);
		$dom     = new DOMDocument;
		@$dom->loadHTML($html);
		$links = $dom->getElementsByTagName('a');
		$vlnk  = 0;

		foreach ($links as $link) {
			$found = false;
			$lol = $link->getAttribute('href');
			if (strpos($lol, '?') !== false) {
				echo $lblue . $bold . "\n[ LINK ] " . $fgreen . $lol . "\n" . $cln;
				echo $blue . $bold . "[ SQLi ] ";
				$sqllist = file_get_contents('sqlerrors.ini');
				$sqlist  = explode(',', $sqllist);
				if (strpos($lol, '://') !== false) {
					$sqlurl = $lol . "'";
				} else {
					$sqlurl = $ipsl . $ip . "/" . $lol . "'";
				}
				$sqlsc = file_get_contents($sqlurl);
				$sqlvn = $bold . $red . "Not Vulnerable";
				foreach ($sqlist as $sqli) {
					if (strpos($sqlsc, $sqli) !== false) { 
						$sqlvn = $green . $bold . "Vulnerable!";
						$found = true;
					}
				}
				echo $sqlvn;
				if ($found == true) {
					fwrite($out, $reallink . $lol . "\n");
					break;
				}
				echo "\n$cln";
				echo "\n";
				$vlnk++;
			}
		}

		echo "\n" . $blue . $bold . "[+] URL(s) With Parameter(s): " . $green . $vlnk;
		echo "\n\n";
	}
	fclose($in);
	fclose($out);
} else {
	exit("File not found\n");

} 

?>
