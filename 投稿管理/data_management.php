<?php
    require_once "connect.php";
    require_once "functions.php";
    $data = get_posts($pdo,0);//渡す値に応じて表示するい投稿を変化
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <h2 style="text-align:center;" class="mb-5">投稿管理ページ</h2>
    <div class="container">
        <h4 class="mb-4">タイトル</h4>
        <?php foreach($data as $value):?>
            <form action="delete_post.php" method="post">
            <p class="mb-4"><?=$value["title"]?><button type="submit" style="position:fixed;right:10%;" class="<?=$value["title"]?> btn btn-outline-primary" value="<?=$value["title"]?>" onclick="input_confirm()">投稿を削除</button></p>
            
            <input type="hidden" name="post_title" value="<?=$value["title"]?>">
            </form>
        <?php endforeach;?>


    </div>
</body>
</html>