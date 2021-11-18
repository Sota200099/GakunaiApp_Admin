<?php
    require_once "connect.php";
    require_once "functions.php";
    session_start();
    $logs=[];
    $PAGE_MAX=10;
    $now_data_max =max_id($pdo)+10;//現在の最大idの取得
    $page_numbers =ceil($now_data_max/10);

    //現在のページの取得
    if(!isset($_GET["page_num"])){
        $current_page=1;
    }else{
        $current_page=$_GET["page_num"];
    }

    $paging_id = (($current_page-1)*$PAGE_MAX);//開始indexの作成


    
    //直近10件を降順で表示
    $stmt = get_posts($pdo,$paging_id);

    while($data =$stmt->fetch()){
        $logs[]=$data;
    }


?>
<script src="js/functions.js"></script>
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
    <script src="js/paginathing.min.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>   
    <h2 style="text-align:center;" class="mb-5"><a href="./index.php">投稿管理ページ</a></h2>
    <div class="container">
        <h4 class="mb-4"><?=$current_page?>ページ目</h4>
        <div class="paging">
            <?php foreach($logs as $value):?>
                <form action="delete_post.php" method="post">
                    <?php if($value["delete_flag"]==0):?>
                        <div class="mb-5 card">
                        <span class="card-body" style="width:70%;height:20%;font-size: 1.3em;">タイトル：<?=$value["title"]?>　投稿者：<?=$value["name"]?>　<?=$value["date"]?></span>
                        <p class="ml-4" style="font-size: 1.3em;">本文：　<?=$value["text"]?></p>
                        <button type="submit" class="<?=$value["title"]?> btn btn-outline-dark" value="<?=$value["title"]?>" onclick="return input_confirm()">投稿を削除</button>
                        </div>
                    <?php endif?>
                    <input type="hidden" name="post_title" value="<?=$value["title"]?>,<?=$value["name"]?>,<?=$value["date"]?>">
                </form>
            <?php endforeach;?>
        </div>
    </div>

    <?php $previous = ($current_page-1);$next =($current_page+1);
        if($previous<1){
            $previous =1;
        }
        if($next>$page_numbers){
            $next = $page_numbers;
        }
        
        
        $page_start=$current_page-2;//現在のページの2個前まで
        $page_amount = $current_page+2;//現在のページから2つ後まで
    
        if($page_start<1){
            $page_start=1;
        }
        if($page_amount>$page_numbers){
            $page_amount=$page_numbers;
        }
        //不足ページを追加
        if($current_page==1){
            $page_amount=$current_page+4;
        }
        if($current_page==2){
            $page_amount=$current_page+3;
        }
        if($current_page==3){
            $page_amount=$current_page+2;
        }
        
        //不足ページを追加
        if($current_page==$page_numbers){
            $page_start=$page_numbers-4;
        }
        if($current_page==$page_numbers-1){
            $page_start=$page_numbers-4;
        }
        if($current_page==$page_numbers-2){
            $page_start=$page_numbers-4;
        }
    ?>
    <div class="pagination container d-flex justify-content-center" style="margin-top: 4%;">
        <?php echo '<button class="btn btn-primary mr-5 page-item"><a href ="./data_management.php?page_num='.($previous).'" style="color:white;">'."前へ".'</a></button>'?>
        <div class="buttons" style="text-align: center;">
            <?php for($i=$page_start;$i<=$page_amount;$i++){

                echo '<button class="btn btn-primary mr-5 page-item"><a href ="./data_management.php?page_num='.$i.'" style="color:white;">'.$i.'</a></button>';
            }
            ?>
        </div>

        <?php echo '<button class="btn btn-primary mr-2 page-item "><a href ="./data_management.php?page_num='.($next).'" style="color:white;">'."次へ".'</a></button>'?>
    </div>

</body>
</html>