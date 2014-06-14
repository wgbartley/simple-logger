<?
header('Content-type: text/plain');

if(!isset($_REQUEST['l']))
	die('You forgot l');

if(!isset($_REQUEST['t']))
	$_REQUEST['t'] = date('Y-m-d H:i:s');

if(!isset($_REQUEST['v']))
	$_REQUEST['v'] = 1;

$filename = 'logs/'.$_REQUEST['l'].'.txt';
$data = $_REQUEST['t'].','.$_REQUEST['v'].PHP_EOL;

if(!file_exists($filename))
	touch($filename);

file_put_contents($filename, $data, FILE_APPEND);

echo $data;
?>
