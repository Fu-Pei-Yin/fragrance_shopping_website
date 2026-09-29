<?php
//啟用 session 功能
session_start();
//引入共用函式檔案
require_once __DIR__.'/../includes/functions.php';
//檢查用戶是否已登入
if(!isLoggedIn()){
    header("Location: /final_project/user/login.php");
    exit;
}
//從 session 獲取用戶ID
$user_id=$_SESSION['user_id'];
//獲取用戶購物車內容
$cart_items=getUserCart($user_id);
//檢查購物車是否為空
if(empty($cart_items)){
    header("Location: /final_project/cart/view_cart.php");
    exit;
}
//查詢用戶基本資訊
$user_info=[];
$stmt=$conn->prepare("SELECT username,email,phone,locations FROM User WHERE user_id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result=$stmt->get_result();
if($result->num_rows > 0){
    $user_info=$result->fetch_assoc();
}
//處理結帳 POST 請求
if($_SERVER['REQUEST_METHOD']=='POST'){
    //開始資料庫事務
    $conn->begin_transaction();
    try{
        //1.檢查商品庫存是否足夠
        $inventory_check=checkInventory($user_id,$conn);
        if(!$inventory_check['success']){
            throw new Exception($inventory_check['message']);
        }
        //2.計算購物車總金額
        $total=calculateCartTotal($user_id);
        //3.建立新訂單記錄
        $stmt=$conn->prepare("INSERT INTO `Order`(user_id,total_price,status,created_at) 
                               VALUES(?,?,'pending',NOW())");
        $stmt->bind_param("id",$user_id,$total);
        $stmt->execute();
        $order_id=$conn->insert_id;
        //4.將購物車項目轉換為訂單項目
        $stmt=$conn->prepare("INSERT INTO Order_Item(order_id,product_id,quantity,price)
                              SELECT?,ci.product_id,ci.quantity,p.price
                              FROM Cart_Item ci
                              JOIN Product p ON ci.product_id=p.product_id
                              WHERE ci.cart_id =(SELECT cart_id FROM Cart WHERE user_id=?)");
        $stmt->bind_param("ii",$order_id,$user_id);
        $stmt->execute();
        //5.更新商品庫存數量
        $stmt=$conn->prepare("UPDATE Product p
                              JOIN Cart_Item ci ON p.product_id=ci.product_id
                              SET p.inventory=p.inventory-ci.quantity
                              WHERE ci.cart_id =(SELECT cart_id FROM Cart WHERE user_id=?)");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        //6.清空購物車
        $stmt=$conn->prepare("DELETE FROM Cart_Item WHERE cart_id =(SELECT cart_id FROM Cart WHERE user_id=?)");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        //提交事務
        $conn->commit();
        //設置成功訊息並跳轉到訂單頁面
        $_SESSION['success']="訂單 #$order_id 已成功建立！";
        header("Location: /final_project/user/orders.php");
        exit;
    } catch(Exception $e){
        //發生錯誤時回滾事務
        $conn->rollback();
        //設置錯誤訊息並返回結帳頁面
        $_SESSION['error']="結帳過程中發生錯誤: ".$e->getMessage();
        header("Location: /final_project/cart/checkout.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <!--基本頁面設定-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>結帳-電子商務網站</title>
    <!--引入 Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--引入 Bootstrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!--引入頁首-->
    <?php include __DIR__.'/../includes/header.php';?>
    <div class="container mt-4">
        <!--顯示成功訊息-->
        <?php if(isset($_SESSION['success'])):?>
            <div class="alert alert-success"><?=$_SESSION['success']; unset($_SESSION['success']);?></div>
        <?php endif;?>
        <!--顯示錯誤訊息-->
        <?php if(isset($_SESSION['error'])):?>
            <div class="alert alert-danger"><?=$_SESSION['error']; unset($_SESSION['error']);?></div>
        <?php endif;?>
        <!--結帳表單-->
        <h2 class="mb-4">結帳</h2>
        <form method="post">
            <div class="row">
                <!--左側區塊-訂單明細和送貨資訊-->
                <div class="col-md-8">
                    <!--訂單明細卡片-->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>訂單明細</h4>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>商品</th>
                                        <th>單價</th>
                                        <th>數量</th>
                                        <th>小計</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--循環顯示購物車商品-->
                                    <?php foreach($cart_items as $item):?>
                                        <tr>
                                            <td><?=htmlspecialchars($item['name'])?></td>
                                            <td>NT$ <?=number_format($item['price'])?></td>
                                            <td><?=$item['quantity']?></td>
                                            <td>NT$ <?=number_format($item['price'] * $item['quantity'])?></td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--送貨資訊卡片-->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>送貨資訊</h4>
                        </div>
                        <div class="card-body">
                            <!--收件人姓名輸入框-->
                            <div class="mb-3">
                                <label for="shipping_name" class="form-label">收件人姓名</label>
                                <input type="text" class="form-control" id="shipping_name" name="shipping_name" 
                                       value="<?=htmlspecialchars($user_info['username']?? '')?>" required>
                            </div>
                            <!--聯絡電話輸入框-->
                            <div class="mb-3">
                                <label for="shipping_phone" class="form-label">聯絡電話</label>
                                <input type="text" class="form-control" id="shipping_phone" name="shipping_phone" 
                                       value="<?=htmlspecialchars($user_info['phone']?? '')?>" required>
                            </div>
                            <!--送貨地址輸入框-->
                            <div class="mb-3">
                                <label for="shipping_locations" class="form-label">送貨地址</label>
                                <textarea class="form-control" id="shipping_locations" name="shipping_locations" required><?=htmlspecialchars($user_info['locations']?? '')?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!--右側區塊-訂單總計-->
                <div class="col-md-4">
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-header">
                            <h4>訂單總計</h4>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <!--商品總計-->
                                <tr>
                                    <th>商品總計</th>
                                    <td>NT$ <?=number_format(calculateCartTotal($user_id))?></td>
                                </tr>
                                <!--運費-->
                                <tr>
                                    <th>運費</th>
                                    <td>NT$ 0</td>
                                </tr>
                                <!--總計-->
                                <tr class="fw-bold">
                                    <th>總計</th>
                                    <td>NT$ <?=number_format(calculateCartTotal($user_id))?></td>
                                </tr>
                            </table>
                            <!--確認結帳按鈕-->
                            <button type="submit" class="btn btn-primary w-100">確認結帳</button>
                            <!--返回購物車按鈕-->
                            <a href="/final_project/cart/view_cart.php" class="btn btn-outline-secondary w-100 mt-2">返回購物車</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!--引入頁尾-->
    <?php include __DIR__.'/../includes/footer.php';?>
</body>
</html>