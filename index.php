<?php

require( dirname(__FILE__) . '/vars.php' );
global $base;

$currentU = $_GET['q'];
$current = str_replace("_"," ",$currentU);

function link_parse($html_body, $nodes)
{
	$back_replace = array("?", "!", ".", ",", ";", ":", "'", "\"", ".\"", ",\"", "'.", "'s", "'d", "'ve", "'m", ")");
	$front_replace = array("'", "\"", "(");
	$both_replace = array("'", "\"");
	//$base = "http://enriquesantos.net/sb/brainiac_alpha1/";
	if(!isset($base)){
		$base='';
	}
	$here = "<a href=\"$base"."?q=";
	
	foreach($nodes as $file_link) {
		if ($file_link != "." && $file_link != "..") {
			$html_body = str_replace(" ".$file_link." ", " ".$here."".str_replace(" ", "_", $file_link)."\">".$file_link."</a> ", $html_body);
			$html_body = str_replace(" ".nameize($file_link)." ", " ".$here."".str_replace(" ", "_", $file_link)."\">".nameize($file_link)."</a> ", $html_body);
			//Adjust linking around punctuation
			for ($i = 0; $i < count($back_replace); $i = $i + 1){
				$temp_rep = $back_replace[$i];
				$html_body = str_replace(" ".$file_link.$temp_rep, " ".$here."".str_replace(" ", "_", $file_link)."\">".$file_link."</a>".$temp_rep, $html_body);
				$html_body = str_replace(" ".nameize($file_link).$temp_rep, " ".$here."".str_replace(" ", "_", $file_link)."\">".nameize($file_link)."</a>".$temp_rep, $html_body);
			}
			for ($i = 0; $i < count($front_replace); $i++){
				$temp_rep = $front_replace[$i];
				$html_body = str_replace($temp_rep.$file_link." ", $temp_rep.$here."".str_replace(" ", "_", $file_link)."\">".$file_link."</a>"." ", $html_body);
				$html_body = str_replace($temp_rep.nameize($file_link)." ", $temp_rep.$here."".str_replace(" ", "_", $file_link)."\">".nameize($file_link)."</a>"." ", $html_body);
			}
			for ($i = 0; $i < count($both_replace); $i++){
				$temp_rep = $both_replace[$i];
				$html_body = str_replace($temp_rep.$file_link.$temp_rep, $temp_rep.$here."".str_replace(" ", "_", $file_link)."\">".$file_link."</a>".$temp_rep, $html_body);
				$html_body = str_replace($temp_rep.nameize($file_link).$temp_rep, $temp_rep.$here."".str_replace(" ", "_", $file_link)."\">".nameize($file_link)."</a>".$temp_rep, $html_body);
			}
			$html_body = str_replace("(".$file_link.")", "(".$here."".$file_link."\">".str_replace(" ", "_", $file_link)."</a>)", $html_body);
			$html_body = str_replace("(".nameize($file_link).")", "(".$here."".$file_link."\">".str_replace(" ", "_", $file_link)."</a>)", $html_body);
		} 
	}
	return $html_body;
}

//copy pasta from php.net!
function nameize($str,$a_char = array("'","-"," ")){   
    //$str contains the complete raw name string
    //$a_char is an array containing the characters we use as separators for capitalization. If you don't pass anything, there are three in there as default.
    $string = strtolower($str);
    foreach ($a_char as $temp){
        $pos = strpos($string,$temp);
        if ($pos){
            //we are in the loop because we found one of the special characters in the array, so lets split it up into chunks and capitalize each one.
            $mend = '';
            $a_split = explode($temp,$string);
            foreach ($a_split as $temp2){
                //capitalize each portion of the string which was separated at a special character
                $mend .= ucfirst($temp2).$temp;
                }
            $string = substr($mend,0,-1);
            }   
        }
    return ucfirst($string);
}

if($current == null){
	$current = "brainiac";
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
?>
<html>
<head>
<title><?php echo nameize($node->title); ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $base; ?>style.css">
    <script type="text/javascript" src="<?php echo $base; ?>thickbox/jquery-latest.pack.js"></script>
<script type="text/javascript" src="<?php echo $base; ?>thickbox/thickbox.js"></script>
<style type="text/css" media="all">@import "<?php echo $base; ?>thickbox/thickbox.css";</style>
<style>
#mapframe {
    width: 60%;
    height: 70%;
    position: absolute;
    top: 15%;
    left: 20%;
    border: 2px solid black;
    background-color: white;
}

#fubar {
    background-image: url(<?php echo $base ?>bg.png);
    width:100%;
    height:100%;
    top:0px;
    left:0px;
    position: absolute;
    visibility: hidden;
}
</style>
<script>
function showFubar(){
   document.getElementById("fubar").style.visibility="visible";
}

function hideFubar(){
   document.getElementById("fubar").style.visibility="hidden";
}

function getKey(e) {
	var code;
	if(!e) var e = window.event;
	if (e.keyCode) code = e.keyCode; //Modern browsers use the 'keyCode' property to get the key that was pressed
	else if (e.which) code = e.which; //Older Browsers(Netscape) uses the 'which' property to do the same.
	if (e.keyCode==13){
		showFubar();
	}
}
window.onkeypress = getKey;

</script>

</head>
<body onkeypress="getKey(event)">
<div class="top">
<a href="<?php echo $base; ?>">Root Node</a> |
<a href="<?php echo $base; ?>edit.php?title=<?php echo ($node->title); ?>&default=&op=Create Entry&keepThis=true&TB_iframe=true&height=265&width=400" title="Create Entry" class="thickbox">New Entry</a> |

<a href="<?php echo $base; ?>edit.php?title=&default=&op=Create Node&keepThis=true&TB_iframe=true&height=265&width=400" title="Create Node" class="thickbox">New Node</a>

 | <a href="javascript:showFubar()">
        View Map</a>&nbsp;
<?php if (isset($_GET['q'])) { ?>
(<a href="<?php echo $base; ?>svg/mapgen.svg.php?node=<?php echo($currentU) ?>" target="_new"><small>In a New Window</small></a>)
<?php } else { ?>
(<a href="<?php echo $base; ?>svg/mapgen.svg.php" target="_new"><small>In a New Window</small></a>)
<?php } ?>
        </div>
<h1 class="title"><?php echo nameize($node->title); ?></h1>

<?php 
foreach(($node->entries->entry) as $a){
	echo "<div class=\"entry\">";
	$b = link_parse(($a->body), $av_nodes);
	echo $b;
	echo "</div>";
}
?>
<div id="fubar" onClick="javascript:hideFubar()">
<?php if (isset($_GET['q'])) { ?>
<iframe src="<?php echo $base; ?>svg/mapgen.svg.php?node=<?php echo($currentU) ?>" id="mapframe" />
<?php } else { ?>
<iframe src="<?php echo $base; ?>svg/mapgen.svg.php" id="mapframe" />
<?php } ?>
</div>
</body>
</html>
