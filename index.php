<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

$cmd = isset($_POST["q"])?$_POST["q"]:"";
if($cmd == "")
	$cmd = isset($_GET["q"])?$_GET["q"]:"";

$isAjax = isset($_POST["a"])?$_POST["a"]:"";
$rss = isset($_GET["rss"])?$_GET["rss"]:false;

function todo($cmd) {
	$cmd = preg_replace("/[\"&;]/", "", $cmd);
	exec("./todo.sh -f -p $cmd", $out);
	$out = implode("<br>", $out);
	return $out;
}

function style($desc) {
	$desc = preg_replace("/([0-9]{1,3} x [\w -]*)/", "<span class=\"rm\">\${1}</span>", $desc);
	$desc = preg_replace("/([0-9]{1,3} \(([A-Z])\)( [\w -]*|))/", "<span class=\"prio-\${2}\">\${1}</span>", $desc);
	$desc = preg_replace("/ ([@][\w:]+)/", " <span class=\"context\">\${1}</span>", $desc);
	$desc = preg_replace("/ ([\+][\w:]+)/", " <span class=\"project\">\${1}</span>", $desc);
	return $desc;
}

if($cmd != "") {
	$out = todo($cmd);
	if($isAjax) {
		echo style($out);
		return;
	}
} else if($isAjax) {
	return;
}

$self = "index.php";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html dir="ltr" lang="en-US">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta charset="UTF-8" />
		<title></title>

		<link rel="stylesheet" href="style.css?v=1" type="text/css" media="all" />
        
        <script src="http://code.jquery.com/jquery-1.5.min.js" type="text/javascript"></script>
		<link rel="alternate" type="application/rss+xml" title="Todo.txt RSS" href="<?php $self ?>?rss" />
</head>
<body>
	<form id="cmd" method="get" action="<?php $self ?>">
	<input type="text" name="q" id="q" value="<?php echo ($cmd!=""?$cmd:"list"); ?>" autocomplete="off" />
	<input type="submit" id="s" value="exec" />
</form>
<script>
$(document).ready(function(){
	$("#cmd").submit(function(){
		$("#out").load("<?php echo $self ?>", {q: $("#q").val(), a: 1});
		$("#q").val("")
		return false;
	});
	$("#cmd").submit();
	$("#q").focus();
});
</script>
<pre id="out">
</pre>
</body>
</html>
