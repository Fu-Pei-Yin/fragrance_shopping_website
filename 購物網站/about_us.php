<?php
// 啟動 session 用於存儲用戶會話數據
session_start();

// 引入共用函數文件
require_once __DIR__.'/includes/functions.php';
// 建立與 MySQL 資料庫的連接
$db=new PDO('mysql:host=localhost;dbname=shop;charset=utf8','root','');
// 檢查是否為表單提交(POST 請求)
if($_SERVER['REQUEST_METHOD']=='POST'){
    // 獲取並清理輸入
    $name=isset($_POST['name'])?trim(filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING)):'';
    $phone=isset($_POST['phone'])?trim(filter_input(INPUT_POST,'phone',FILTER_SANITIZE_STRING)):'';
    $email=isset($_POST['email'])?trim(filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL)):'';
    $message=isset($_POST['message'])?trim(filter_input(INPUT_POST,'message',FILTER_SANITIZE_STRING)):'';
    // 驗證必填欄位
    if(empty($name)){
        $error="請填寫姓名欄位";
    }elseif(empty($email)){
        $error="請填寫電子郵件欄位";
    }elseif(empty($message)){
        $error="請填寫建議內容";
    }else{
        try{
            $stmt=$db->prepare("INSERT INTO feedback(name,phone,email,message) VALUES(?,?,?,?)");
            $stmt->execute([$name,$phone,$email,$message]);
            // 清空表單值
            $name=$phone=$email=$message='';
            $success="感謝您的回饋！我們會盡快處理您的意見。";
        }catch(PDOException $e){
            $error="提交失敗，請稍後再試。錯誤:".$e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <!-- 引入 Font Awesome 圖標庫 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 引入 jQuery 庫 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- 引入 Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>關於我們 - 香氛小築</title>
    <style>
        /* CSS 變數定義 */
        :root{
            --candle-primary:#8B4513;    /* 主色：棕色 */
            --candle-secondary:#D2B48C;  /* 次要色：淺棕色 */
            --candle-accent:#F5DEB3;     /* 強調色：米色 */
        }
        .btn-brown{
            background-color:var(--candle-primary);
            color:white;
            border-color:var(--candle-primary);
        }
        .btn-outline-brown{
            border-color:var(--candle-primary);
            color:var(--candle-primary);
        }
        .btn-brown:hover,.btn-outline-brown:hover{
            background-color:var(--candle-secondary);
            color:white;
            border-color:var(--candle-secondary);
        }
        /* 主視覺區樣式 */
        .hero-section{
            background:linear-gradient(rgba(0,0,0,0.7),rgba(0,0,0,0.7));
            background-size:cover;
            background-position:center;
            color:white;
            padding:120px 0;
            text-align:center;
            margin-bottom:50px;
            position:relative;
        }
        /* 卡片懸停效果 */
        .card:hover{
            transform:translateY(-5px);
            transition:transform 0.3s ease;
            box-shadow:0 10px 20px rgba(0,0,0,0.1);
        }
        body{
            background-color:#FFF9F0;
            font-family:'Noto Sans TC',sans-serif;
        }
        .social-links a{
            width:40px;
            height:40px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:50%;
        }
        .required:after{
            content:"*";
            color:red;
        }
        /* 新增錯誤訊息樣式 */
        .is-invalid{
            border-color:#dc3545;
        }
        .invalid-feedback{
            color:#dc3545;
            font-size:0.875em;
        }
    </style>
</head>
<body>
    <!-- 引入頁首導航欄 -->
    <?php include_once __DIR__.'/includes/header.php'; ?>
    <!-- 提示訊息-->
    <?php if(isset($success)):?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <!--錯誤訊息顯示-->
    <?php if(isset($error)):?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <!-- 主視覺區 -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4">關於 香氛小築</h1>
            <p class="lead">點燃生活的美好時刻</p>
        </div>
    </section>
    <!-- 公司介紹區塊 -->
    <section class="container mb-5">
        <div class="row align-items-center">
            <div class="col-12">
                <h2 class="mb-4">我們的使命</h2>
                <p class="lead">"用香氣溫暖每個家庭，用蠟燭點亮每個心靈"</p>
                <p>自2010年成立以來，香氛小築始終致力於為顧客提供最優質的手工香氛蠟燭，使用天然原料，創造安全、環保且充滿療癒力的居家香氛體驗。</p>
                <p>我們的團隊由專業調香師和蠟燭工藝師組成，每一款產品都經過嚴格的品質把關，確保帶給您最純粹的香氛享受。</p>
            </div>
        </div>
    </section>
    <!-- 核心價值區塊 -->
    <section class="py-5" style="background-color:#f8f9fa;">
        <div class="container">
            <h2 class="text-center mb-5">我們的承諾</h2>
            <div class="row">
                <!-- 天然原料卡片 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-leaf fa-3x mb-3" style="color:var(--candle-primary);"></i>
                            <h4>天然原料</h4>
                            <p>100%純天然大豆蠟，搭配植物精油，無毒無害，燃燒時不會釋放有害物質，保護您和家人的健康。</p>
                        </div>
                    </div>
                </div>
                <!-- 手工製作卡片 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-hand-sparkles fa-3x mb-3" style="color:var(--candle-primary);"></i>
                            <h4>手工製作</h4>
                            <p>每件作品都是手工精心製作，獨一無二。我們的工匠將熱情注入每一個細節，創造出完美的香氛體驗。</p>
                        </div>
                    </div>
                </div>
                <!-- 香氛諮詢卡片 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-spa fa-3x mb-3" style="color:var(--candle-primary);"></i>
                            <h4>香氛諮詢</h4>
                            <p>專業調香師為您推薦適合的居家香氛。無論是放鬆、專注還是浪漫氛圍，我們都能為您找到完美搭配。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- 聯絡我們區塊 -->
    <section class="container py-5">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="mb-4">聯絡香氛小築</h2>
                <p><i class="fas fa-map-marker-alt mr-2"></i> 台北市大安區和平東路一段186巷7號</p>
                <p><i class="fas fa-phone mr-2"></i>(02) 123-4567</p>
                <p><i class="fas fa-envelope mr-2"></i> contact@aromacottage.com</p>
                <div class="mt-4">
                    <h5>營業時間</h5>
                    <p>週一至週五:10:00 - 20:00<br>
                    週六至週日:11:00 - 18:00</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">香氛諮詢</h4>
                        <!-- 聯絡表單 -->
                        <form method="POST" action="">
                            <!-- 姓名輸入欄 -->
                            <div class="form-group mb-3">
                                <label for="contact_name" class="font-weight-bold required">姓名</label>
                                <input type="text" class="form-control" id="contact_name" name="name" required 
                                    placeholder="請輸入您的姓名"
                                    value="<?php echo isset($_POST['name'])?htmlspecialchars($_POST['name']):''; ?>">
                            </div>
                            <!-- 電話輸入欄 -->
                            <div class="form-group mb-3">
                                <label for="contact_phone" class="font-weight-bold">電話</label>
                                <input type="tel" class="form-control" id="contact_phone" name="phone" 
                                    placeholder="請輸入您的電話號碼"
                                    value="<?php echo isset($_POST['phone'])?htmlspecialchars($_POST['phone']):''; ?>">
                            </div>
                            <!-- 電子郵件輸入欄 -->
                            <div class="form-group mb-3">
                                <label for="contact_email" class="font-weight-bold required">電子郵件</label>
                                <input type="email" class="form-control" id="contact_email" name="email" required
                                    placeholder="請輸入您的電子郵件"
                                    value="<?php echo isset($_POST['email'])?htmlspecialchars($_POST['email']):''; ?>">
                                <small class="form-text text-muted">我們絕不會與第三方分享您的電子郵件</small>
                            </div>
                            <!-- 訊息內容輸入欄 -->
                            <div class="form-group mb-4">
                                <label for="contact_message" class="font-weight-bold required">訊息內容</label>
                                <textarea class="form-control" id="contact_message" name="message" rows="4" required
                                    placeholder="請輸入您的問題或建議"><?php echo isset($_POST['message'])?htmlspecialchars($_POST['message']):''; ?></textarea>
                            </div>
                            <!-- 提交按鈕 -->
                            <div class="text-right">
                                <button type="submit" class="btn btn-brown btn-lg px-4">
                                    <i class="fas fa-paper-plane mr-2"></i>送出詢問
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- 引入頁尾 -->
    <?php include_once __DIR__.'/includes/footer.php'; ?>
</body>
</html>