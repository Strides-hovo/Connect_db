<?php

namespace classes;

use \PDO;


class Db {

private static $HOST = 'localhost';
private static $USER = 'root';
private static $PASS = 'root';
private static $DB_NAME = 's_red_rect';
private const OPTIONS = [
  PDO::MYSQL_ATTR_FOUND_ROWS   => TRUE,
  PDO::ATTR_ERRMODE             => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES    => FALSE
  
];

public $conn;
public $where = null;
public $select = null;
public $quantity  = null;
public $insert = null;
public $left_join = null;
public $query = null;
public $orderBy = null;
public $rowCount = null;
// public $lastId;

 public function __construct( $host,$user,$pass,$dbName ){

    self::$HOST    = $host;
    self::$USER    = $user;
    self::$PASS    = $pass;
    self::$DB_NAME = $dbName;

  try{
      $this->conn = new PDO('mysql:host='.self::$HOST.';dbname='.self::$DB_NAME,self::$USER,self::$PASS,self::OPTIONS );
      // $this->lastId = $this->conn->lastInsertId();

  }
  catch(PDOExeption $e){
      throw  $e->getMessage();
  }
}








/**
 * select('category'); select * from category
 * select(['category' => ['id','name']]); select id,name from category
 * @return string
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
 * where("privilegiya != 'admin'")
 * @return string
 */

public function where(  $where )
{
  $str = '';
  if(is_array($where)){
    $i = 0;
      foreach ($where as $k => $v) {  
        if( $i < 1 )
          $str .= ' WHERE ' . $k . ' = ' . "'$v'";
        else
          $str .= ' AND ' . $k . ' = ' . "'$v'";
      $i++;
      }
  }else{
    $str .= " WHERE $where";
  }

  $this->where = $str;
  return $this;
}


/*
* orderBy(id,ASC)
*/
public function orderBy( $orderBy )
{
    $this->orderBy =  ' ORDER BY ' . key($orderBy) . ' '. current($orderBy);
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
   $sql  = ($this->select) ?? null;
   $sql .= ($this->where)  ?? null;
   $sql .= ($this->left_join) ?? null;
   $sql .= ($this->orderBy) ?? null;
   // $sql .= ($this->query)  ?? null;

   $res  = $this->conn->prepare( $sql );
   $res->execute();
   $req = $this->quantity;

   $result = $res->$req();

   return $result;
}




/*
* return query->rowCount()
*/
public function replace( $table, $colums, $data ){

    $ins = '';
    $items = [];
    $request = []; 

    if( is_string($colums) ){
      $colum = $colums;
    }elseif (is_array($colums)){
      $colum = implode(', ',$colums);
    }else return false;

    foreach ( $data as $key => $value ) {
      $ins .= "?,";
      if( is_array($value) ) 
            $items[] = $value;
      else 
            $items[] = array_fill( 0, 1, $value );  
      }

      $ins = rtrim($ins,',');
      $ins = "( {$ins} )";
      $sql = "REPLACE INTO `{$table}` ($colum) VALUES $ins";
         
    if( !empty( $items ) ){
      $query = $this->conn->prepare( $sql );
      for($i = 0; $i < count( $items[0] ); ++$i){
          for( $s = 0; $s < count( $items ); ++$s ){
              $request[$i][$s] = $items[$s][$i];
          }
      }

      $res = 0;
      foreach ( $request as $key => $execute ) {
        
        if( $query->execute( $execute ) ){

          $res += $query->rowCount();
        }else return false;
      }
    }
  return $res;
}



/**
 * $table string
 * $colums string|array
 * $data array
 * @example insert('category',['name',status'],[$name,$status]);
 * @example insert('category','name,status',[$name,1]);
 * @return count insert_row
 */

 public function insert( $table, $colums, $data )
{
    $ins = '';
    $items = [];
    $request = []; 

    if( is_string($colums) ){
      $colum = $colums;
    }elseif (is_array($colums)){
      $colum = implode(', ',$colums);
    }else return false;

    foreach ( $data as $key => $value ) {
      $ins .= "?,";
      if( is_array($value) ) 
            $items[] = $value;
      else 
            $items[] = array_fill( 0, 1, $value );  
      }

      $ins = rtrim($ins,',');
      $ins = "( {$ins} )";
      $sql = "INSERT INTO `{$table}` ($colum) VALUES $ins";
         
    if( !empty( $items ) ){
      $query = $this->conn->prepare( $sql );
      for($i = 0; $i < count( $items[0] ); ++$i){
          for( $s = 0; $s < count( $items ); ++$s ){
              $request[$i][$s] = $items[$s][$i];
          }
      }
      $res = 0;
      foreach ( $request as $key => $execute ) {
        if($query->execute( $execute ) ){
          $res += $query->rowCount();
        }else return false;   
      }
    }
  return $res;
}







/**
 * $table string
 * $colums string|array
 * $data array
 * $where array
 * @example update('category',['name',status'],[$name,$status]);
 * @example update('category','name,status', $name, 1 );
 * @example update('category','status', $status, ['id' => $_POST['task_id']]);
 * @return count update_row
 */
public function update( $table, $colums, $data, $where )
{

    $sql = "UPDATE $table SET";
    $val = '';
    $type = 'array';

      if ( is_string( $colums )) 
          $sql .= " {$colums} = '{$data}'";

      else if( is_array( $colums )){
        $count = count( $colums );
        for ( $i = 0; $i < $count; $i++ ) {
          $sql .= " {$colums[$i]} = '{$data[$i]}',";
        }
      }

    $sql = rtrim($sql,',');
    $where = key($where) . ' = '.implode(',',$where);
    $sql .= " WHERE $where";

    $result =  $this->conn->prepare( $sql )->execute();

    return $result;
}







public function left_join( $table1, $column1,$table2, $column2 )
{
  $res = "SELECT * FROM `production` 
          LEFT JOIN foremens 
          ON production.foremen_id = foremens.id";
   $sql = " LEFT JOIN {$table2} ON {$table1}.{$column1} = {$table2}.{$column2}";
$this->left_join = $sql;
return $this;
}











public function query( $query, $type = 'fetchAll' ){
    $this->query = $query;
    $res  = $this->conn->prepare( $query );
    $res->execute();
  
    $req = $type;

    $result = $res->$req();

   return $result;
}



/**
 * $from string
 * $where string
 * @example delite('category',"name = 'polo'" );
 * @return count update_row
 */

public function delete( $from, $where )
{
  $sql = "DELETE FROM $from WHERE ";
  if (is_string($where) ) {
      $sql .= $where;
  }else{
    foreach ($where as $k => $w) {
        $sql .= $k . ' = ' . $w;
    }
    
  }

    $this->conn->query( $sql );
}





}
