<?php
//啟用 session 功能
session_start();
//引入必要的函式庫和驗證檔案
require_once __DIR__.'/../includes/functions.php';
require_once __DIR__.'/../includes/auth.php';
//檢查使用者是否具有管理員權限
if (!isAdmin()) {
    // 如果不是管理員，重定向到首頁
    header("Location: /final_project/index.php");
    exit;
}
//獲取當前登入使用者的個人資料
$user=getUserProfile($_SESSION['user_id']);
//從資料庫獲取所有商品資料
$products=getAllProducts();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>商品管理-<?=htmlspecialchars($user['username'])?></title>
    <!--引入 Bootstrap CSS 框架-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--引入 Bootstrap JS 框架-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!--引入頁首檔案-->
    <?php include __DIR__.'/../includes/header.php';?>
    <div class="container mt-5">
        <div class="row">
            <!--左側導覽欄-->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <!--顯示當前使用者名稱-->
                        <h5><?=htmlspecialchars($user['username'])?></h5>
                        <!--顯示註冊日期-->
                        <p class="text-muted">管理員 since <?=date('Y/m/d',strtotime($user['regtime']))?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <!--管理員功能導航選單-->
                        <li class="list-group-item active">商品管理</a></li>
                        <li class="list-group-item"><a href="/final_project/admin/orders.php">訂單管理</a></li>
                        <li class="list-group-item"><a href="/final_project/admin/users.php">會員管理</a></li>
                        <li class="list-group-item"><a href="/final_project/user/profile.php">個人資料</a></li>
                        <li class="list-group-item"><a href="/final_project/user/settings.php">帳號設定</a></li>
                        <li class="list-group-item"><a href="/final_project/user/logout.php">登出</a></li>
                    </ul>
                </div>
            </div>
            <!--右側主要內容區-->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>商品管理</h4>
                        <!--新增商品按鈕-->
                        <a href="/final_project/admin/add_product.php" class="btn btn-success">新增商品</a>
                    </div>
                    <div class="card-body">
                        <!--顯示成功訊息-->
                        <?php if (isset($_SESSION['success'])):?>
                            <div class="alert alert-success"><?=htmlspecialchars($_SESSION['success'])?></div>
                            <?php unset($_SESSION['success']);?>
                        <?php endif;?>
                        <!--顯示錯誤訊息-->
                        <?php if (isset($_SESSION['error'])):?>
                            <div class="alert alert-danger"><?=htmlspecialchars($_SESSION['error'])?></div>
                            <?php unset($_SESSION['error']);?>
                        <?php endif;?>
                        <!--商品列表表格-->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>商品名稱</th>
                                        <th>價格</th>
                                        <th>庫存</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--循環顯示每個商品資料-->
                                    <?php foreach ($products as $product):?>
                                        <tr>
                                            <td>#<?=$product['product_id']?></td>
                                            <!--使用 htmlspecialchars 防止 XSS 攻擊-->
                                            <td><?=htmlspecialchars($product['name'])?></td>
                                            <!--格式化價格顯示-->
                                            <td>NT$ <?=number_format($product['price'])?></td>
                                            <td><?=$product['inventory']?></td>
                                            <td>
                                                <!--編輯商品按鈕-->
                                                <a href="/final_project/admin/edit_product.php?id=<?=$product['product_id']?>" class="btn btn-sm btn-primary">編輯</a>
                                                <!--刪除商品按鈕，帶有確認對話框-->
                                                <a href="/final_project/admin/delete_product.php?id=<?=$product['product_id']?>" class="btn btn-sm btn-danger" onclick="return confirm('確定要刪除此商品嗎？')">刪除</a>
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
    <!--引入頁尾檔案-->
    <?php include __DIR__.'/../includes/footer.php';?>
    
    
</body>
</html>