<?php
//啟用 session 功能
session_start();
//引入共用函數文件
require_once __DIR__ . '/../includes/functions.php';
//檢查當前用戶是否為管理員，若不是則跳轉回首頁
if(!isAdmin()){
    header("Location:/final_project/index.php");
    exit;
}
//從 GET 參數獲取要刪除的商品 ID，若無則設為 0
$product_id=$_GET['id'] ?? 0;
//當有傳入商品 ID 時執行刪除操作
if($product_id){
    //準備 SQL 刪除語句，使用參數化查詢防止 SQL 注入
    $stmt=$conn->prepare("DELETE FROM product WHERE product_id=?");
    //綁定參數(i 表示整數類型)
    $stmt->bind_param("i", $product_id);
    //執行刪除操作
    if($stmt->execute()){
        //刪除成功，設置成功訊息
        $_SESSION['success']="商品已成功删除";
    }else{
        //刪除失敗，設置錯誤訊息
        $_SESSION['error']="删除商品失敗，請稍後再試";
    }
}
//無論成功或失敗，都跳轉回商品管理頁面
header("Location:/final_project/admin/products.php");
exit;
?>