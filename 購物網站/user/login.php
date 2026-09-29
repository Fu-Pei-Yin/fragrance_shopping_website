<?php
session_start();//啟動 session，才能使用登入狀態與訊息等功能
require_once __DIR__.'/../includes/functions.php';//載入通用函式
require_once __DIR__.'/../includes/auth.php';//載入登入驗證相關函式
//如果使用者已經登入，直接導回首頁
if(isLoggedIn()){
    header("Location:/final_project/index.php");
    exit;
}
// 初始化變數
$username=$password='';
$error='';//用來存放登入錯誤訊息
//如果是透過 POST 方法送出表單
if($_SERVER['REQUEST_METHOD']=='POST'){
    $username=$_POST['username'];//取得使用者輸入的帳號
    $password=$_POST['password'];//取得使用者輸入的密碼
    $result=loginUser($username, $password);//嘗試登入
    if($result===true){//如果登入成功
        $_SESSION['success']='登入成功';//設定成功訊息
        header("Location:/final_project/index.php");//導回首頁
        exit;
    } else {
        $error=$result;//如果登入失敗，將錯誤訊息存入變數
    }
}
?>
<!--引入頁首導航欄-->
<?php include_once __DIR__.'/../includes/header.php';?>
<!--登入頁標題-->
<h2>使用者登入</h2>
<!--提示訊息-->
<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?=$_SESSION['success'];?></div>
<?php endif; ?>
<?php if($error):?>
    <div class="alert alert-danger"><?=$error?></div>
<?php endif;?>
<!--登入表單-->
<form method='post'>
    <div class="mb-3">
        <label for="username" class="form-label">使用者名稱</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">密碼</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">登入</button>
    <p class="mt-3">還沒有帳號？<a href="/final_project/user/register.php">註冊</a></p>
</form>
<!--載入頁尾HTML-->
<?php require_once __DIR__.'/../includes/footer.php';?>