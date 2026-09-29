<?php
session_start();//啟動 session 功能
require_once __DIR__.'/../includes/functions.php';//載入自訂函式庫
//如果使用者尚未登入，導向登入頁面
if(!isLoggedIn()){
    header("Location: /final_project/user/login.php");
    exit;
}
$order_id=$_GET['id']??0;//從 URL 取得訂單 ID，若無則預設為 0
$user_id=$_SESSION['user_id'];//取得目前登入使用者的 ID
//驗證該訂單是否屬於當前登入使用者
$stmt=$conn->prepare("SELECT o.* FROM `order` o WHERE o.order_id=? AND o.user_id=?");
$stmt->bind_param("ii",$order_id,$user_id);
$stmt->execute();
$order=$stmt->get_result()->fetch_assoc();
//若查無此訂單或不是使用者的訂單，則導回訂單列表頁
if(!$order){
    header("Location: /final_project/user/orders.php");
    exit;
}
//查詢該訂單中的所有商品資料
$stmt=$conn->prepare("
    SELECT oi.*,p.name 
    FROM order_Item oi 
    JOIN product p ON oi.product_id=p.product_id 
    WHERE oi.order_id=?
");
$stmt->bind_param("i",$order_id);
$stmt->execute();
$items=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);//以關聯陣列格式取得所有商品
?>
<!--引入頁首導航欄-->
<?php include_once __DIR__.'/../includes/header.php';?>
<!--顯示訂單編號-->
<h2>訂單詳情 #<?=$order['order_id']?></h2>
<div class="row">
    <div class="col-md-8">
        <!--商品明細表格-->
        <table class="table">
            <thead>
                <tr>
                    <th>商品名稱</th>
                    <th>單價</th>
                    <th>數量</th>
                    <th>小計</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item):?>
                    <tr>
                        <!--商品名稱，使用 htmlspecialchars 防止 XSS-->
                        <td><?=htmlspecialchars($item['name'])?></td>
                        <!--單價格式化為台幣-->
                        <td>NT$ <?=number_format($item['price'])?></td>
                        <!--數量-->
                        <td><?=$item['quantity']?></td>
                        <!--小計：單價乘以數量-->
                        <td>NT$ <?=number_format($item['price'] * $item['quantity'])?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
            <tfoot>
                <!--訂單總價-->
                <tr>
                    <th colspan="3">總計</th>
                    <th>NT$ <?=number_format($order['total_price'])?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-md-4">
        <!--訂單額外資訊卡片-->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">訂單信息</h5>
                <!--訂單狀態-->
                <p><strong>狀態:</strong>
                    <?=getStatusText($order['status'])?>
                </p>
                <!--訂單建立時間-->
                <p><strong>下單時間:</strong> <?=$order['created_at']?></p>
                <!--訂單最後更新時間-->
                <p><strong>最後更新:</strong> <?=$order['updated_at']?></p>
            </div>
        </div>
    </div>
</div>
<!--返回訂單列表的按鈕-->
<a href="/final_project/user/orders.php" class="btn btn-secondary mt-3">返回訂單列表</a>
<?php require_once __DIR__.'/../includes/footer.php';//載入頁尾 HTML?>
