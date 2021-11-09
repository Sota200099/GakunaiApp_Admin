/**
* optionタグ生成
* @param {Number} num 
* @param {String} parentId 生成するoptionの親selectタグのid属性
*/
function createOptionElements(num,parentId){
    const doc = document.createElement('option');
    doc.value = doc.innerHTML = num;
    document.getElementById(parentId).appendChild(doc);
  }
   
   
  /**
  * 年月日を yyyymmdd に整形
  * @param {Number} y 
  * @param {Number} m 
  * @param {Number} d 
  */
  function yyyymmdd(y, m, d) {
    var y0 = ('000' + y).slice(-4);
    var m0 = ('0' + m).slice(-2);
    var d0 = ('0' + d).slice(-2);
    return y0 + m0 + d0;
  }
   
   
  /**
  * 年 or 月変更時
  */ 
  function yearMonthChange() {
   
    const selectY = document.getElementById("js_year").value;
    const selectM = document.getElementById("js_month").value;
    // 日付のみ変更するので、letで宣言
    let selectD = document.getElementById("js_day").value;
   
    // 月により、最終日を変更
    switch (selectM) {
      case "1":
      case "3":
      case "5":
      case "7":
      case "8":
      case "10":
      case "12":
        lastDay = "31"
        break;
      case "4":
      case "6":
      case "9":
      case "11":
        lastDay = "30"
        break;
      case "2":
        // 2月のみ、うるう年判定をする
        if (selectY %4 === 0 && selectY%100 !== 0 || selectY % 400 === 0 ) {
          lastDay = "29"
        } else {
          lastDay = "28"
        }
        break;
      default:
        lastDay = "31"
        break;
    }
   
    // 選択可能な日付を変更（いったん空にしてから、optionを生成する）
    document.getElementById("js_day").innerHTML = "";
    for (let i = 1; i <= this.lastDay; i++) {
      this.createOptionElements(i,'js_day');
    }
   
    // もともと選択していた日付を選択した状態にする
    if (lastDay <= selectD) {
      // 選択していた日付が変更後の年月にない場合、存在する最後の日を選択させる
      selectD = lastDay
    }
    document.getElementById("js_day").value = selectD
   
  }
   
  /**
  * submit時
  */ 
  function formSubmit() {
   
    const date = yyyymmdd(document.getElementById("js_year").value,
      document.getElementById("js_month").value,
      document.getElementById("js_day").value);
   
    // 現在より未来の日付は登録出来ないようにしたい場合
    const now = new Date();
    const nowY = now.getFullYear();
    const nowM = now.getMonth() + 1; // getMonth()のみ0から始まる整数を返すので、+1する
    const nowD = now.getDate();
    const nowDate = yyyymmdd(nowY,nowM,nowD);
    if ( date > nowDate ) {
      alert('現在より未来の日付は登録出来ません');
      return false;
    } else {
      return true;
    }
   
  }
  
  window.onload = function() {
   
    // 現在から何年まで遡って、プルダウンを出すか
    let mostOldYear = 2006;
    // 日付の最大値の初期値
    let lastDay = 31;
    // 現在年取得
    const nowTime = new Date();
    const nowYear = nowTime.getFullYear();
   
    // 年月日それぞれのselectタグに初期option生成
    for (let i = nowYear; i >= mostOldYear; i--) {
      createOptionElements(i,'js_year');
    }
    for (let i = 1; i <= 12; i++) {
      createOptionElements(i,'js_month');
    }
    for (let i = 1; i <= lastDay; i++) {
      createOptionElements(i,'js_day');
    }
   
  }