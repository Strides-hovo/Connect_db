<?php

namespace classes;

use \PDO;


class Db {

private const HOST = 'localhost';
private const USER = 'root';
private const PASS = '';
private const DB_NAME = 'test';
private const OPTIONS = [
  PDO::ATTR_ERRMODE             => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES    => FALSE
];

public $conn;
public $where;
public $select;
public $quantity;



 public function __construct()
{
  try{
      $this->conn = new PDO('mysql:host='.self::HOST.';dbname='.self::DB_NAME,self::USER,self::PASS,self::OPTIONS );
  }
  catch(PDOExeption $e){
      throw  $e->getMessage();
  }
}




/**
 * select('category'); select * from category
 * select(['category' => ['id','name']]); select id,name from category
 */

public function select( $table )
{
  $str = '';
  if(is_string( $table )){
    $str .= 'SELECT * FROM ' . $table;
  }elseif( is_array( $table ) ){
    foreach( $table as $k => $v ){
      $str .= 'SELECT ' . implode( ',' ,$v) . ' FROM ' . $k;
    }
  }
  $this->select = $str;
  return $this;
}

/**
 * where( ['id' => 4, 'name' => 'mark'] )
 * where('id = 2')
 * @return string
 */

public function where(  $where )
{
  $str = '';
  if(is_array($where)){
    $i = 0;
      foreach ($where as $k => $v) {  
        if( $i < 1 )
          $str .= ' WHERE ' . $k . ' = ' . $v;
        else
          $str .= ' AND ' . $k . ' = ' . $v;
      $i++;
      }
  }else{
    $str .= ' WHERE ' . $where;
  }
  $this->where = $str;
  return $this;
}

/**
 * get PDO::fetch
 * @return string
 */
 public function one()
{
  $this->quantity = 'fetch';
  return $this->req();
}



/**
 * get PDO::fetchAll
 * @return string
 */

public function all()
{
  $this->quantity = 'fetchAll';
  return $this->req();
}



/**
 * set request
 * @return array
 */

 public function req()
{
   $sql = $this->select . $this->where;
   $res = $this->conn->prepare( $sql );
   $res->execute();
   $req = $this->quantity;
   return $res->$req();
}


 public function insert( $data )
{
  # code...
}


}