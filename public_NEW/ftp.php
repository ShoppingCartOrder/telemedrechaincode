<?php
/*
error_reporting(E_ALL );
ini_set('display_errors',1);
var_dump( extension_loaded( 'ssh2' ) );
$con = ssh2_connect ( 'vivahaayojan.com') or die('cannot connect');
die($con);
ftp_login($con, 'cyber', 'weddew123!@#)(*') or die('cannot login');
*/


$options = array
(
    'hostname' => 'localhost',
    'port'     => '8080',
    'path'     => '/solr/wp2'
);

$client = new SolrClient($options);

$query = new SolrQuery();
$query->setQuery('astro');
$query->setStart(0);
$query->setRows(50);
//var_dump($query);
// specify which fields do we want to retrieve

$query->addField('tag_name');

$res = $client->query($query)->getResponse();

// how does he response look like?
var_dump($res);
//var_dump($res->reponse);
?>
