<?php
require_once(dirname(__FILE__).'/Dovetail.php');
$dove = new Dovetail;

$directory = (isset($_GET['dir']) && is_dir($_GET['dir']) ? $_GET['dir'] : dirname(dirname(__FILE__)).'/hermes-db/' );

if(!is_dir($directory)){ print $directory." is not an directory."; exit; } else { print $directory." is a valid directory. <br/>"; }

print '<pre>';
$list = scandir($directory);
print_r($list);
print_r( $dove->open_directory($directory, TRUE, TRUE) );

print '<strong>COUNT</strong> (<strong>UNIQUE</strong>)<br/>'; foreach(array(FALSE, 'identity', 'HTTP_USER_AGENT', 'HTTP_REFERER', 'REMOTE_ADDR', 'HTTP_ACCEPT_LANGUAGE') as $act){ print ($act == FALSE ? '<em>(ALL)</em>' : $act)." = ".$dove->count($act).' ('.$dove->count($act, TRUE).")<br/>"; }

print "\$p = "; print_r($dove->count("p", NULL));
print '</pre>';


print '<pre>'; print_r($dove); print '</pre>';
?>
