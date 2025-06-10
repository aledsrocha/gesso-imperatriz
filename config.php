<?php 

    session_start();
    $base = 'http://localhost/controle_de_estoque';

    $db_name = 'controledeestoque';
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';

    try {
        $pdo = new PDO("mysql:dbname=$db_name;host=$db_host;", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
         die("Could not connect to the database $db_name :" . $e->getMessage());

        
    }

   
 ?>