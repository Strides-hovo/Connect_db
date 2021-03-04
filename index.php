<?php

function debug( $data, $type = false ){
	if( $type ) var_dump( '<pre>', $data, '</pre>' );
  else echo '<pre>' . print_r( $data, true ) . '</pre>';
}



require 'classes/Db.php';

$name   = ['make','join','simone','bob','pop'];
$status = [ null,5, null, 8];



$oop_conn = new classes\Db();


// $categories = $oop_conn->select( 'category')->all();

$ins = $oop_conn->insert('test','name,status',[$name,1]);


debug( $ins );
