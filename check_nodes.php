<?php
$current = $_GET['q'];
$current = str_replace("_"," ",$current);

if($current == null){
	$current = brainiac;
}

//loads all available nodes into array
$dir = 'nodes/';
$av_nodes = scandir($dir);
unset($av_nodes[0]);
unset($av_nodes[1]);
$av_nodes = array_values($av_nodes);
foreach($av_nodes as &$i){
	$i = substr($i, 0, -4);
}

//loads current node into xml parser
$current_file = $dir . $current . ".xml";
$node = simplexml_load_file($current_file);

$yeah = ($node->entries->entry->body);

print_r(node_detect($yeah,$av_nodes));

function node_detect($html_body, $nodes){

	for($i=0; $i<count($nodes); $i++){
		$underscore_nodes[$i] = str_replace(" ","_",$nodes[$i]);
	} 

	for($i=0; $i<count($nodes); $i++){
		if($av_nodes[$i]!=$underscore_nodes[$i]){
			$html_body = str_replace($nodes[$i],$underscore_nodes[$i],$html_body);
		}
	}

	$tok = strtok($html_body," /-\\?!.,;:'");
	while ($tok !== FALSE)
	{
	  $toks[] = $tok;
	  $tok = strtok(" /-\\?!.,;:'");
	}
	
	foreach($toks as $i) {
		foreach($underscore_nodes as $j) {
			if($i==$j||$i==ucfirst($j)){
				$confirmed[]=$j;
			}
		}
	}
	return $confirmed;
}




?>
