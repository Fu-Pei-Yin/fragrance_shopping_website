<?php
//啟用 session 功能
session_start();
//引入共用函式檔案
require_once __DIR__.'/../includes/functions.php';
//檢查用戶是否已登入
if (!isLoggedIn()){    
    //設置錯誤訊息並跳轉至登入頁面
    $_SESSION['error']="請先登入才能修改購物車";
    header("Location:/final_project/user/login.php");
    exit;
}
//驗證請求方法是否為 POST 或帶有 action 和 product_id 參數的 GET
if ($_SERVER['REQUEST_METHOD']!='POST'&&!(isset($_GET['action'])&&isset($_GET['product_id']))){
    //設置錯誤訊息並跳轉回購物車頁面
    $_SESSION['error']="無效的請求方式";
    header("Location:/final_project/cart/view_cart.php");
    exit;
}
//從 session 獲取用戶ID並轉為整數
$user_id=(int)$_SESSION['user_id'];
//開始資料庫事務處理
try{
    $conn->begin_transaction();
    //處理更新商品數量的 POST 請求
    if ($_SERVER['REQUEST_METHOD']=='POST'&&isset($_POST['product_id'],$_POST['quantity'])){
        //獲取並轉換商品ID和數量為整數
        $product_id=(int)$_POST['product_id'];
        $quantity=(int)$_POST['quantity'];
        //檢查數量是否有效
        if ($quantity <= 0){
            throw new Exception("數量必須是大於零的整數");
        }
        //準備查詢購物車商品資訊的SQL
        $stmt=$conn->prepare("
            SELECT p.product_id,p.inventory,p.name,p.price,ci.quantity AS current_quantity
            FROM cart_item ci
            JOIN product p ON ci.product_id=p.product_id
            JOIN cart c ON ci.cart_id=c.cart_id
            WHERE c.user_id=? AND ci.product_id=?
        ");
        $stmt->bind_param("ii",$user_id,$product_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $item=$result->fetch_assoc();
        //檢查商品是否存在
        if (!$item){
            throw new Exception("找不到此商品或商品已被移除");
        }
        //檢查庫存是否足夠
        if ($item['inventory'] < $quantity){
            throw new Exception("「{$item['name']}」庫存不足，目前僅剩{$item['inventory']} 件");
        }
        //更新購物車數量
        $stmt=$conn->prepare("
            UPDATE cart_item 
            SET quantity=? 
            WHERE product_id=? AND cart_id IN (SELECT cart_id FROM cart WHERE user_id=?)
        ");
        $stmt->bind_param("iii",$quantity,$product_id,$user_id);
        //執行更新並檢查結果
        if (!$stmt->execute()){
            throw new Exception("更新購物車失敗");
        }
        //設置成功訊息
        $_SESSION['success']="「{$item['name']}」數量已更新為{$quantity} 件";
    }   //處理刪除商品的 GET 請求
    elseif (isset($_GET['action'],$_GET['product_id'])&&$_GET['action']=='delete'){
        //獲取並轉換商品ID為整數
        $product_id=(int)$_GET['product_id'];
        //準備查詢商品資訊的SQL
        $stmt=$conn->prepare("
            SELECT p.name,ci.quantity,p.price 
            FROM cart_item ci
            JOIN product p ON ci.product_id=p.product_id
            JOIN cart c ON ci.cart_id=c.cart_id
            WHERE c.user_id=? AND ci.product_id=?
        ");
        $stmt->bind_param("ii",$user_id,$product_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $item=$result->fetch_assoc();
        //檢查商品是否存在
        if (!$item){
            throw new Exception("找不到此商品或商品已被移除");
        }
        //準備刪除商品的SQL
        $stmt=$conn->prepare("
            DELETE FROM cart_item 
            WHERE product_id=? AND cart_id IN (SELECT cart_id FROM cart WHERE user_id=?)
        ");
        $stmt->bind_param("ii",$product_id,$user_id);
        //執行刪除並檢查結果
        if (!$stmt->execute()){
            throw new Exception("移除商品失敗");
        }
        //設置成功刪除的訊息
        if ($stmt->affected_rows>0){
            $total_removed=$item['price'] * $item['quantity'];
            $_SESSION['success']="「{$item['name']}」({$item['quantity']} 件，共 NT$ ".number_format($total_removed).") 已從購物車移除";
        }
    } else{
        //處理無效的操作請求
        throw new Exception("無效的操作請求");
    }
    //提交事務
    $conn->commit();
} catch (Exception $e){    
    //發生錯誤時回滾事務
    if (isset($conn)){
        $conn->rollback();
    }   //設置錯誤訊息並記錄錯誤日誌
    $_SESSION['error']=$e->getMessage();
    error_log("購物車更新錯誤:".$e->getMessage()." 使用者ID:$user_id");
}
//跳轉回購物車頁面
header("Location:/final_project/cart/view_cart.php");
exit;
?>