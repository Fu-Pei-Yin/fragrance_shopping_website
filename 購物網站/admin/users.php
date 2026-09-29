<?php
//啟用 session
session_start();
//引入必要的函式庫和驗證檔案
require_once __DIR__.'/../includes/functions.php';
require_once __DIR__.'/../includes/auth.php';
//檢查使用者是否登入且具有管理員權限
if(!isAdmin()){
    //如果不是管理員，重定向到首頁
    header("Location:/final_project/index.php");
    exit;
}
//獲取當前登入使用者的個人資料
$current_user=getUserProfile($_SESSION['user_id']);
//從資料庫獲取所有使用者資料
$users=getAllUsers();
//處理表單提交
if($_SERVER['REQUEST_METHOD']=='POST'){
    //處理刪除使用者請求
    if(isset($_POST['delete_user'])){
        $result=deleteUser($_POST['user_id']);
        if($result==true){
            $success="使用者已刪除！";
            //刪除成功後重新獲取使用者列表
            $users=getAllUsers();
        } else{
            $error=$result;//儲存錯誤訊息
        }
    }
    //處理切換管理員權限請求
    if(isset($_POST['toggle_admin'])){
        $result=toggleAdminStatus($_POST['user_id'], $_POST['is_admin']);
        if($result==true){
            $success="使用者權限已更新！";
            //權限變更成功後重新獲取使用者列表
            $users=getAllUsers();
        } else{
            $error=$result;//儲存錯誤訊息
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員管理 - <?=htmlspecialchars($current_user['username'])?></title>
    <!--引入 Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--引入 Bootstrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!--引入頁首-->
    <?php include __DIR__.'/../includes/header.php';?>
    <div class="container mt-5">
        <div class="row">
            <!--左側導覽欄-->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <!--顯示當前使用者名稱-->
                        <h5><?=htmlspecialchars($current_user['username'])?></h5>
                        <!--顯示註冊日期-->
                        <p class="text-muted">管理員 since <?=date('Y/m/d', strtotime($current_user['regtime']))?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <!--管理員功能導航連結-->
                        <li class="list-group-item"><a href="/final_project/admin/products.php">商品管理</a></li>
                        <li class="list-group-item"><a href="/final_project/admin/orders.php">訂單管理</a></li>
                        <li class="list-group-item active">會員管理</li>
                        <li class="list-group-item"><a href="/final_project/user/profile.php">個人資料</a></li>
                        <li class="list-group-item"><a href="/final_project/user/settings.php">帳號設定</a></li>
                        <li class="list-group-item"><a href="/final_project/user/logout.php">登出</a></li>
                    </ul>
                </div>
            </div>
            <!--右側主要內容區-->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h4>會員管理</h4>
                    </div>
                    <div class="card-body">
                        <!--顯示操作成功訊息-->
                        <?php if(isset($success)):?>
                            <div class="alert alert-success"><?=$success?></div>
                        <?php endif;?>
                        <!--顯示錯誤訊息-->
                        <?php if(isset($error)):?>
                            <div class="alert alert-danger"><?=$error?></div>
                        <?php endif;?>
                        <!--使用者列表表格-->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>使用者名稱</th>
                                        <th>電子郵件</th>
                                        <th>電話</th>
                                        <th>地址</th>
                                        <th>註冊日期</th>
                                        <th>權限</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--循環顯示每個使用者資料-->
                                    <?php foreach($users as $user):?>
                                        <tr>
                                            <td><?=$user['user_id']?></td>
                                            <td><?=htmlspecialchars($user['username']??'未提供')?></td>
                                            <td><?=htmlspecialchars($user['email']??'未提供')?></td>
                                            <td><?=htmlspecialchars($user['phone']??'未提供')?></td>
                                            <td><?=htmlspecialchars($user['locations']??'未提供')?></td>
                                            <td><?=date('Y/m/d H:i', strtotime($user['regtime']))?></td>
                                            <td>
                                                <!--切換管理員權限的表單-->
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="user_id" value="<?=$user['user_id']?>">
                                                    <input type="hidden" name="is_admin" value="<?=$user['is_admin']?'0':'1'?>">
                                                    <button type="submit" name="toggle_admin" class="btn btn-sm <?=$user['is_admin']?'btn-warning':'btn-secondary'?>">
                                                        <?=$user['is_admin']?'管理員':'一般會員'?>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <!--刪除使用者的表單-->
                                                <form method="POST" class="d-inline" onsubmit="return confirm('確定要刪除此使用者嗎？此操作無法復原！');">
                                                    <input type="hidden" name="user_id" value="<?=$user['user_id']?>">
                                                    <button type="submit" name="delete_user" class="btn btn-sm btn-danger">刪除</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--引入頁尾-->
    <?php include __DIR__.'/../includes/footer.php';?>
</body>
</html>