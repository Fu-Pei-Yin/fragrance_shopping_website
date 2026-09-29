<?php
require_once __DIR__.'/../config/database.php';//載入資料庫連線設定
//檢查使用者是否登入
function isLoggedIn(){
    return isset($_SESSION['user_id']);
}
//檢查使用者是否為管理員
//先檢查 session 中是否存在 'is_admin' 這個變數。這是必要的檢查，因為如果直接存取不存在的 session 變數會產生 PHP 警告。
//檢查實際獲取該變數的值來檢查是否為真（true）。只有在第一個檢查通過（變數存在）的情況下，才會執行這個檢查。
function isAdmin(){
    return isset($_SESSION['is_admin'])&&$_SESSION['is_admin'];
}
//取得所有商品
function getAllProducts(){
    global $conn;
    $sql="SELECT * FROM product";
    $result=$conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}
//取得單一商品資料
function getProductById($product_id){
    global $conn;
    $stmt=$conn->prepare("SELECT * FROM product WHERE product_id=?");
    $stmt->bind_param("i",$product_id);
    $stmt->execute();
    $result=$stmt->get_result();
    return $result->fetch_assoc();
}
//取得指定使用者的購物車內容
function getUsercart($user_id){
    global $conn;
    $stmt=$conn->prepare("
        SELECT 
            ci.cart_id,
            ci.product_id,
            ci.quantity,
            p.name,
            p.price,
            p.inventory,
            p.description
        FROM 
            cart c
        JOIN 
            cart_item ci ON c.cart_id=ci.cart_id
        JOIN 
            product p ON ci.product_id=p.product_id
        WHERE 
            c.user_id=?
    ");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
//計算使用者購物車總金額
function calculatecartTotal($user_id){
    global $conn;
    $stmt=$conn->prepare("
        SELECT SUM(p.price * ci.quantity) as total 
        FROM cart_item ci
        JOIN product p ON ci.product_id=p.product_id
        JOIN cart c ON ci.cart_id=c.cart_id
        WHERE c.user_id=?
    ");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $result=$stmt->get_result()->fetch_assoc();
    return $result['total']??0;
}
//取得使用者個人資料
function getUserProfile($user_id){
    global $conn;
    $stmt=$conn->prepare("SELECT user_id,username,email,regtime,phone,locations FROM user WHERE user_id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
//更新使用者個人資料（可選更新密碼）
function updateUserProfile($user_id,$username,$email,$phone=null,$locations=null,$new_password=null,$current_password){
    global $conn;
    //驗證當前密碼是否正確
    $stmt=$conn->prepare("SELECT password FROM user WHERE user_id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $user=$stmt->get_result()->fetch_assoc();
    if($current_password!=$user['password']){
        return "當前密碼不正確";
    }
    //確認使用者名稱是否唯一
    $stmt=$conn->prepare("SELECT user_id FROM user WHERE username=? AND user_id != ?");
    $stmt->bind_param("si",$username,$user_id);
    $stmt->execute();
    if($stmt->get_result()->num_rows>0){
        return "使用者名稱已被使用";
    }
    else if(strlen($username)<3 || strlen($username)>20){
        return "使用者名稱長度必須在3到20個字元之間";
    }
    //確認電子郵件是否唯一
    $stmt=$conn->prepare("SELECT user_id FROM user WHERE email=? AND user_id != ?");
    $stmt->bind_param("si",$email,$user_id);
    $stmt->execute();
    if($stmt->get_result()->num_rows>0){
        return "電子郵件已被使用";
    }
    if(strlen($phone)<10 || strlen($phone)>15){
        return "電話號碼長度必須在10到15個字元之間";
    }
    else if(!preg_match('/^[0-9-]+$/', $phone)){
        return "電話號碼只能包含數字或連字符(-)";
    }
    if(strlen($locations)<5 || strlen($locations)>100){
        return "地址長度必須在5到100個字元之間";
    }
    //準備更新資料
    $password_update="";
    $params=[$username,$email,$phone,$locations,$user_id];
    $types="sssis";
    //若有新密碼則加上
    if(!empty($new_password)){
        if(strlen($new_password)<6||strlen($new_password)>20){
            return "新密碼長度必須在6到20個字元之間";
        }else{
        $password_update=",password=?";
        $types .= "s";
        $params[]=$new_password;
        }
    }
    //執行更新
    $stmt=$conn->prepare("UPDATE user SET username=?,email=?,phone=?,locations=? {$password_update} WHERE user_id=?");
    $stmt->bind_param($types,...$params);
    return $stmt->execute()?true:"更新失敗，請稍後再試";
}
//管理員：取得所有訂單（可依狀態過濾）
function getAllOrders($status=null){
    global $conn;
    $query="SELECT o.* FROM `order` o 
              JOIN user u ON o.user_id=u.user_id
              WHERE u.is_admin=0";
    if($status!=null){
        $query .= " AND status=?";
    }
    $query .= " ORDER BY created_at DESC";
    $stmt=$conn->prepare($query);
    if($status!=null){
        $stmt->bind_param('s',$status);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
}
//使用者：取得個人所有訂單（可依狀態過濾）
function getUserOrders($user_id,$status=null){
    global $conn;
    $query="SELECT * FROM `order` WHERE user_id=?";
    if($status!=null){
        $query .= " AND status=?";
    }
    $query .= " ORDER BY created_at DESC";
    $stmt=$conn->prepare($query);
    if($status!=null){
        $stmt->bind_param('is',$user_id,$status);
    } else{
        $stmt->bind_param('i',$user_id);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
}
//取得訂單詳細資料（含商品）
function getOrderDetails($order_id){
    global $conn;
    //查詢訂單基本資訊
    $stmt=$conn->prepare("SELECT * FROM `order` WHERE order_id=?");
    $stmt->bind_param('i',$order_id);
    $stmt->execute();
    $result=$stmt->get_result();
    if($result->num_rows==0) return null;
    $order=$result->fetch_assoc();
    //查詢訂單商品
    $stmt=$conn->prepare("
        SELECT oi.*,p.name,p.price,p.inventory,p.description
        FROM order_item oi 
        JOIN product p ON oi.product_id=p.product_id 
        WHERE oi.order_id=?");
    $stmt->bind_param('i',$order_id);
    $stmt->execute();
    $order['items']=$stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
    return $order;
}
//更新訂單狀態
function updateOrderStatus($order_id,$new_status){
    global $conn;
    $stmt=$conn->prepare("UPDATE `order` SET status=?,updated_at=NOW() WHERE order_id=?");
    $stmt->bind_param('si',$new_status,$order_id);
    return $stmt->execute();
}
//驗證訂單是否屬於使用者
function orderBelongsToUser($order_id,$user_id){
    global $conn;
    $stmt=$conn->prepare("SELECT order_id FROM `order` WHERE order_id=? AND user_id=?");
    $stmt->bind_param('ii',$order_id,$user_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows>0;
}
//將訂單狀態轉為中文描述
function getStatusText($status){
    $statuses=[
        'pending'=>'處理中',
        'paid'=>'已付款',
        'shipped'=>'已出貨',
        'completed'=>'已完成',
        'cancelled'=>'已取消'
    ];
    return $statuses[$status]??$status;
}
//根據訂單狀態回傳Bootstrap標籤樣式
function getStatusBadgeClass($status){
    $classes=[
        'pending'=>'warning',
        'paid'=>'info',
        'shipped'=>'primary',
        'completed'=>'success',
        'cancelled'=>'danger'
    ];
    return $classes[$status]??'secondary';
}
//管理員：取得所有使用者列表
function getAllUsers(){
    global $conn;
    $sql="SELECT user_id,username,email,phone,locations,regtime,is_admin FROM user ORDER BY user_id ASC";
    $result=$conn->query($sql);
    return $result?$result->fetch_all(MYSQLI_ASSOC):[];
}
//管理員：刪除使用者
function deleteUser($user_id){
    global $conn;
    $stmt=$conn->prepare("DELETE FROM user WHERE user_id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    return $stmt->affected_rows>0?true:"刪除失敗：找不到該使用者";
}
//管理員：切換使用者是否為管理員
function toggleAdminStatus($user_id,$is_admin){
    global $conn;
    $stmt=$conn->prepare("UPDATE user SET is_admin=? WHERE user_id=?");
    $stmt->bind_param("ii",$is_admin,$user_id);
    $stmt->execute();
    return $stmt->affected_rows>0?true:"權限更新失敗或沒有變更內容";
}
//查詢購物車中商品的庫存情況
function checkInventory($user_id,$conn){
    $stmt=$conn->prepare("SELECT p.product_id,p.name,p.inventory,ci.quantity 
                           FROM Cart_Item ci
                           JOIN Product p ON ci.product_id=p.product_id
                           WHERE ci.cart_id =(SELECT cart_id FROM Cart WHERE user_id=?)");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $result=$stmt->get_result();
    //檢查每項商品的庫存是否足夠
    while($row=$result->fetch_assoc()){
        if($row['inventory'] < $row['quantity']){
            return [
                'success' => false,
                'message' => "商品 '{$row['name']}' 庫存不足(庫存:{$row['inventory']},需求量:{$row['quantity']})"
            ];
        }
    }
    return ['success' => true];
}