<?php
session_start();
require_once __DIR__.'/includes/functions.php';
require_once __DIR__.'/includes/header.php';
// 資料庫連接
$db=new PDO('mysql:host=localhost;dbname=shop;charset=utf8','root','');
// 處理表單提交
if($_SERVER['REQUEST_METHOD']=='POST'){
    $name=filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING);
    $phone=filter_input(INPUT_POST,'phone',FILTER_SANITIZE_STRING);
    $email=filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
    $message=filter_input(INPUT_POST,'message',FILTER_SANITIZE_STRING);
    // 驗證必填欄位
    if(!empty($name)&&!empty($email)&&!empty($message)){
        try{
            $stmt=$db->prepare("INSERT INTO feedback(name,phone,email,message)VALUES(?,?,?,?)");
            $stmt->execute([$name,$phone,$email,$message]);
            // 清空表單值
            $name=$phone=$email=$message='';
            $success="感謝您的回饋！我們會盡快處理您的意見。";
        } catch(PDOException $e){
            $error="提交失敗，請稍後再試。錯誤: ".$e->getMessage();
        }
    } else{
        $error="請填寫所有必填欄位（姓名、電子郵件和建議內容）";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>意見回饋</title>
    <!-- 必要的JS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .feedback-form{
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group{
            margin-bottom: 20px;
        }
        label{
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="tel"],
        input[type="email"],
        textarea{
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        textarea{
            height: 150px;
        }
        .required:after{
            content: " *";
            color: red;
        }
        .btn-submit{
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-submit:hover{
            background-color: #45a049;
        }
        .alert{
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success{
            background-color: #dff0d8;
            color: #3c763d;
        }
        .alert-error{
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <!-- 導航欄 -->
    <?php include_once __DIR__.'/includes/header.php'; ?>
    <div class="container">
        <h1>意見回饋</h1>
        <!--提示訊息-->
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <!--表單內容-->
        <form class="feedback-form" method="POST" action="contact.php">
            <div class="form-group">
                <label for="name" class="required">姓名</label>
                <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name'])? htmlspecialchars($_POST['name']): ''; ?>">
            </div>
            <div class="form-group">
                <label for="phone">電話</label>
                <input type="tel" id="phone" name="phone" value="<?php echo isset($_POST['phone'])? htmlspecialchars($_POST['phone']): ''; ?>">
            </div>
            <div class="form-group">
                <label for="email" class="required">電子郵件</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email'])? htmlspecialchars($_POST['email']): ''; ?>">
            </div>
            <div class="form-group">
                <label for="message" class="required">建議內容</label>
                <textarea id="message" name="message" required><?php echo isset($_POST['message'])? htmlspecialchars($_POST['message']): ''; ?></textarea>
            </div>
            <button type="submit" class="btn-submit">提交回饋</button>
        </form>
    </div>
    <?php include_once __DIR__.'/includes/footer.php'; ?>
</body>
</html>