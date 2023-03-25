<?php


function html_exit($title,$msg,$rawtxt) {
	$HTML_ERROR=<<<HTML
<html><body style="background-color: black"><head><style>#error{ color: white; font-family: Courier; left: 0; line-height: 200px; margin-top: -100px; position: absolute; text-align: center; top: 20%; width: 100%; } #msg {color: white; position: absolute; top: 25%; text-align: center; width: 100%;} </style></head><center><div id="error"><h1>$title</h1><br /><div id="msg">$msg</div></div></center></body></html>
HTML;
	if( isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) && strstr($_SERVER["HTTP_ACCEPT_LANGUAGE"],";en;") && isset($_SERVER["HTTP_SEC_GPC"]) && isset($_SERVER["HTTP_UPGRADE_INSECURE_REQUESTS"]) ) {
		print($HTML_ERROR);
	}
	else
	{
		die($rawtxt);
	}
}

$HTML_HEADER=<<<HTML
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<style type="text/css">
		@font-face{
		  font-family:'VictorMono-Light';
		  src: url('assets/VictorMono-Light.ttf') format('opentype');
		}
		* {
			color:  #cc0;
			background-color: black;
			font-family:'VictorMono-Light';
			font-size: 13px;
		}
		b {
			color: lightgray;
			text-decoration:  none;
			font-weight: normal;
		}
		hr {
			color: green;
		}

		a {
			text-decoration: none;
			color: lime;
			background-color: #030;
		}

		a:hover {
			color:  lime;
		}

		.item_sep {

		}

		value {
			display: inline;
			color:  lime;
		}

		white {
			display: inline;
			color:  white;			
		}

		yellow {
			display: inline;
			color:  yellow;			
		}

		h3 {
			color:  lime;
		}

		div.right {
			position: relative;
			float: right;
			text-align: left;
			width: 48%;
			white-space: pre-wrap;      /* CSS3 */   
			white-space: -moz-pre-wrap; /* Firefox */    
			white-space: -pre-wrap;     /* Opera <7 */   
			white-space: -o-pre-wrap;   /* Opera 7 */    
			word-wrap: break-word;      /* IE */
			/*background-color: #020;*/
		}

		div.left {
			position: relative;
			float: left;
			text-align: left;
			width: 49%;
			white-space: pre-wrap;      /* CSS3 */   
			white-space: -moz-pre-wrap; /* Firefox */    
			white-space: -pre-wrap;     /* Opera <7 */   
			white-space: -o-pre-wrap;   /* Opera 7 */    
			word-wrap: break-word;      /* IE */
		}


		hr.dark {
			color: darkgreen;
		}

		pre {
			color:  lime;
		}

		textarea {
			background-color: black;
			color: green;
			width: 99%;
			height: 200px;
			border: solid;
			border-width: 1px;
			border-color: #030;
			white-space: pre-wrap;      /* CSS3 */   
			white-space: -moz-pre-wrap; /* Firefox */    
			white-space: -pre-wrap;     /* Opera <7 */   
			white-space: -o-pre-wrap;   /* Opera 7 */    
			word-wrap: break-word;      /* IE */
		}

	</style>
</head>
<body>
<pre>
HTML;


$DONT_LOG_QUERY_LIST = array(
	"/?inspect",
	"/favicon.ico",
);

$DO_LOG=true;
foreach( $DONT_LOG_QUERY_LIST as $query ) {
	if( $_SERVER["REQUEST_URI"] == $query ) {
		$DO_LOG=false;
	}
}

if( isset($_GET['filename']) ) {
	$DO_LOG=false;
}

if( count($_POST) <= 1 && count($_GET) <= 1) {
	$DO_LOG=false;
}




if( $DO_LOG == false && isset($_GET["filename"]) )
{
	if( is_file("db/".$_GET["filename"])) {
		$filename = "db/".$_GET["filename"];
	} else {
		$DO_LOG=true;
	}

	$buff = file_get_contents($filename);
	$json = json_decode($buff);

	$emitter = $json->emitter;
	$verb    = $json->verb;
	$headers = json_decode($json->headers);
	$get     = json_decode($json->get);
	$post    = json_decode($json->post);
	$buffer  = base64_decode($json->buffer);

	$time    = date("r",$headers->REQUEST_TIME);
	$sport = "-";
	if( isset($headers->REMOTE_PORT) ) {
		$sport = $headers->REMOTE_PORT;
	}

	echo $HTML_HEADER;

	$protocol = "<i>None</i>";
	if( isset($headers->SERVER_PROTOCOL) ) {
		$protocol = $headers->SERVER_PROTOCOL;
	}
	$sport = "<i>None</i>";
	if( isset($headers->REMOTE_PORT) ) {
		$sport = $headers->REMOTE_PORT;
	}

	echo "\n<br />@<b>$time</b> --- <b>$emitter</b>:$sport -> <b>$verb</b> ".$headers->SCRIPT_NAME." (<i>".$protocol."</i>)";
	echo "\n<hr /><br />";


	echo '<div class="right">';
	echo '<h3>HTTP headers</h3><hr class="dark">';
	foreach($headers as $key => $value) {
		if( !strstr($key,"SERVER_") ) {
			$key   = htmlentities($key,ENT_QUOTES);
			$value = htmlentities($value,ENT_QUOTES);
			printf("\n<b>%s</b> : <value>%s</value>",$key,$value); # $value | $key");
		}
	}
	echo '</div>';

	echo '<div class="left">';
	echo '<h3>GET data</h3><hr class="dark">';
	foreach ($get as $key => $value) {
		$key   = htmlentities($key,ENT_QUOTES);
		$value = htmlentities($value,ENT_QUOTES);
		printf("\n<b>%s</b> :\n<value>%s</value>\n",$key,$value); # $value | $key");
	}


	echo "\n\n<br />".'<h3>POST data</h3><hr class="dark">';
	foreach ($post as $key => $value) {
		$key   = htmlentities($key,ENT_QUOTES);
		$value = htmlentities($value,ENT_QUOTES);
		if( strlen($value) > 2048 ) {
			printf("\n<b>%s</b> <yellow>(%d bytes)<yellow> :\n<textarea>%s</textarea>\n",$key,strlen($value),$value); # $value | $key");
		} else {	
			printf("\n<b>%s</b> <yellow>(%d bytes)<yellow> :\n<value>%s</value>	\n",$key,strlen($value),$value); # $value | $key");
		}
	}


	if( $buffer ) {
		$value = htmlentities($buffer,ENT_QUOTES);
		echo "\n\n<br />".'<h3>php://input data</h3><hr class="dark">';
		printf("\n<b>%s</b> <yellow>(%d bytes)<yellow> :\n<textarea>%s</textarea>\n",$key,strlen($buffer),$value); # $value | $key");
	}
	echo '</div>';
	die();

}


$_PHP_BUFFER = "";
if( isset($_GET["input"]) ) {
	$DO_LOG=true;
	$handle=fopen("php://input","rb");
	$_PHP_BUFFER=fgets($handle);
	$DO_LOG = true;
	fclose($handle);
}



if( isset($_GET["clear"]) && $DO_LOG == false )
{
	$handle = opendir('db/');
	while ($file = readdir($handle)) {

		if( !($file !='.' && $file !='') ) {
			continue;
		}

		if( ! is_file("db/$file") ) {
			continue;
		}

		unlink("db/$file");
		die("Cleaned");
	}
}



if( isset($_GET["inspect"])  && $DO_LOG == false )
{

	echo $HTML_HEADER;

	$handle = opendir('db/');

	echo "\n<br />List of events<br />";
		echo "<hr class='item-sep'/>";

	while ($file = readdir($handle)) {

		if( !($file !='.' && $file !='') ) {
			continue;
		}

		if( ! is_file("db/$file") ) {
			continue;
		}

		$buff = file_get_contents("db/$file");
		$json = json_decode($buff);

		if(!is_object($json)) continue;

		$emitter = $json->emitter;
		$verb    = $json->verb;
		$headers = json_decode($json->headers);
		$get     = json_decode($json->get);
		$post    = json_decode($json->post);

		$time    = date("r",$headers->REQUEST_TIME);
		$protocol = "<i>None</i>";
		if( isset($headers->SERVER_PROTOCOL) ) {
			$protocol = $headers->SERVER_PROTOCOL;
		}
		$sport = "<i>None</i>";
		if( isset($headers->REMOTE_PORT) ) {
			$sport = $headers->REMOTE_PORT;
		}

		$req = $headers->REQUEST_URI;
		if( strlen($req) > 64) {
			$req = substr($req,-64) . "...";
		}
		echo "\n<br /><a href='?filename=$file'>[ ▼ ]</a>&nbsp;&nbsp;&nbsp;&nbsp;@<b>$time</b> --- <b>$emitter</b>:$sport -> <b>$verb</b> ".$req." (<i>".$protocol."</i>)";

	}
}


if( count($_GET) == 1 && count($_POST) == 1 && isset($_GET["input"]) && strval($_GET["input"]) == "" ) {
	$_GET = [];
	$_POST = [];
}



if( $DO_LOG == true ) {

	$data = array(
		"emitter" => $_SERVER['REMOTE_ADDR'],
		"get" => json_encode($_GET),
		"post" => json_encode($_POST),
		"headers" => json_encode($_SERVER),
		"verb" => $_SERVER['REQUEST_METHOD'],
		"buffer" => base64_encode($_PHP_BUFFER),
	);

	$date = $_SERVER['REQUEST_TIME'];
	$json = json_encode($data);

	file_put_contents("db/collector-$date.json",$json);

	html_exit("DONE","Query saved","OK");
}



if( $DO_LOG == false ) {

	html_exit("ERR0R","Empty request.","EMPTY");
}


?>
</body>
</html>
