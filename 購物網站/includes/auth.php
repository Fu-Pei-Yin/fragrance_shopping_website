<?php
//載入資料庫連線設定檔
require_once __DIR__.'/../config/database.php';
//使用者註冊函式
function registerUser($username,$password,$email,$phone,$locations,$confirm_password){
    global $conn;//使用全域變數資料庫連線
    //檢查使用者名稱是否已存在
    $stmt=$conn->prepare("SELECT user_id FROM user WHERE username=?");
    $stmt->bind_param("s",$username);//綁定參數
    $stmt->execute();//執行查詢
    if($stmt->get_result()->num_rows>0){
        return "使用者名稱已被使用";//回傳錯誤訊息
    }
    else if(strlen($username)<3||strlen($username)>20){
        return "使用者名稱長度必須介於3到20個字元";
    }
    //檢查電子郵件是否已存在
    $stmt=$conn->prepare("SELECT user_id FROM user WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    if($stmt->get_result()->num_rows>0){
        return "電子郵件已被使用";
    }
    // 檢查電話號碼長度
    if(strlen($phone)<10||strlen($phone)>15){
        return "電話號碼長度必須介於10到15個數字";
    }
    // 檢查電話號碼是否只包含數字或連字符
    else if(!preg_match('/^[0-9-]+$/', $phone)){
        return "電話號碼只能包含數字或連字符(-)";
    }
    if(strlen($locations)<5||strlen($locations)>100){
        return "地址長度必須介於5到100個字元";
    }
    if(strlen($password)<6||strlen($password)>20){
        return "密碼長度必須介於6到20個字元";
    }
    //檢查密碼與確認密碼是否一致
    else if($password!=$confirm_password){
        return "密碼與確定密碼不相符";
    }
    //插入使用者資料到資料庫
    $stmt=$conn->prepare("INSERT INTO user(username,password,email,phone,locations) VALUES(?,?,?,?,?)");
    $stmt->bind_param("sssss",$username,$password,$email,$phone,$locations);
    //若執行成功，則新增對應購物車資料
    if($stmt->execute()){
        $cart_id=$stmt->insert_id;//取得新註冊使用者的 ID
        $user_id=$stmt->insert_id;//取得新註冊使用者的 ID
        $stmt=$conn->prepare("INSERT INTO cart(user_id,cart_id) VALUES(?,?)");
        $stmt->bind_param("ii",$user_id,$cart_id);
        if($stmt->execute()){
            return true;//註冊成功
        } else {
            return "購物車建立失敗";
        }
    }
    //若失敗則回傳錯誤訊息
    return "註冊失敗，請稍後再試";
}

//使用者登入函式
function loginUser($username,$password){
    global $conn;//使用全域變數資料庫連線
    //檢查使用者名稱是否存在
    $stmt=$conn->prepare("SELECT user_id,password,is_admin FROM user WHERE username=?");
    $stmt->bind_param("s",$username);//綁定參數
    $stmt->execute();//執行查詢
    $result=$stmt->get_result()->fetch_assoc();//取得查詢結果
    if($result){//如果有查詢到使用者資料
        if($password==$result['password']){//驗證密碼是否正確
            //登入成功，設定 session
            $_SESSION['user_id']=$result['user_id'];
            $_SESSION['username']=$username;
            $_SESSION['is_admin']=$result['is_admin'];
            return true;//回傳成功
        } else {
            return "密碼錯誤";//密碼不正確
        }
    } else {
        return "使用者不存在";//使用者名稱不存在
    }
}