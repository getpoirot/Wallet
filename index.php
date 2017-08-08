<?php
require_once __DIR__.'/vendor/autoload.php';

$wallet =new Poirot\Wallet\Entity\EntityWallet(array("uid"=>13,"wallet_type"=>"gold","amount"=>-3,"source"=>"another user"));
$db = new PDO(
    'mysql:host=localhost;
    dbname=testdb;
    charset=utf8mb4',
    'root',
    '');

$test=new \Poirot\Wallet\Repo\RepoMysqlPdo($db);
var_dump($test->insert($wallet));

krumo($test->getCountTotalAmount("13","gold"));