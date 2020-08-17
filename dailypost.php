<?php

session_start();
include("funcs.php");
loginCheck();

$lid = $_SESSION['lid'];
$name = $_SESSION['name'];
// echo $name;
//1.  DB接続します
$pdo = db_conn();

//２．データ登録SQL作成
$stmt = $pdo->prepare("SELECT * FROM summary_table WHERE (summary_table.lid=:lid) 
                                AND (DATE_FORMAT(summary_table.indate,'%M %d %Y') = DATE_FORMAT(sysdate(),'%M %d %Y')) ");
$stmt->bindValue(":lid", $lid, PDO::PARAM_STR);
$status = $stmt->execute();

//３．データ表示
$view="";

if($status==false) {

  //execute（SQL実行時にエラーがある場合）
  sql_error($stmt);

}else{
  //Selectデータの数だけ自動でループしてくれる
  //FETCH_ASSOC=http://php.net/manual/ja/pdostatement.fetch.php
 
  while( $r[] = $stmt->fetch(PDO::FETCH_ASSOC)){ 
    //$view .= '<p>'.$r['id'].": ".$r['title'].'</p>';  //.= ,eams += in js , connect & update one by one
    //$view .= "$r[url]";
    //$view .= "<p>";
    $json = json_encode($r);  
  }
}




//tag
$order = $pdo->prepare("SELECT * FROM outcome_table WHERE (outcome_table.lid=:lid) 
                                AND (DATE_FORMAT(outcome_table.indate,'%M %d %Y') = DATE_FORMAT(sysdate(),'%M %d %Y')) ORDER BY tag");
$order->bindValue(":lid", $lid, PDO::PARAM_STR);
$condition = $order->execute();

//３．データ表示
$view="";

if($condition==false) {

  //execute（SQL実行時にエラーがある場合）
  sql_error($order);

}else{
  //Selectデータの数だけ自動でループしてくれる
  //FETCH_ASSOC=http://php.net/manual/ja/pdostatement.fetch.php
 
  while( $t[] = $order->fetch(PDO::FETCH_ASSOC)){ 
    //$view .= '<p>'.$r['id'].": ".$r['title'].'</p>';  //.= ,eams += in js , connect & update one by one
    //$view .= "$r[url]";
    //$view .= "<p>";
    $taag = json_encode($t);  
  }
}






?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>記録を投稿</title>
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet">
  <link rel="stylesheet" href='css/reset.css'>
  <link rel="stylesheet" type="text/css" href="css/dailypost.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <!-- ファビコン追加 -->
  <link rel="shortcut icon" href="img/goat_32.ico">

  <style>div{font-size:16px;}</style>
</head>
<body>

<!-- Head[Start] -->
<header>
<nav>
 <div class="drawer">
 <!--- いわゆるロゴ svg を利用------>
 <div id="logo"><a href="main.php"><img src="img/Logo.jpg" alt="GOATロゴ"></a></div>
 

  </div>
 <!-------------- drawer ここまで-->
 
 <div class="menu">
  <ul>
   <li ><a href="main.php">Home</a></li>
   <li ><a href="index.php">Plan</a></li>
   <li ><a href="result_summary-1.php">Record</a></li>
   <li><a href="dailypost.php">Post</a></li>
   <li><a href="select.php">Ranking</a></li>
   <li ><a href="logout.php">Logout</a></li>
  </ul>
 </div>
</nav>
</header>
<!-- Head[End] -->

<!-- Main[Start] -->
<h1>記録を投稿する</h1>

<form method="post" action="dailypost_act.php">
    <legend>Post Now!</legend>
      <div id=dailypost class=wrapper style="
          display: grid;
          margin: auto;
          grid-template-columns: 1fr ;
      ">
      </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
  sum =(data, key)=> {

    all_stop_watch = []
        for (i = 0; i <data.length; i++) {
            all_stop_watch.push(parseInt(data[i][key]))
        }
    // console.log(all_stop_watch);

    var sum = all_stop_watch.reduce(function(a, b){
        return a + b;
    }, 0);
    return sum;
  }



  

  tagCollector=(input)=>{
        
        var onerecord = [input[0].tag] // one record
        console.log(onerecord)
        if(input.length === 0 ){
            return 0
        } else if (input.length === 1) {
            return onerecord
        }else {
            for (i = 0; i+1 < input.length; i++) {
                thistag = input[i].tag
                nexttag = input[i+1].tag

                if (thistag !== nexttag){
                  console.log('works')
                    onerecord.push(nexttag)
                    console.log(onerecord)
                    
                }
                if (i+2 === input.length){
                      
                        return onerecord
                } 
            }
        }        
  }
  tagStringify=(tags)=>{
    var str ="";
    var blank = ","
    for (i = 0; i < tags.length; i++) {
      str += (tags[i]+ blank)
    }
    return str
  }



    let datas; 
    if (!'<?=$json?>') {
        datas = ("empty")
    } else {
      datas = JSON.parse('<?=$json?>')
      var data = datas[0]
    console.log(data)
    
    }
   
    var lid = ('<?=$lid?>')

    let taag; 
    if (!'<?=$taag?>') {
      taag = ("empty")
      console.log(taag)
    } else {
      taag = JSON.parse('<?=$taag?>')
      var tags = tagCollector(taag)
   
      var str = tagStringify(tags)
    }


    
    //var total_stop = Math.round(sum(datas,'total_stopwatch')/60/60);
      //console.log(total_stop)

    //var total_todayy = Math.round(sum(datas,'total_today')/60/60);
      //console.log(total_todayy)


    //var total_achievement = Math.round(sum(datas,'achievement'));
      //console.log(total_achievement)


      //datas.map(data => 
        $('#dailypost').append( 
          `<div class="card" style=
          "width:80%; 
          margin: auto;
          display: flex;
          
          ">
          <img src="https://robohash.org/${datas[0].id}?200*200" style="width:20%">
          <form method="post" id="plan_post" action="dailypost_act.php">

            <div class="container"> 
            <table>
            <tr>
              <h3> Tag : ${str}</h3>
            </tr>
            <tr>
              <td><h3> Total Study times : </h3></td>
              <td><h3>${data.total_stopwatch} h </h3></td>
            </tr>
            <tr>
              <td><h3> Achievement : </h3></td>
              <td><h3>${data.achievement} % </h3></td>
            </tr>
            </table>  
              <h3> Comment : <br> <textArea id="comment" class="uk-textarea" name="comment" rows="4" cols="40"></textArea> </h3> 

              <input type="hidden" name="lid" value=${lid}>
              <input type="hidden" name="total_study_time" value=${data.total_stopwatch}>
              <input type="hidden" name="total_achievement" value=${data.achievement}>
              <input type="hidden" name="str" value=${str}>
 
              <input type="submit" id=submit  value="Submit">
            
            </div>
            </form>
          </div>`
        )
      
     
     
  </script>
</form>
<!-- Main[End] -->


</body>
</html>
