<?php 

if (is_file('config.php')) {
	
	require_once 'config.php';
	
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
	$pdo = new PDO('mysql:host='.DB_HOSTNAME.';dbname='.DB_DATABASE,DB_USERNAME,DB_PASSWORD,$options );
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage());
}
}
/**
*
*@return assoc array
*@example select('oc_category','where category_id < 6','parent_id')
*/
function select( $table, $where = false, $column = false ){
	global $pdo;
		$sql = 'SELECT ';
		$column ? $sql .= $column : $sql .= '*';
		$sql .= ' FROM '. $table;
		$where ? $sql .= ' ' . $where : null;

	return $pdo->query($sql)->fetchAll();
}



















