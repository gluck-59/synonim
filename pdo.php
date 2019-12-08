<?php
    
// мануал http://phpfaq.ru/pdo
// константы http://fi2.php.net/manual/ru/pdo.constants.php
// транзакции http://fi2.php.net/manual/ru/pdo.transactions.php

error_reporting(E_ERROR);
ini_set('display_errors','On');
ini_set('default_charset', 'utf-8');

$host = 'localhost';
$db = 'synonim';
$charset = 'utf8';
$user = 'root'; // юзер с ограниченными правами для уменьшения checking_permissions
$pass = 'NhbUdjplz';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
);

$pdo = new PDO($dsn, $user, $pass, $opt);
    






//global $pdo;

//echo '<!--classes/pdo.php загружен-->';

/*

echo '<pre>';
//print_r(PDO::getAvailableDrivers());die;



// вставка
//$stmt = $pdo->exec("INSERT INTO `os` (`name`) VALUES('login2')");


// получение всего
//$stmt = $pdo->query('SELECT * FROM os')->fetchAll();



// получение c условиями
$eu = 0;
$stmt = $pdo->prepare('SELECT * FROM countries WHERE isEU = :isEU');
$stmt->execute(array('isEU' => $eu));
while ($row = $stmt->fetch() )
{
//    echo '<img src="../img/flags/'.$row->iso.'.png"><br>';
//    echo $row->iso . ' ';
//    echo $row->name . ' (+'.$row->phone_code.')<br><br><br>';
print_r($row);
}

*/




?>