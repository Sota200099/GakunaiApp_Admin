<?php

//クラス名をすべて取得
function Get_class_Id($pdo)
{
    try{
        $sql = "SELECT class_id FROM classes ORDER BY class_id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $class_id = $stmt->fetchall(PDO::FETCH_ASSOC);
        return $class_id;
    }catch (PDOException $e) {
        header('Location: error_page.php');
        exit;
    }
}

//class名の重複チェック
function unique($pdo, $class)
{
    try{
        $sql = 'SHOW TABLES FROM timetable';
        //SQL文を実行
        $table_stmt=$pdo->prepare($sql);
        $table_stmt->execute();
        //データベースのテーブルすべて読み出すまでループ
        while($table_rec = $table_stmt->fetch(PDO::FETCH_ASSOC)){
            //連想配列すべてを読み出すまでループ
            foreach($table_rec as $key => $val){
                // //クラス名が重複している場合trueを返す
                if($class == $val){
                    return $val;
                }
            }
        }
        return false;
    }catch (PDOException $e) {
        header('Location: error_page.php');
        exit;
    }

}

function CreateTable($pdo, $class)
{
    // テーブル作成のSQLを作成
    $sql = "CREATE TABLE IF NOT EXISTS {$class} (
        times_id INT(11) PRIMARY KEY,
        start_time  DATETIME NOT NULL,
        ending_time DATETIME NOT NULL,
        subjects_id_monday VARCHAR(40),
        subjects_id_tuesday VARCHAR(40),
        subjects_id_wednesday VARCHAR(40),
        subjects_id_thursday VARCHAR(40),
        subjects_id_friday VARCHAR(40),
        FOREIGN KEY (subjects_id_monday) REFERENCES subjects(subject_id) ON DELETE RESTRICT ON UPDATE CASCADE,
        FOREIGN KEY (subjects_id_tuesday) REFERENCES subjects(subject_id) ON DELETE RESTRICT ON UPDATE CASCADE,
        FOREIGN KEY (subjects_id_wednesday) REFERENCES subjects(subject_id) ON DELETE RESTRICT ON UPDATE CASCADE,
        FOREIGN KEY (subjects_id_thursday) REFERENCES subjects(subject_id) ON DELETE RESTRICT ON UPDATE CASCADE,
        FOREIGN KEY (subjects_id_friday) REFERENCES subjects(subject_id) ON DELETE RESTRICT ON UPDATE CASCADE
    ) engine=innodb charset=utf8mb4";

    // SQLを実行
    $stmt = $pdo->query($sql);
    return $stmt;
}

//DB接続
require_once('Components/connect.php');

//pdo変数
$pdo_attendance = Attendance();
$pdo_timetable = TimeTable();

//エラーメッセージ用の変数
$message = "";

//クラス名をすべて取得
$class_id = Get_class_Id($pdo_attendance);

//postデータを受け取る
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    //テーブル名チェック
    $isTableName = unique($pdo_timetable, $_POST['class']);
    //選択したクラス名のテーブルが既に存在している場合
    if ($isTableName) {
        $message = "選択したクラス名のテーブルは既に存在しています";
    }else{
        //テーブル作成
        if(CreateTable($pdo_timetable, $_POST['class'])){
            $message = "テーブル作成に成功しました";
            //csv読み込み
        }else{
            $message = "テーブル作成に失敗しました";
        }

    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
    <title>時間割作成</title>
</head>
<body>
    <form action="" method="post">
        <div class="error_message">
            <?= $message ?>
        </div> 
        <div class="mt-2" name="class" id="class">
            <label>学科・クラス</label>        
            <select name="class" class="form-control" required>
                <option value="" hidden>選択してください</option>
                <?php for($i = 0; $i < count($class_id); $i++){
                    echo "<option>{$class_id[$i]["class_id"]}</option>";
                }?>
            </select>
        </div>
        <p>
            <input type="file" name="filename" accept=".csv" required/>
        </p>
        <div class="mt-4">
            <button class="w-100 btn btn-lg btn-primary">作成</button>
        </div>
    </form>    
</body>

</html>