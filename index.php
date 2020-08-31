<?php

error_reporting(E_ALL);


require 'functions.php';
require 'yxml.php';
require 'classes/Db.php';



$oop_conn = new classes\Db();


$categories = $oop_conn->select( 'category')->all();





