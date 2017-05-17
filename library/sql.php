<?php
error_reporting(0);
	//$config = new JConfig();
    $sql = ADONewConnection($config->dbtype); 
    $sql->debug = $config->debug;
	$sql->autoRollback = $config->autoRollback;
    $sql->PConnect($config->server, $config->user, $config->password, $config->database); 

	$session = new Session();
?>