<?php

require_once "connect.php";
require_once "functions.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>Document</title>
</head>
<body class="bg-lsBlue">
    <div class="container">
        <h3 style="background-color: lightskyblue;">テーブルの初期化</h3>
        <p class="mt-5">掲示板に関連したテーブル初期化</p>
        <form action="reset.php" class="mt-5 float-right">
            <button class="btn btn-primary">テーブルを初期化</button>
        </form>
    </div>
</body>
</html>