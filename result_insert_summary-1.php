<?php
//1. POSTデータ取得
//$name = filter_input( INPUT_GET, ","name" ); //こういうのもあるよ
//$email = filter_input( INPUT_POST, "email" ); //こういうのもあるよ
session_start();
$lid = $_SESSION['lid'];
$achievement = $_POST['achievement'];
$total_stopwatch = $_POST['total_stopwatch'];
$progress = $_POST['progress'];
$alltags = $_POST['alltags'];
$_SESSION['alltags'] = $alltags;




//2. DB接続します
include("funcs.php");
$pdo = db_conn();

// (1st process)select all the taskids (1) made today (2) made ever  --> only task id
$stmt = $pdo->prepare("SELECT outcome_table.taskid
                      FROM outcome_table
                      WHERE (outcome_table.lid=:lid) AND (DATE_FORMAT(outcome_table.indate,'%M %d %Y') = DATE_FORMAT(sysdate(),'%M %d %Y'))");
$stmt->bindValue(":lid", $lid, PDO::PARAM_STR);
$status_a = $stmt->execute();


$view="";
if($status_a==false) {
  
  $error = $stmt->errorInfo();
  exit("SQLError:".$error[2]);

}else{
 
 
  while( $r[] = $stmt->fetch(PDO::FETCH_ASSOC)){ 
  
    //$taskidsToday = json_encode($r);
    $taskidsToday = $r;
    //echo $taskidsToday;
    $_SESSION['taskidsToday'] = $taskidsToday;
    //print_r($taskidsToday[0]['taskid']);
    
  }

}
// end of (1st process)




$checking = $pdo->prepare("SELECT * FROM summary_table WHERE lid=:lid AND DATE_FORMAT(summary_table.indate,'%M %d %Y') = DATE_FORMAT(sysdate(),'%M %d %Y')");
$checking->bindValue(":lid", $lid, PDO::PARAM_STR);
$condition = $checking->execute();


// get taskid(created today exist) here from db
$view="";
if($condition==false) {
  sql_error();
}else{
  while( $r = $checking->fetch(PDO::FETCH_ASSOC)){
  $view .= $r["lid"];
}
}
// herer select all the taskids (1) made today (2) made ever  --> only task id
// make all ids into array and push them into array

// on result_detail, receive it and push it into session
// on result_insert_detail get them from session
// put the array into loap , on each round, compare the taskid to id in outcome table created today
//   

if ($view){
  $update = $pdo->prepare("UPDATE summary_table SET lid=:lid, total_stopwatch=:total_stopwatch, achievement=:achievement, indate=sysdate() WHERE lid=:lid AND DATE_FORMAT(summary_table.indate,'%M %d %Y') = DATE_FORMAT(sysdate(),'%M %d %Y')");
  $update->bindValue(":lid", $lid, PDO::PARAM_STR); 
  $update->bindValue(":total_stopwatch", $total_stopwatch, PDO::PARAM_INT);
  $update->bindValue(":achievement", $achievement, PDO::PARAM_INT);
  $status = $update->execute();
} else { 
  $update = $pdo->prepare("INSERT INTO summary_table(lid,total_stopwatch,achievement,indate)VALUES(:lid, :total_stopwatch, :achievement, sysdate() )");
  $update->bindValue(":lid", $lid, PDO::PARAM_STR);
  $update->bindValue(":total_stopwatch", $total_stopwatch, PDO::PARAM_INT); 
  $update->bindValue(":achievement", $achievement, PDO::PARAM_INT);
  $status = $update->execute(); 
}


//３．データ登録SQL作成 // bined var (:dsakj) to recieved value from Post request to html

//----- summary_table -----

//４．データ登録処理後
if($status==false){
  //*** function化する！*****************
  sql_error($update);
}else{
  //*** function化する！*****************
//   redirect("index.php");
}



if ($view){
  $reupdate = $pdo->prepare("UPDATE summary2_table SET lid=:lid, progress=:progress, indate=sysdate() WHERE lid=:lid AND DATE_FORMAT(summary2_table.indate,'%M %d %Y') = DATE_FORMAT(sysdate(),'%M %d %Y')");
  $reupdate->bindValue(":lid", $lid, PDO::PARAM_STR); 
  $reupdate->bindValue(":progress", $progress, PDO::PARAM_INT);
  $condition = $reupdate->execute();
} else { 
//----- summary2_table -----
$reupdate = $pdo->prepare("INSERT INTO summary2_table(lid,progress,indate )VALUES(:lid, :progress, sysdate() )");
$reupdate->bindValue(":lid", $lid, PDO::PARAM_STR); 
$reupdate->bindValue(":progress", $progress, PDO::PARAM_INT);
$condition = $reupdate->execute(); // IT returns boolean (fail or success)
}

//４．データ登録処理後
if($condition==false){
  //*** function化する！*****************
  sql_error($reupdate);
}else{
  //*** function化する！*****************
  redirect("result_summary-1.php");
}


?>