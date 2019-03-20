<?php 

require 'functions.php';
require 'var.php';
redhawk_banner();
userinput("Enter input directory");
$input_dir = trim(fgets(STDIN, 1024));

$input_file_names = glob($input_dir);

$out_file_names = array();
foreach ($input_file_names as $input_file) { 
	$tmp_out = str_replace(".txt", "-out.txt", $input_file);
	$out_file_names[] = str_replace("input/", "output/", $tmp_out);
}

$pipe = array();

for ($i = 0; $i < count($input_file_names); ++$i)
	$pipe[$i] = popen("php rhawk.php " . $input_file_names[$i] . " " . $out_file_names[$i],  "w");	

for ($i = 0; $i < count($input_file_names); ++$i)
	pclose($pipe[$i]);


?>
