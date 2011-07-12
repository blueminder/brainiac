<?php require_once('vars.php') ?>

<html>
<head>
<script type="text/ecmascript"><![CDATA[

function goToPage(node) {
    parent.top.location = </php echo($base) ?>+node;
}

]]></script>
</head>
<?php

$title = filter_var(stripslashes( $_POST['titleField'] )) ;
$body = filter_var(stripslashes( $_POST['FCKeditor1'] )) ;
$date = strftime("%A, %B %d, %G - %R");
$body = str_replace("<","&lt;",$body);
$body = str_replace(">","&gt;",$body);
$body = str_replace("&nbsp;"," ",$body);
$op = filter_var(stripslashes( $_POST['submitButton'] )) ;

$myFile = "nodes/" . $title . ".xml";

if($op=="Create Node"){

$xmlstr = <<<XML
<?xml version='1.0' standalone='yes'?>
<node>
	<title>$title</title>
	<entries>
		<entry>
			<date>$date</date>
			<body>$body</body>
		</entry>
	</entries>
</node>
XML;


$fh = fopen($myFile, 'w+');
fwrite($fh, $xmlstr);
fclose($fh);

}

if($op=="Create Entry"){

if (file_exists($myFile)) {
    $xml = simplexml_load_file($myFile);
	$new_entry = $xml->entries->addChild('entry');
	$new_entry->addChild('date', $date);
	$new_entry->addChild('body', $body);
	$out_xml = ($xml->asXML());
	
	$fh = fopen($myFile, 'w');
	fwrite($fh, $out_xml);
	fclose($fh);
	
} else {
    exit('Failed to open node for editing.');
}

}


$title = str_replace(' ','_',$title);
//echo $xml_str;
/*
print_r($title);
print_r($body);
print_r($date);
*/
?>
<body onLoad="javascript:goToPage('<?php echo $title; ?>')">

</body>
</html>
