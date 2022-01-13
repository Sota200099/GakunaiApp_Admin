<?php
    define("DB_CONNECTION","mysql");
    define("DB_NAME","postlog");
    define("DB_HOST","localhost");
    define("DB_USER","root");
    define("DB_PASS","");
    $connect = DB_CONNECTION;
    $name = DB_NAME;
    $host = DB_HOST;
    $user = DB_USER;
    $pass = DB_PASS;
    $dsn = "{$connect}:dbname={$name};host={$host};chrset=utf-8";
    try{
        $pdo = new PDO($dsn,$user,$pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    }catch (PDOException $e){
        echo $e->getMessage();
        exit;
    }
?>