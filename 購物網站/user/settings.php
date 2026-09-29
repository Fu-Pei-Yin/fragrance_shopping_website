<?php
// 啟用 session
session_start();
// 引入必要的函式庫和驗證文件
require_once __DIR__.'/../includes/functions.php';
require_once __DIR__.'/../includes/auth.php';

// 檢查用戶是否登入，未登入則跳轉到登入頁面
if(!isLoggedIn()){
    header("Location:/final_project/user/login.php");
    exit;
}
// 獲取當前用戶資料
$user=getUserProfile($_SESSION['user_id']);
// 處理表單提交
if($_SERVER['REQUEST_METHOD']=='POST'&&isset($_POST['update_profile'])){
    // 獲取並清理表單數據
    $username=trim($_POST['username']);
    $email=trim($_POST['email']);
    $phone=trim($_POST['phone']);
    $locations=trim($_POST['locations']);
    $current_password=trim($_POST['current_password']);
    $new_password=trim($_POST['new_password']);
    // 基本驗證
    $errors=[];
    if(empty($username)){
        $errors[]='使用者名稱不能為空';
    }
    if(empty($email)){
        $errors[]='請輸入有效的電子郵件地址';
    }
    if(empty($phone)){
        $errors[]='請輸入有效的電話號碼';
    }
    if(empty($locations)){
        $errors[]='請輸入有效的地址';
    }
    // 只有在有輸入新密碼時才驗證密碼
    if(!empty($new_password)){
        if(empty($current_password)){
            $errors[]='請輸入當前密碼以更改密碼';
        }
    }
    // 如果沒有錯誤，則更新用戶資料
    if(empty($errors)){
        $result=updateUserProfile(
            $_SESSION['user_id'],
            $username,
            $email,
            $phone,
            $locations,
            !empty($new_password)? $new_password :null,
            $current_password
        );
        $update_password = !empty($new_password) ? ", password=?" : "";
        if($result===true){
            // 更新成功，執行資料庫更新
            global $conn;
            $stmt=$conn->prepare("UPDATE user SET username=?,email=?,phone=?,locations=? {$update_password} WHERE user_id=?");
            if(!empty($new_password)){
                $stmt->bind_param("sssssi", $username, $email, $phone, $locations,$new_password,$_SESSION['user_id']);
            }
            else{
                $stmt->bind_param("ssssi", $username, $email, $phone, $locations, $_SESSION['user_id']);
            }
            $stmt->execute();
            $success='帳號設定已更新成功';
            // 刷新用戶資料
            $user=getUserProfile($_SESSION['user_id']);
            $_SESSION['username']=$user['username']; // 更新 session 中的用戶名
        }else{
            $error=$result;
        }
    }else{
        $error=implode('<br>',$errors);
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width,initial-scale=1.0">
   <title>帳號設定-<?=htmlspecialchars($user['username'])?></title>
   <!--引入 Bootstrap CSS-->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <!--引入 JavaScript 庫-->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
   <script>
        // 密碼顯示/隱藏切換功能
        document.querySelectorAll('.password-toggle').forEach(button =>{
            button.addEventListener('click',function(){
                const targetId=this.getAttribute('data-target');
                const input=document.getElementById(targetId);
                const icon=this.querySelector('i');
                if(input.type=='password'){
                    input.type='text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }else{
                    input.type='password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
   </script>
   <style>
        /* 表單區塊樣式 */
        .form-section{
            margin-bottom:2rem;
            padding-bottom:1.5rem;
            border-bottom:1px solid #eee;
        }
        .form-section h5{
            margin-bottom:1.5rem;
            color:#444;
        }
        /* 密碼顯示切換按鈕樣式 */
        .password-toggle{
            cursor:pointer;
            color:#666;
        }
        .password-toggle:hover{
            color:#333;
        }
        /* 提示訊息樣式 */
        .alert{
            margin-bottom:1.5rem;
        }
   </style>
</head>
<body>
   <!--引入頁首-->
   <?php include __DIR__.'/../includes/header.php';?>
   <div class="container mt-5">
       <div class="row">
           <!--左側導覽列-->
           <div class="col-md-3">
               <div class="card">
                   <div class="card-body text-center">
                       <h5><?=htmlspecialchars($user['username'])?></h5>
                       <?php if(isAdmin()):?>
                           <p class="text-muted">管理員 since<?=date('Y/m/d',strtotime($user['regtime']))?></p>
                       <?php else:?>
                           <p class="text-muted">會員 since<?=date('Y/m/d',strtotime($user['regtime']))?></p>
                       <?php endif;?>
                   </div>
                   <ul class="list-group list-group-flush">
                       <?php if(isAdmin()):?>
                           <!--管理員專用導覽-->
                           <li class="list-group-item"><a href="/final_project/admin/products.php">商品管理</a></li>
                           <li class="list-group-item"><a href="/final_project/admin/orders.php">訂單管理</a></li>
                           <li class="list-group-item"><a href="/final_project/admin/users.php">會員管理</a></li>
                           <li class="list-group-item"><a href="/final_project/user/profile.php">個人資料</a></li>
                           <li class="list-group-item active">帳號設定</li>
                           <li class="list-group-item"><a href="/final_project/user/logout.php">登出</a></li>
                       <?php else:?>
                           <!--一般用戶導覽-->
                           <li class="list-group-item"><a href="/final_project/user/orders.php">我的訂單</a></li>
                           <li class="list-group-item"><a href="/final_project/user/profile.php">個人資料</a></li>
                           <li class="list-group-item active">帳號設定</li>
                           <li class="list-group-item"><a href="/final_project/user/logout.php">登出</a></li>
                       <?php endif;?>
                   </ul>
                </div>
            </div>
           <!--右側主要內容區-->
           <div class="col-md-9">
               <div class="card">
                   <div class="card-header">
                       <h4>帳號設定</h4>
                   </div>
                   <div class="card-body">
                    <!--成功訊息顯示-->
                    <?php if(isset($success)):?>
                        <div class="alert alert-success"><?=$success?></div>
                    <?php endif;?>
                    <!--錯誤訊息顯示-->
                    <?php if(isset($error)):?>
                        <div class="alert alert-danger"><?=$error?></div>
                    <?php endif;?>
                       <form method="POST">
                           <!--基本資料區塊-->
                           <div class="form-section">
                               <h5>基本資料</h5>
                               <div class="mb-3">
                                   <label for="username" class="form-label">使用者名稱</label>
                                   <input type="text" class="form-control" id="username" name="username" 
                                    value="<?=htmlspecialchars($user['username'])?>" required>
                               </div>
                               <div class="mb-3">
                                   <label for="email" class="form-label">電子郵件</label>
                                   <input type="email" class="form-control" id="email" name="email" 
                                    value="<?=htmlspecialchars($user['email'])?>" required>
                               </div>
                               <div class="mb-3">
                                   <label for="phone" class="form-label">電話號碼</label>
                                   <input type="tel" class="form-control" id="phone" name="phone" 
                                    value="<?=htmlspecialchars($user['phone'])?>">
                               </div>
                               <div class="mb-3">
                                   <label for="locations" class="form-label">住址</label>
                                   <input type="text" class="form-control" id="locations" name="locations" 
                                    value="<?=htmlspecialchars($user['locations'])?>">
                               </div>
                           </div>
                           <!--密碼變更區塊-->
                           <div class="form-section">
                               <h5>更改密碼</h5>
                               <div class="mb-3">
                                   <label for="current_password" class="form-label">當前密碼</label>
                                   <input type="password" class="form-control" id="current_password" name="current_password" required>
                                   <div class="form-text">需要提供當前密碼以確認變更</div>
                               </div>
                               <div class="mb-3">
                                   <label for="new_password" class="form-label">新密碼(留空則不變更)</label>
                                   <input type="password" class="form-control" id="new_password" name="new_password">
                               </div>
                           </div>
                           <!--表單按鈕-->
                           <div class="d-flex justify-content-end mt-4">
                               <button type="reset" class="btn btn-outline-secondary me-2">重設</button>
                               <button type="submit" class="btn btn-primary" name="update_profile">儲存變更</button>
                           </div>
                       </form>
                   </div>
               </div>
           </div>
       </div>
   </div>
   <!--引入頁尾-->
   <?php include __DIR__.'/../includes/footer.php';?>
</body>
</html>