<?php
// 啟用 session
session_start();
// 引入功能函數檔
require_once __DIR__ .'/../includes/functions.php';
// 檢查是否為管理員，若不是則跳轉回首頁
if(!isAdmin()){
    header("Location: /final_project/index.php");
    exit;
}

// 從 GET 參數取得商品 ID，預設為 0
$product_id = $_GET['id'] ?? 0;
$product_id = intval($product_id);

// 初始化商品陣列
$product = [];
$error = '';

// 處理 GET 請求（顯示商品編輯表單）
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    // 準備 SQL 查詢，獲取指定商品資料
    $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    // 如果商品不存在，跳轉回商品管理頁面
    if(!$product){
        $_SESSION['error'] = "找不到指定的商品";
        header("Location: /final_project/admin/products.php");
        exit;
    }
}

// 處理 POST 請求（更新商品資料）
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // 從表單獲取資料並清理
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $inventory = intval($_POST['inventory']);
    $product_id = intval($_POST['product_id']);
    
    // 驗證輸入數據
    if(empty($name) || empty($description) || $price <= 0 || $inventory < 0){
        $error = "請填寫所有必填欄位，且價格必須大於0，庫存不能為負數";
    } else {
        // 檢查商品名稱是否已被其他商品使用
        $check_stmt = $conn->prepare("SELECT product_id FROM product WHERE name = ? AND product_id != ?");
        $check_stmt->bind_param("si", $name, $product_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows > 0){
            $error = "商品名稱已被其他商品使用，請使用其他名稱";
        } else {
            // 準備 SQL 更新語句
            $stmt = $conn->prepare("UPDATE product SET name = ?, description = ?, price = ?, inventory = ? WHERE product_id = ?");
            $stmt->bind_param("ssdii", $name, $description, $price, $inventory, $product_id);
            
            // 執行更新操作
            if($stmt->execute()){
                $_SESSION['success'] = "商品已成功更新";
                header("Location: /final_project/admin/products.php");
                exit;
            } else {
                $error = "更新商品失敗: " . $conn->error;
            }
        }
    }
    
    // 如果發生錯誤，重新獲取商品資料以顯示在表單中
    $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}
?>

<!-- 引入頁首導航欄 -->
<?php include_once __DIR__.'/../includes/header.php'; ?>

<div class="container mt-4">
    <!-- 顯示成功訊息 -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <!-- 顯示錯誤訊息 -->
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <h2 class="mb-4">編輯商品</h2>
    
    <form method="post">
        <input type="hidden" name="product_id" value="<?= $product_id ?>">
        
        <div class="mb-3">
            <label for="name" class="form-label">商品名稱 *</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">商品描述 *</label>
            <textarea class="form-control" id="description" name="description" 
                      rows="3" required><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>
        
        <div class="mb-3">
            <label for="price" class="form-label">價格 *</label>
            <input type="number" class="form-control" id="price" name="price" 
                    step="0.01" min="0.01" value="<?= $product['price'] ?? '' ?>" required>
        </div>  
        <div class="mb-3">
            <label for="inventory" class="form-label">庫存數量 *</label>
            <input type="number" class="form-control" id="inventory" name="inventory" 
                    min="0" value="<?= $product['inventory'] ?? '' ?>" required>
        </div>
        
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> 更新商品
            </button>
            <a href="/final_project/admin/products.php" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> 取消
            </a>
        </div>
    </form>
</div>

<!-- 引入頁尾文件 -->
<?php require_once __DIR__ .'/../includes/footer.php'; ?>