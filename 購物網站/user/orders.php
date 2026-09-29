<?php
//啟用 session
session_start();
//引入必要的函式庫
require_once __DIR__.'/../includes/functions.php';
//檢查用戶是否登入，未登入則跳轉到登入頁面
if(!isLoggedIn()){
    header("Location:/final_project/user/login.php");
    exit;
}
//從 session 獲取當前用戶 ID
$user_id=$_SESSION['user_id'];
//準備 SQL 查詢獲取用戶所有訂單（按總金額降序排列）
$stmt=$conn->prepare("SELECT order_id,total_price,status,created_at FROM `order` WHERE user_id=? ORDER BY total_price DESC");
$stmt->bind_param("i",$user_id);//綁定參數防止 SQL 注入
$stmt->execute();
$result=$stmt->get_result();
$orders=$result->fetch_all(MYSQLI_ASSOC);//獲取所有結果作為關聯數組
//獲取用戶基本資料
$user=getUserProfile($user_id);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>我的訂單 - <?=htmlspecialchars($user['username'])?></title>
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
                        <p class="text-muted">會員 since <?=date('Y/m/d',strtotime($user['regtime']))?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item active">我的訂單</li>
                        <li class="list-group-item"><a href="/final_project/user/profile.php">個人資料</a></li>
                        <li class="list-group-item"><a href="/final_project/user/settings.php">帳號設定</a></li>
                        <li class="list-group-item"><a href="/final_project/user/logout.php">登出</a></li>
                    </ul>
                </div>
            </div>
            <!--右側訂單列表-->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h4>我的訂單</h4>
                    </div>
                    <div class="card-body">
                        <!--提示訊息-->
                        <?php if(isset($_SESSION['success'])):?>
                            <div class="alert alert-success"><?=$_SESSION['success']?></div>
                        <?php endif;?>
                        <?php if(isset($_SESSION['error'])):?>
                            <div class="alert alert-danger"><?=$_SESSION['error']?></div>
                        <?php endif;?>
                        <?php if(empty($orders)):?>
                            <!--無訂單時的提示訊息-->
                            <div class="alert alert-info">目前沒有任何訂單記錄</div>
                        <?php else:?>
                            <!--訂單表格-->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>訂單編號</th>
                                            <th>總金額</th>
                                            <th>訂單狀態</th>
                                            <th>下單時間</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($orders as $order):?>
                                        <tr>
                                            <td>#<?=$order['order_id']?></td>
                                            <td>NT$<?=number_format($order['total_price'])?></td>
                                            <td><?=getStatusText($order['status'])?></td>
                                            <td><?=date('Y/m/d H:i',strtotime($order['created_at']))?></td>
                                            <!--大寫H表示24小時制的時間格式，i表示分鐘-->
                                            <td>
                                                <a href="/final_project/user/order_details.php?id=<?=$order['order_id']?>" 
                                                class="btn btn-sm btn-outline-primary">查看詳情</a>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--引入頁尾-->
    <?php include __DIR__.'/../includes/footer.php';?>
</body>
</html>