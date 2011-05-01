#!/usr/bin/php
<?php
$file_path = '';
if (@$_SERVER['TM_MODE'] != 'CSS') {
	error_reporting(E_ALL);
	echo '<p style="color:crimson">Testing mode (probably). Some things like links might not work.</p>';
	$file = 
	'.sel1 {
		attr1: val1; z-index: 100; attr2:val2;
	}
	#sel1-5 {attrs:vals;}
	.sel2 {
	z-index: 99;
	attr4:val4;
	}
	.sel3, #sel3 {z-index : 101; attr5:val5;}
/* 
	@group Sel: 4
*/
	.sel4-1, /* @todo something **important** (in czech: něco důležitého) */
	.sel4 {;z-index  :101}';
} else {
	error_reporting(E_WARNING);
	$file_path = getenv('TM_FILEPATH');
	$file = file_get_contents($file_path);
}
?>
<html>
<head>
	<title>z-index^2</title>
	<style>
		body {font-family: "Lucida Grande"; font-size:13px;}
		h1 {font-size:16px; line-height:30px; border-bottom:1px solid #ccc}
		
		small {color:#666; font-size:11px; float:right;}
		a {color:inherit;}
		table {border-collapse: collapse;}
		th, td {padding: 3px 5px; font-size: 12px;}
		th {padding-right: 40px; text-align: left; font-weight: normal;}
		th + td {text-align: right;}
		tr:nth-child(even) {background-color: #EDF3FE;}
</style>
</head>

<body>
	<h1>z-index<sup>2</sup> <small><?php echo array_pop(explode('/', $file_path)) ?></small></h1>
	<div>
		
		<h2>By order in code</h2>
	
<?php 

$res = array();
$i = 0;
preg_match_all('/([^{^}]+){[^{^}]*z\-index[\s]*:[\s]*(\d+)[\s]*[;|}]/m', $file, $res);

// reorganize result
for ($i = 0; $i < count($res[0]); $i++) {
	$z = $res[2][$i];
	$line = (substr_count($file, "\n", 0, strpos($file, $res[1][$i])+strlen($res[1][$i]))+1); // @todo fails with multiline comments but approx. ok :-)
	$selector = trim(preg_replace('/\s+/', ' ', preg_replace('/\/\*[\s\S]*?\*\//ms', '', $res[1][$i]))); // not really sure how is [\s\S]*? better than .*
	$index [] = array(
		's' => $selector,
		'z' => $z,
		'l' => $line
		);
}

function echo_html_table ($index) {
	
	global $file_path;
	
	echo '<table>'."\n";
	foreach ($index as $i) {
	
		echo '<tr>';	
		echo '<td><small><a href="txmt://open/?url=file://'. $file_path .'&line='. $i['l'] .'">'. $i['l'] .'</a></small></td>';
		echo '<th>'.$i['s'].'</th>'."\n"; 
		echo '<td>'.$i['z'].'</td>'."\n";
		echo '</tr>'."\n\n";

	}
	echo '</table>';

}

// echo 1
echo_html_table ($index);

?>

<h2>By z-index size</h2>

<?php

function cmp($a,$b) {
	if ($a['z'] == $b['z']) return 0;
	return ($a['z'] < $b['z']) ? -1 : 1;
}

usort($index, 'cmp');

// echo 2
echo_html_table (array_reverse($index));

?>


	</div>
</body>
</html>