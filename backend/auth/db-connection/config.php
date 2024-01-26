<?php

//Database connection config

$config = array(
    'db_hostname' => 'localhost',
    'db_name' => 'reactcrud-non-jwt',
    'db_username' => 'root',
    'db_password' => '',
);

try 
{
    $connection = new PDO("mysql:host=" . $config['db_hostname'] . ";dbname=" . $config['db_name'], $config['db_username'], $config['db_password']);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} 
catch(PDOException $e) 
{
    die("Connection failed: " . $e->getMessage());
}

?>
