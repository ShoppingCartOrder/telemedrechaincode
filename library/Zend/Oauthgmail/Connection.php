<?php
if (version_compare(phpversion(), "5.3.0", ">=")  == 1)
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
else
error_reporting(E_ALL & ~E_NOTICE); 
$sClientId = '965219841522.apps.googleusercontent.com';
$sClientSecret = 'WLlBRGkS4nVwQOsQARhj-fzd';
//$sCallback = 'http://azurroware.com/mybb/Cvs/importgmail';  
$sCallback = 'http://local.vivahaayojan.com/Invites/gmailcontact';  
$iMaxResults = 50; 
$sStep = 'auth'; 
$_SESSION['addguest_event'];
?> 