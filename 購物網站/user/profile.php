<?php
// 啟用 session
session_start();
// 引入必要的函式庫和驗證文件
require_once __DIR__.'/../includes/functions.php';
require_once __DIR__.'/../includes/auth.php';

// 檢查用戶是否登入，未登入則跳轉到登入頁面
if(!isLoggedIn()){
    header('Location:/final_project/user/login.php');
    exit;
}
// 獲取當前用戶資料
$user = getUserProfile($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>個人資料-<?=htmlspecialchars($user['username'])?></title>
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
           <!--左側導覽列-->
           <div class="col-md-3">
               <div class="card">
                   <div class="card-body text-center">
                       <h5><?=htmlspecialchars($user['username'])?></h5>
                       <?php if(isAdmin()):?>
                           <p class="text-muted">管理員 since<?=date('Y/m/d', strtotime($user['regtime']))?></p>
                       <?php else:?>
                           <p class="text-muted">會員 since<?=date('Y/m/d', strtotime($user['regtime']))?></p>
                       <?php endif;?>
                   </div>
                   <ul class="list-group list-group-flush">
                       <?php if(isAdmin()):?>
                           <!--管理員專用導覽-->
                           <li class="list-group-item"><a href="/final_project/admin/products.php">商品管理</a></li>
                           <li class="list-group-item"><a href="/final_project/admin/orders.php">訂單管理</a></li>
                           <li class="list-group-item"><a href="/final_project/admin/users.php">會員管理</a></li>
                           <li class="list-group-item active">個人資料</li>
                           <li class="list-group-item"><a href="/final_project/user/settings.php">帳號設定</a></li>
                           <li class="list-group-item"><a href="/final_project/user/logout.php">登出</a></li>
                       <?php else:?>
                           <!--一般用戶導覽-->
                           <li class="list-group-item"><a href="/final_project/user/orders.php">我的訂單</a></li>
                           <li class="list-group-item active">個人資料</li>
                           <li class="list-group-item"><a href="/final_project/user/settings.php">帳號設定</a></li>
                           <li class="list-group-item"><a href="/final_project/user/logout.php">登出</a></li>
                       <?php endif;?>
                   </ul>
               </div>
           </div>
           <!--右側主要內容區-->
           <div class="col-md-9">
               <div class="card">
                   <div class="card-header">
                       <h4>個人資料</h4>
                   </div>
                   <div class="card-body">
                       <!--個人資料顯示-->
                       <div class="mb-3">
                           <label class="form-label">使用者名稱</label>
                           <div class="form-control-plaintext"><?=htmlspecialchars($user['username'])?></div>
                       </div>
                       <div class="mb-3">
                           <label class="form-label">電子郵件</label>
                           <div class="form-control-plaintext"><?=htmlspecialchars($user['email'])?></div>
                       </div>
                       <?php if(!empty($user['phone'])): ?>
                       <div class="mb-3">
                           <label class="form-label">電話</label>
                           <div class="form-control-plaintext"><?=htmlspecialchars($user['phone'])?></div>
                       </div>
                       <?php endif; ?>
                       <?php if(!empty($user['locations'])): ?>
                       <div class="mb-3">
                           <label class="form-label">地址</label>
                           <div class="form-control-plaintext"><?=htmlspecialchars($user['locations'])?></div>
                       </div>
                       <?php endif; ?>
                       <div class="mb-3">
                           <label class="form-label">註冊日期</label>
                           <div class="form-control-plaintext"><?=date('Y/m/d', strtotime($user['regtime']))?></div>
                       </div>
                       <!--前往修改頁面的按鈕-->
                       <a href="/final_project/user/settings.php" class="btn btn-primary">修改帳號設定</a>
                   </div>
               </div>
           </div>
       </div>
   </div>
   <!--引入頁尾-->
   <?php include __DIR__.'/../includes/footer.php';?>
</body>
</html>