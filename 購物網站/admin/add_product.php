<?php
// 開啟 session
session_start();
//引入共用函數文件
require_once __DIR__ . '/../includes/functions.php';

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 從 POST 數據中獲取商品資訊
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $inventory = intval($_POST['inventory']);
    
    // 驗證輸入數據
    if (empty($name) || empty($description) || $price <= 0 || $inventory < 0) {
        $_SESSION['error'] = "請填寫所有必填欄位，且價格必須大於0，庫存不能為負數";
        header("Location: /final_project/admin/products.php");
        exit;
    }
    
    // 檢查商品名稱是否已存在
    $check_stmt = $conn->prepare("SELECT product_id FROM product WHERE name = ?");
    $check_stmt->bind_param("s", $name);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        $_SESSION['error'] = "商品名稱已存在，請使用其他名稱";
        header("Location: /final_project/admin/products.php");
        exit;
    }
    
    // 準備 SQL 插入語句
    $stmt = $conn->prepare("INSERT INTO Product (name, description, price, inventory) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $name, $description, $price, $inventory);
    
    // 執行 SQL 語句
    if ($stmt->execute()) {
        $_SESSION['success'] = "商品已成功新增！";
        header("Location: /final_project/admin/products.php");
        exit;
    } else {
        $_SESSION['error'] = "新增商品失敗: " . $conn->error;
        header("Location: /final_project/admin/products.php");
        exit;
    }
}
// 如果不是 POST 請求，顯示表單
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增商品</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- 引入頁首導航欄 -->
    <?php include_once __DIR__.'/../includes/header.php'; ?>
    <div class="container mt-5">
        <h2>新增商品</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="name" class="form-label">商品名稱 *</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">商品描述 *</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="mb-3">
                <label for="price" class="form-label">價格 *</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" min="0.01" required>
            </div>
            
            <div class="mb-3">
                <label for="inventory" class="form-label">庫存數量 *</label>
                <input type="number" class="form-control" id="inventory" name="inventory" min="0" required>
            </div>
            
            <button type="submit" class="btn btn-primary">新增商品</button>
            <a href="/final_project/admin/products.php" class="btn btn-secondary">返回商品列表</a>
        </form>
    </div>
</body>
</html>

<?php
// 關閉資料庫連接
$conn->close();
?>