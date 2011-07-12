<?php
header('Content-Type: image/svg+xml');
//header('Content-Type: text/plain');

$dir = '../nodes/';
$av_nodes = scandir($dir);
unset($av_nodes[0]);
unset($av_nodes[1]);
$av_nodes = array_values($av_nodes);
foreach($av_nodes as &$i){
	$i = substr($i, 0, -4);
}


function node_detect($html_body, $nodes){

	for($i=0; $i<count($nodes); $i++){
		$underscore_nodes[$i] = str_ireplace(" ","_",$nodes[$i]);
	} 

	for($i=0; $i<count($nodes); $i++){
		if($nodes[$i]!=$underscore_nodes[$i]){
			$html_body = str_ireplace($nodes[$i],$underscore_nodes[$i],$html_body);
		}
	}

	$tok = strtok($html_body," /-\\?!.,;:'");
	while ($tok !== FALSE)
	{
	  $toks[] = $tok;
	  $tok = strtok(" /-\\?!.,;:'");
	}
	
	if($toks){

	foreach($toks as $i) {
		foreach($underscore_nodes as $j) {
			if(strcasecmp($i,$j)==0){
				$confirmed[]=$j;
			}
		}
	}

	}
	if (!$confirmed) {
                return array();
       } else {
	       return $confirmed;
       }
}


function getListOfNodes() {
        /***
         * Should return an array of the form
         * array(
         *     'NODE_NAME' => array (
         *         'NODE_REF1',
         *         'NODE_REF2',
         *         ...
         *         'NODE_REFN'
         *     ),
         *     ...
         * )
         ***/
         /*return array(
                "Test Node A" => array("Test Node C","Test Node D","Test Node F"),
                "Test Node B" => array("Test Node A","Test Node H"),
                "Test Node C" => array("Test Node I","Test Node J","Test Node K"),
                "Test Node D" => array(),
                "Test Node E" => array("Test Node A","Test Node L"),
                "Test Node F" => array("Test Node G"),
                "Test Node G" => array(),
                "Test Node H" => array("Test Node B"),
                "Test Node I" => array(),
                "Test Node J" => array("Test Node E","Test Node G"),
                "Test Node K" => array(),
                "Test Node L" => array(),
         );*/
         
        $refs = array();
        
        global $av_nodes,$dir;
        
        foreach ($av_nodes as $node) {
            $node_file = $dir . $node . '.xml';
            $node_xml = simplexml_load_file($node_file);
            $body = "";
            foreach ($node_xml->entries->entry as $ent) {
                $body = $body . $ent->body;
            }
            $node_refs = node_detect($body,$av_nodes);
            //echo $node . "\n";
            //print_r($node_refs);
            $node = str_replace(" ","_",$node);
            $refs[$node] = array();
            foreach ($node_refs as $node_ref) {
                if (!in_array($node_ref,$refs[$node]) && $node != $node_ref) {
                    array_push($refs[$node],$node_ref);
                }
            }
        }
        
        return $refs;
}

function getCenterNode() {
    if (isset($_GET['node'])) {
        return $_GET['node'];
    } else {
        return "brainiac";
    }
}

function fixRefArray($refs) {
    foreach ($refs as $nodeName => $nodeRefs) {
        foreach ($nodeRefs as $ref) {
            //echo($nodeName . " // " . $ref . "\n");
            if (!in_array($nodeName,$refs[$ref])) {
                //echo("\t" . $nodeName . " not in ref[" . $ref ."]\n");
                array_push($refs[$ref],$nodeName);
                //echo(in_array($nodeName,$refs[$ref]));
            }
        }
    }
    return $refs;
}

function nodeAt($name,$xPos,$yPos) {
    return
        '<circle cx="' . $xPos . '" cy="' . $yPos . '" r="18" ' .
        'style="fill:#66f;fill-opacity:0.75" id="' . fixName($name) . '-circle"'.
        ' onmouseover="hoverIn(\'' . fixName($name) . '\')"'.
        ' onmouseout="hoverOut(\'' . fixName($name) . '\')"'.
        ' onclick="goToPage(\'' . fixName($name) . '\')" />' . "\n" .
        '<text x="' . $xPos . '" y="' . ($yPos + 3) . '" font-size="12px" ' .
        'style="font-family: Helvetica; font-weight: bold" ' .
        'font-weight="normal" text-anchor="middle"'.
        ' onclick="goToPage(\'' . fixName($name) . '\')" >' . "\n\t" . $name . "\n" .
        '</text>' . "\n";
}

function lineTo($x1,$y1,$x2,$y2) {
    return
        '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 .
        '" stroke="#0000ff" style="stroke-opacity:0.2" stroke-width="2" />' . "\n";
}

function makeSVGHeader() {
    include("map.include.svg");
}

function fixName($name) {
    return str_replace(" ","_",$name);
}

function makeSVGFooter() {
    return '</svg>';
}

$arr = getListOfNodes();
//print_r($arr);
$arr = fixRefArray($arr);

//print_r($arr);

//exit(0);

$rings = array(
    0 => array(getCenterNode()),
    //0 => array("Test Node A"),
    1 => array()
);
$currentRing = 1;
$visited = array();
$toSearch = array();

// My apologies to Dijkstra

$toSearch[] = getCenterNode();
//$toSearch[] = "Test Node A";

while (count($toSearch) != 0) {
    $current = array_shift($toSearch);
    array_push($visited,$current);
    foreach ($arr[$current] as $ref) {
        if (!in_array($ref,$visited)) {
            array_push($toSearch,$ref);
        }
    }
    $found = 0;
    foreach ($rings[$currentRing-1] as $pRingElm) {
        if (in_array($current,$arr[$pRingElm])) {
            // Connected node in previous ring
            $found = 1;
            break;
        } else if ($current == $pRingElm) {
            // Self in previous ring
            $found = 2;
            break;
        }
    }
    if ($found == 1) {
        if (!in_array($current,$rings[$currentRing]))
            array_push($rings[$currentRing],$current);
    } else if ($found == 0) {
        $currentRing += 1;
        $rings[] = array();
        if (!in_array($current,$rings[$currentRing]))
            array_push($rings[$currentRing],$current);
    }
}

$ringDist = 100;

$coords = array();

$minX = 0;
$maxX = 0;

$minY = 0;
$maxY = 0;

for ($i = 0; $i < count($rings); ++$i) {
    for ($j = 0; $j < count($rings[$i]); ++$j) {
        $angle = 2 * M_PI * $j / count($rings[$i]);
        $radius = $i * $ringDist;
        
        $theX = $radius * cos($angle);
        $theY = $radius * sin($angle);
        
        if ($theX > $maxX)
                $maxX = $theX;
        if ($theX < $minX)
                $minX = $theX;
        if ($theY > $maxY)
                $maxY = $theY;
        if ($theY < $minY)
                $minY = $theY;
        
        $coords[$rings[$i][$j]] = array("x" => $theX, "y" => $theY);
    }
}

$SVGwidth = $maxX - $minX + 100;
$SVGheight = $maxY - $minY + 100;

$centerX = abs($minX - 50);
$centerY = abs($minY - 50);

makeSVGHeader();

$edges = array();

foreach ($arr as $node => $refs) {
    echo '<g id="' . fixName($node) . '-lines">';
    foreach ($refs as $ref) {
        echo lineTo($coords[$node]["x"]+$centerX,$coords[$node]["y"]+$centerY,
                $coords[$ref]["x"]+$centerX,$coords[$ref]["y"]+$centerY);
    }
    echo "</g>\n\n";
}

foreach ($coords as $node => $point) {
    echo nodeAt($node,$point["x"]+$centerX,$point["y"]+$centerY);
}

echo makeSVGFooter();

//print_r($coords);

?>