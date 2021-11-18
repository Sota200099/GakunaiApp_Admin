function input_confirm(){//入力内容の確認ダイアログを表示
    var check = confirm("削除してよろしいですか？");
    if(check){
        var re_check =confirm("本当によろしいですか?")
        if(re_check){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}