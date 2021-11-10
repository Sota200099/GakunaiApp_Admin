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

//教科名をすべて取得
function Get_Subject_Id($pdo)
{
    try{
        $sql = "SELECT subject_id FROM subjects ORDER BY subject_id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $subject_id = $stmt->fetchall(PDO::FETCH_ASSOC);
        return $subject_id;
    }catch (PDOException $e) {
        header('Location: error_page.php');
        exit;
    }
}

//サニタイズ(XSS対策)
function check($posts)
{
    foreach ($posts as $column => $post) {
        $posts[$column] = htmlspecialchars($post, ENT_QUOTES, 'UTF-8');
    }
    return $posts;
}

//入力値が正の整数か確認する
function Check_Num($num)
{
    //全角数字を半角に変換
    $num = mb_convert_kana($num, 'n', 'UTF-8');

    //文字列すべてが数字か確認(formからの入力は全て文字列となる)
    if(ctype_digit($num)){
        //int型に変換
        $num = intval($num); 
        //numが0より大きければ
        if($num > 0){
            return $num;
        }
    }
    return false;
}

//出席データを取得
function Get_Log($pdo,$date,$class,$subject,$status)
{
    //指定した日程から現在まで、選択したクラスと教科で出席しているデータを取得
     try{
         $sql = "SELECT * FROM attendance_log 
         WHERE DATE_FORMAT(update_time, '%Y-%m-%d') <= DATE_FORMAT(now(), '%Y-%m-%d')
         AND DATE_FORMAT(update_time, '%Y-%m-%d') >= ?
         AND students_classes_id = ?
         AND timetable_subjects_id = ?
         AND attendance_status = ?";
         $stmt = $pdo->prepare($sql);
         $stmt->execute(array($date,$class,$subject,$status));
         $log = $stmt->fetchall(PDO::FETCH_ASSOC);
         return $log;
     }catch (PDOException $e) {
         header('Location: error_page.php');
         exit;
     }
}

//出席データを取得するための定数
define("ATTENDANCE","出席");

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

//DB接続
require_once('Components/connect.php');

//pdo変数
$pdo_attendance = Attendance();
$pdo_timetable = TimeTable();


//エラーメッセージ用の変数
$message = "";

//クラス名をすべて取得
$class_id = Get_class_Id($pdo_attendance);

//教科名をすべて取得
$subject_id = Get_Subject_Id($pdo_timetable);

//postデータを受け取る
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    //サニタイズ
    $posts = check($_POST);

    //コマ数
    $num = Check_Num($_POST['num']);
    //コマ数が正の整数か確認
    if($num){
        //引数用の日時
        $date_argument = $_POST['selectYear']."-".$_POST['selectMonth']."-".$_POST['selectDate'];
        //データ取得範囲を定める変数
        $date = new DateTimeImmutable($date_argument);
        //フォーマット
        $date = $date->format('Y-m-d');

        //受け取った教科を変数に格納
        $subject = $_POST['subject'];

        //受け取ったクラスを変数に格納
        $class = $_POST['class'];

        //条件に該当する出席記録をすべて取得
        $log = Get_Log($pdo_attendance,$date,$class,$subject,ATTENDANCE);

        //logデータがあるか確認
        if(!empty($log)){
            //logデータをソートした連想配列を格納
            $array = Array_Sort($log);
            var_dump($array);
            //出席率をデータベースに格納
            Insert_Array($pdo_attendance,$num,$subject,$array);
        }else{
            $message = "データが見つかりませんでした";
        }
    }else{
        $message = "コマ数には正の整数を入力してください";
    }
}

//生徒番号をキーとして生徒ごとの出席数を格納した連想配列を作る
function Array_Sort($log)
{
    //配列を初期化
    $array = array();

    foreach($log as $l){
        if(!empty($array)){
            foreach($array as $key => $value){
                //生徒番号キーが存在したら
                if($key == $l['students_id']){
                    //生徒の出席数を+1
                    $array[$key] += 1;
                    break;
                }
                //キーが存在しないまま配列が最後まで到達したら
                if ($key == array_key_last($array)){
                    //生徒番号のキーを出席数1とともに追加
                    $array[$l['students_id']] = 1;
                }
            }
        }else{    
            //生徒番号のキーを出席数1とともに追加
            $array[$l['students_id']] = 1;
        }
    }
    return $array;
}

//生徒ごとに出席数を割り出し、データベースに格納
function Insert_Array($pdo,$num,$subject,$array)
{
    //sql
    $sql = "INSERT INTO attendance_rate VALUES(id,?,?,?,?)";

    //現在時刻を取得
    $update_time = new DateTime();
    $update_time = $update_time-> format('Y-m-d H:i:s');

    foreach($array as $key => $value){
        $rate = floor(($value/$num) * 100)."%";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array($key,$subject,$rate,$update_time));
        }catch (PDOException $e) {
            header('Location: error_page.php');
            exit;
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>出席率</title>
</head>
<body>
    <main class="d-flex justify-content-center">
        <div class="w-50 mt-3 p-5 bg-light">
            <form action="" method="post" onsubmit="return formSubmit();">
                <h3 class="h3 mb-3 fw-normal">出席率の算出条件</h3>
                <div class="error_message">
                    <?= $message ?>
                </div>
                <div>
                    <div><label>データの取得範囲</label></div>  
                    <select id="js_year" name="selectYear" onchange="yearMonthChange()"></select> 年
                    <select id="js_month" name="selectMonth" onchange="yearMonthChange()"></select> 月
                    <select id="js_day" name="selectDate"></select> 日から現在
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
                <div>
                    <label>教科</label>        
                    <select name="subject" class="form-control" required>
                        <option value="" hidden>選択してください</option>
                        <?php for($i = 0; $i < count($subject_id); $i++){
                            echo "<option>{$subject_id[$i]['subject_id']}</option>";
                        }?>
                    </select>
                </div>
                <div>
                    <label for="num">コマ数（正の整数で入力）</label>
                    <input type="text" name="num" class="form-control" id="num" required>
                </div>
                <div class="mt-4">
                    <button class="w-100 btn btn-lg btn-success">算出</button>
                </div>       
            </form>
        </div>
    </main>    
</body>
<script src="js/date.js"></script>
</html>