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
        times_id INT(11) AUTO_INCREMENT PRIMARY KEY,
        start_time  TIME NOT NULL,
        ending_time TIME NOT NULL,
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
}

function ImportCsv($pdo,$class,$aaa)
{
    try{
        $sql = "INSERT INTO {$class} VALUES(times_id,\"$aaa\");";
        // SQLを実行
        $stmt = $pdo->query($sql);
    }catch (PDOException $e) {
        header('Location: error_page.php');
        exit;
    }
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
    $class = $_POST['class'];
    //テーブル名チェック
    $isTableName = unique($pdo_timetable, $class);
    //選択したクラス名のテーブルの有無を判定
    if ($isTableName) {
        $message = "既に作成されているテーブルにファイルデータをインポートしました";
    }else{
        //テーブル作成
        CreateTable($pdo_timetable, $class);
        $message = "選択したクラス名のテーブルを新たに作成し、ファイルデータをインポートしました";
    }

    //fopen()関数で、ファイルを開く。"r"で「読み込み専用」という指示。
    $tmp = fopen($_FILES['csvfile']['tmp_name'], "r");
    //2次元配列にする。fgetcsv()関数により、csvデータを読み込む。
    while ($csv[] = fgetcsv($tmp, "1024")) {}
    //文字化け防止のため、配列 $csv の文字コードをSJIS-winからUTF-8に変換
    mb_convert_encoding($csv, "SJIS", "UTF-8");       
    $lim = count($csv);//for文で読み込むために、配列の最大行数を出す
    for($i=0; $i<=$lim; $i++){
        if($i < ($lim-1)){
            $ar = $csv[$i];
            $aaa = implode("\",\"", $ar);
            //csvインポート
            ImportCsv($pdo_timetable,$class,$aaa);
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
    <div class="container bg-light">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="error_message">
                <?= $message ?>
            </div> 
            <h2 class="mt-4 mb-5 pt-5">時間割作成</h2>
            <div class="mt-2 mb-3" name="class" id="class">
                <label class="font-weight-bold mb-4">学科・クラス</label>        
                <select name="class" class="form-control" required>
                    <option value="" hidden>選択してください</option>
                    <?php for($i = 0; $i < count($class_id); $i++){
                        echo "<option>{$class_id[$i]["class_id"]}</option>";
                    }?>
                </select>
            </div>
            <p>
                <input type="file" name="csvfile" enctype="multipart/form-data" accept=".csv" required/>
            </p>
            <div class="mt-5 pt-5 pb-5">
                <button class="w-100 btn btn-lg btn-primary">作成</button>
            </div>
        </form>    
    </div>
</body>

</html>