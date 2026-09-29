<?php
session_start(); // 啟動 session，才能使用與清除 session 資料
session_unset(); // 清除所有 session 變數（但不銷毀 session ID）
session_destroy(); // 銷毀整個 session（包含 session ID 與資料）
// 導向登入頁面
header("Location:/final_project/user/login.php");
exit; // 確保後續程式碼不會執行
?>
