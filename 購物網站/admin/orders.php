<?php
//啟用 session 功能
session_start();
//引入必要的函式庫和驗證檔案
require_once __DIR__.'/../includes/functions.php';
require_once __DIR__.'/../includes/auth.php';
//檢查使用者是否已登入
if(!isLoggedIn()){
    //未登入則跳轉到登入頁面
    header('Location:/final_project/user/login.php');
    exit;
}
//獲取當前登入使用者的個人資料
$user=getUserProfile($_SESSION['user_id']);
//從 session 獲取當前使用者ID
$user_id=$_SESSION['user_id'];
//判斷當前使用者是否為管理員
$is_admin=isAdmin();
//獲取URL中的訂單狀態篩選參數
$status_filter=isset($_GET['status'])&&$_GET['status']!='all'?$_GET['status']:null;
//根據使用者權限獲取訂單列表
if($is_admin){
    //管理員獲取所有訂單
    $orders=getAllOrders($status_filter);
} else{
    //普通使用者只能獲取自己的訂單
    $orders=getUserOrders($user_id,$status_filter);
}
//處理管理員更新訂單狀態的請求
if($_SERVER['REQUEST_METHOD']=='POST'&&$is_admin&&isset($_POST['update_status'])){
    $order_id=$_POST['order_id'];
    $new_status=$_POST['status'];
    //嘗試更新訂單狀態
    if(updateOrderStatus($order_id,$new_status)){
        $success="訂單狀態已更新";
        //更新成功後重新獲取訂單列表
        $orders=$is_admin?getAllOrders($status_filter):getUserOrders($user_id,$status_filter);
    } else{
        $error="更新訂單狀態失敗";
    }
}
//檢查是否請求查看特定訂單詳情
$order_details=null;
if(isset($_GET['order_id'])){
    $order_id=$_GET['order_id'];
    //驗證使用者是否有權限查看此訂單
    if($is_admin||orderBelongsToUser($order_id,$user_id)){
        $order_details=getOrderDetails($order_id);
    } else{
        $error="您沒有權限查看此訂單";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title><?=$is_admin?'訂單管理':'我的訂單'?>-<?=htmlspecialchars($_SESSION['username'])?></title>
    <!--引入 Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--引入 Bootstrap 圖標-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!--引入 Bootstrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /*訂單卡片懸停效果*/
        .order-card{
            transition:all 0.3s ease;
            margin-bottom:15px;
        }
        .order-card:hover{
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }
        /*不同訂單狀態的背景色*/
        .status-pending{ background-color:#fff3cd; }
        .status-paid{ background-color:#cce5ff; }
        .status-shipped{ background-color:#d4edda; }
        .status-cancelled{ background-color:#f8d7da; }
        .status-completed{ background-color:#e2e3e5; }
    </style>
</head>
<body>
    <!--引入頁首-->
    <?php include __DIR__.'/../includes/header.php';?>
    <div class="container mt-5 mb-5">
        <div class="row">
            <!--左側導覽欄-->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5><?=htmlspecialchars($_SESSION['username'])?></h5>
                        <?php if($is_admin):?>
                            <p class="text-muted">管理員 since <?=date('Y/m/d',strtotime($user['regtime']))?></p>
                        <?php else:?>
                            <p class="text-muted">會員 since <?=date('Y/m/d',strtotime($user['regtime']))?></p>
                        <?php endif;?>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php if($is_admin):?>
                            <!--管理員專用導航-->
                            <li class="list-group-item"><a href="/final_project/admin/products.php">商品管理</a></li>
                            <li class="list-group-item active">訂單管理</li>
                            <li class="list-group-item"><a href="/final_project/admin/users.php">會員管理</a></li>
                            <li class="list-group-item"><a href="/final_project/user/profile.php">個人資料</a></li>
                            <li class="list-group-item"><a href="/final_project/user/settings.php">帳號設定</a></li>
                            <li class="list-group-item"><a href="/final_project/user/logout.php">登出</a></li>
                        <?php else:?>
                            <!--普通會員導航-->
                            <li class="list-group-item active">我的訂單</li>
                            <li class="list-group-item"><a href="/final_project/user/profile.php">個人資料</a></li>
                        <?php endif;?>
                    </ul>
                </div>
            </div>
            <!--右側主要內容區-->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><?=$is_admin?'訂單管理':'我的訂單'?></h4>
                    </div>
                    
                    <?php if($order_details):?>
                        <!--訂單詳情視圖-->
                        <div class="card-body">
                            <?php if(isset($success)):?>
                                <div class="alert alert-success"><?=$success?></div>
                            <?php endif;?>
                            <?php if(isset($error)):?>
                                <div class="alert alert-danger"><?=$error?></div>
                            <?php endif;?>
                            <div class="d-flex justify-content-between mb-4">
                                <h5>訂單詳情 #<?=$order_details['order_id']?></h5>
                                <a href="orders.php<?=$status_filter?'?status='.$status_filter:''?>" class="btn btn-outline-secondary">返回訂單列表</a>
                            </div>
                            <!--訂單基本資訊-->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>訂單資訊</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>訂單編號:</span>
                                            <span>#<?=$order_details['order_id']?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>訂單日期:</span>
                                            <span><?=date('Y/m/d H:i',strtotime($order_details['created_at']))?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>訂單狀態:</span>
                                            <span class="badge bg-<?=getStatusBadgeClass($order_details['status'])?>">
                                                <?=getStatusText($order_details['status'])?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>最後更新:</span>
                                            <span><?=date('Y/m/d H:i',strtotime($order_details['updated_at']))?></span>
                                        </li>
                                    </ul>
                                </div>
                                <!--訂單金額資訊-->
                                <div class="col-md-6">
                                    <h6>金額資訊</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>商品總計:</span>
                                            <span>$<?=number_format($order_details['total_price'],2)?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>運費:</span>
                                            <span>$0</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>總金額:</span>
                                            <span class="fw-bold">$<?=number_format($order_details['total_price'],2)?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!--訂單商品列表-->
                            <h6 class="mt-4">訂單商品</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>商品</th>
                                            <th>單價</th>
                                            <th>數量</th>
                                            <th>小計</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($order_details['items'] as $item):?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="/final_project/images/<?=htmlspecialchars($item['name'])?>.jpg" 
                                                             alt="<?=htmlspecialchars($item['name'])?>" 
                                                             width="60" class="me-3">
                                                        <div>
                                                            <h6 class="mb-0"><?=htmlspecialchars($item['name'])?></h6>
                                                            <small class="text-muted">介紹:<?=htmlspecialchars($item['description'])?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>$<?=number_format($item['price'],2)?></td>
                                                <td><?=$item['quantity']?></td>
                                                <td>$<?=number_format($item['price']*$item['quantity'],2)?></td>
                                            </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                            <!--管理員專用：更新訂單狀態表單-->
                            <?php if($is_admin):?>
                                <div class="mt-4">
                                    <h6>更新訂單狀態</h6>
                                    <form method="POST" class="row g-3">
                                        <input type="hidden" name="order_id" value="<?=$order_details['order_id']?>">
                                        <div class="col-md-6">
                                            <select name="status" class="form-select" required>
                                                <option value="pending" <?=$order_details['status'] =='pending'?'selected':''?>>處理中</option>
                                                <option value="paid" <?=$order_details['status'] =='paid'?'selected':''?>>已付款</option>
                                                <option value="shipped" <?=$order_details['status'] =='shipped'?'selected':''?>>已出貨</option>
                                                <option value="completed" <?=$order_details['status'] =='completed'?'selected':''?>>已完成</option>
                                                <option value="cancelled" <?=$order_details['status'] =='cancelled'?'selected':''?>>已取消</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" name="update_status" class="btn btn-primary">更新狀態</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif;?>
                        </div>
                    <?php else:?>
                        <!--訂單列表視圖-->
                        <div class="card-body">
                            <?php if(empty($orders)):?>
                                <!--無訂單時的提示-->
                                <div class="text-center py-5">
                                    <i class="bi bi-cart-x" style="font-size:3rem; color:#6c757d;"></i>
                                    <h5 class="mt-3">您還沒有任何訂單</h5>
                                </div>
                            <?php else:?>
                                <!--訂單列表-->
                                <div class="list-group">
                                    <?php foreach($orders as $order):?>
                                        <a href="?order_id=<?=$order['order_id']?><?=$status_filter?'&status='.$status_filter:''?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">訂單 #<?=$order['order_id']?></h5>
                                                <span class="badge bg-<?=getStatusBadgeClass($order['status'])?>">
                                                    <?=getStatusText($order['status'])?>
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <div>
                                                    <small class="text-muted">訂單日期:<?=date('Y/m/d',strtotime($order['created_at']))?></small>
                                                </div>
                                                <div>
                                                    <strong>總金額:$<?=number_format($order['total_price'],2)?></strong>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach;?>
                                </div>
                            <?php endif;?>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>    
    <!--引入頁尾-->
    <?php include __DIR__.'/../includes/footer.php';?>
</body>
</html>