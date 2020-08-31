<?php

function debug( $data, $type = false ){
  
	if( $type ) var_dump( '<pre>', $data, '</pre>' );
  else echo '<pre>' . print_r( $data, true ) . '</pre>';
  
}












