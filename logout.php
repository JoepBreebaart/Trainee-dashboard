<?php 
require_once 'core/init.php';

$gebruiker = new Gebruiker();
$gebruiker->logout();

Redirect::to('index.php');