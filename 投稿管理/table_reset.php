<?php

require_once "./components/connect.php";
require_once "./components/functions.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Document</title>
</head>
<body class="mt-5 bg-light">
    <div class="container bg-light pt-4 mb-5">
        <h3 >テーブルの初期化</h3>
        <p class="mt-5 pb-5 ">掲示板に関連したテーブル初期化</p>
        <form action="reset.php" class="mt-5 float-right">
            <button class="btn btn-primary">テーブルを初期化</button>
        </form>
    </div>
</body>
</html>