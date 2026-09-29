<?php
//資料庫連線設定常數
define('DB_SERVER','localhost');//資料庫伺服器位址（本地端）
define('DB_USERNAME','root');//資料庫使用者名稱
define('DB_PASSWORD','');//資料庫密碼（本地端通常為空）
define('DB_NAME','shop');//資料庫名稱
//建立資料庫連線物件
$conn=new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);
//檢查資料庫連線是否成功
if ($conn->connect_error){
    //若連線失敗，顯示錯誤訊息並終止程式
    die("連線失敗: " . $conn->connect_error);
}
//設定資料庫連線的編碼為 utf8mb4（支援多種語系與 Emoji）
$conn->set_charset("utf8mb4");
