<?php
// 確保 session 已啟動
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 引入必要的函式庫和頁首、驗證文件
require_once __DIR__.'/../includes/functions.php';
require_once __DIR__.'/../includes/auth.php';

// 檢查用戶是否已登入，已登入則跳轉至首頁
if(isLoggedIn()){
    header("Location:/final_project/index.php");
    exit;
}

// 初始化變數
$username = $email = $phone = $locations = '';
$_SESSION['success']=''; // 清除成功訊息
// 處理表單提交
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // 獲取並清理表單數據
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $locations = trim($_POST['locations']);
    $confirm_password = $_POST['confirm_password'];
    
    // 呼叫註冊函式
    $result = registerUser($username, $password, $email, $phone, $locations, $confirm_password);
    
    // 根據註冊結果進行處理
    if($result === true){
        // 註冊成功，設置成功訊息並跳轉至登入頁
        $_SESSION['success'] = "註冊成功，請登入";
        header("Location: /final_project/user/login.php");
        exit;
    } else {
        // 註冊失敗，保存錯誤訊息和表單數據
        $_SESSION['error'] = $result;
        $_SESSION['form_data'] = [
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'locations' => $locations
        ];
        // 重新載入註冊頁面以顯示錯誤
        header("Location: /final_project/user/register.php");
        exit;
    }
}

// 從 session 中獲取並清除錯誤訊息和表單數據
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
if(isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}

$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [
    'username' => '',
    'email' => '',
    'phone' => '',
    'locations' => ''
];
if(isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}

// 從 session 獲取並清除成功訊息
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
if(isset($_SESSION['success'])) {
    unset($_SESSION['success']);
}
?>
<!--引入頁首導航欄-->
<?php include_once __DIR__.'/../includes/header.php'; ?>
<!--頁面標題-->
<h2>使用者註冊</h2>
<!-- 提示訊息 -->
    <?php if($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
<!--註冊表單-->
<form method="post">
    <!--使用者名稱輸入欄-->
    <div class="mb-3">
        <label for="username" class="form-label">使用者名稱</label>
        <input type="text" class="form-control" id="username" name="username" 
               value="<?= htmlspecialchars($form_data['username']) ?>" required>
    </div>
    <!--電子郵件輸入欄-->
    <div class="mb-3">
        <label for="email" class="form-label">電子郵件</label>
        <input type="email" class="form-control" id="email" name="email" 
               value="<?= htmlspecialchars($form_data['email']) ?>" required>
    </div>
    <!--電話號碼輸入欄-->
    <div class="mb-3">
        <label for="phone" class="form-label">電話號碼</label>
        <input type="tel" class="form-control" id="phone" name="phone" 
               value="<?= htmlspecialchars($form_data['phone']) ?>" required>
    </div>
    <!--地址輸入欄-->
    <div class="mb-3">
        <label for="locations" class="form-label">地址</label>
        <input type="text" class="form-control" id="locations" name="locations" 
               value="<?= htmlspecialchars($form_data['locations']) ?>" required>
    </div>
    <!--密碼輸入欄-->
    <div class="mb-3">
        <label for="password" class="form-label">密碼</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <!--確認密碼輸入欄-->
    <div class="mb-3">
        <label for="confirm_password" class="form-label">確定密碼</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
    </div>
    <!--提交按鈕-->
    <button type="submit" class="btn btn-primary">註冊</button>
    <!--登入連結-->
    <p class="mt-3">已經有帳號？<a href="/final_project/user/login.php">登入</a></p>
</form>
<!--引入頁尾-->
<?php require_once __DIR__.'/../includes/footer.php'; ?>