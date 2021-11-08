<?php

require_once('config.php');

function Attendance()
{
    $db_connection = DB_CONNECTION;
    $db_name = DB_DATABASE_ATTENDANCE;
    $db_host = DB_HOST;
    $db_user = DB_USERNAME;
    $db_password = DB_PASSWORD;
    $dsn = "{$db_connection}:dbname={$db_name};host={$db_host}";
    try{
        $pdo = new PDO($dsn, $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
    catch (PDOException $e) {
        header('Location: error_page.php');
        exit;
    }
}