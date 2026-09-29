<?php
//啟用 session 功能
session_start();
//引入共用函式檔案
require_once __DIR__.'/../includes/functions.php';
//檢查使用者是否已登入
if (!isLoggedIn()){
    $_SESSION['error']="請先登入後再操作";
    header("Location:/final_project/user/login.php");
    exit;
}
//只處理 POST 請求
if ($_SERVER['REQUEST_METHOD']!='POST'){
    $_SESSION['error']="無效的請求方法";
    header("Location:/final_project/products/list_products.php");
    exit;
}
//驗證必要的參數是否存在
if (!isset($_POST['product_id'])||!isset($_POST['quantity'])){
    $_SESSION['error']="缺少必要的參數";
    header("Location:".($_SERVER['HTTP_REFERER']??'/final_project/products/list_products.php'));
    exit;
}
//獲取並轉換參數為整數
$product_id=(int)$_POST['product_id'];
$quantity=(int)$_POST['quantity'];
$user_id=(int)$_SESSION['user_id'];
//驗證商品ID有效性
if ($product_id<=0){
    $_SESSION['error']="無效的商品ID";
    header("Location:".($_SERVER['HTTP_REFERER']??'/final_project/products/list_products.php'));
    exit;
}
//驗證數量有效性
if ($quantity<=0){
    $_SESSION['error']="數量必須是正整數";
    header("Location:".($_SERVER['HTTP_REFERER']??'/final_project/products/list_products.php'));
    exit;
}
try{
    // 開始資料庫事務
    $conn->begin_transaction();
    // 檢查商品庫存並鎖定該筆記錄
    $stmt=$conn->prepare("SELECT inventory,name FROM Product WHERE product_id=? FOR UPDATE");
    $stmt->bind_param("i",$product_id);
    $stmt->execute();
    $result=$stmt->get_result();
    $product=$result->fetch_assoc();
    // 檢查商品是否存在
    if (!$product){
        throw new Exception("商品不存在");
    }
    // 檢查庫存是否足夠
    if ($product['inventory']<$quantity){
        throw new Exception("「{$product['name']}」庫存不足，目前僅剩{$product['inventory']} 件");
    }
    // 檢查使用者是否有購物車並鎖定記錄
    $stmt=$conn->prepare("SELECT cart_id FROM Cart WHERE user_id=? FOR UPDATE");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $result=$stmt->get_result();
    $cart=$result->fetch_assoc();
    // 如果沒有購物車則創建一個
    if (!$cart){
        $stmt=$conn->prepare("INSERT INTO Cart (user_id) VALUES (?)");
        $stmt->bind_param("i",$user_id);
        if (!$stmt->execute()){
            throw new Exception("無法創建購物車");
        }
        $cart_id=$conn->insert_id;
    } else{
        $cart_id=$cart['cart_id'];
    }
    // 檢查商品是否已在購物車中並鎖定記錄
    $stmt=$conn->prepare("SELECT cart_id,quantity FROM cart_item 
                           WHERE product_id=? AND cart_id=? FOR UPDATE");
    $stmt->bind_param("ii",$product_id,$cart_id);
    $stmt->execute();
    $result=$stmt->get_result();
    $existing_item=$result->fetch_assoc();
    if ($existing_item){
        // 計算新數量並檢查是否超過庫存
        $new_quantity=$existing_item['quantity'] + $quantity;
        if ($new_quantity > $product['inventory']){
            throw new Exception("「{$product['name']}」購物車中該商品數量已達庫存上限");
        }
        // 更新購物車中商品數量
        $stmt=$conn->prepare("UPDATE cart_item SET quantity=? WHERE cart_id=?");
        $stmt->bind_param("ii",$new_quantity,$existing_item['cart_id']);
    } else{
        // 新增商品到購物車
        $stmt=$conn->prepare("INSERT INTO cart_item (cart_id,product_id,quantity) VALUES (?,?,?)");
        $stmt->bind_param("iii",$cart_id,$product_id,$quantity);
    }
    // 執行SQL並檢查結果
    if (!$stmt->execute()){
        throw new Exception("無法更新購物車");
    }
    // 提交事務
    $conn->commit();
    $_SESSION['success']="商品「{$product['name']}」已成功添加到購物車！";
} catch (Exception $e){
    // 發生錯誤時回滾事務
    if (isset($conn)){
        $conn->rollback();
    }
    $_SESSION['error']=$e->getMessage();
}
//重定向回原頁面
header("Location:".($_SERVER['HTTP_REFERER']??'/final_project/products/list_products.php'));
exit;
?>