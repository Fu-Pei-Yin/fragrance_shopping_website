<?php
session_start();//啟動 session，用於登入驗證與訊息傳遞
require_once __DIR__.'/../includes/functions.php';//載入自定義函式庫
//檢查是否有傳入有效的商品 ID，若無則導回商品總覽頁
if (!isset($_GET['id'])||!is_numeric($_GET['id'])) {
    header("Location: /products/list_products.php");
    exit;
}
$product_id =intval($_GET['id']);//將 GET 傳來的商品 id 轉為整數
$product =getProductById($product_id);//根據商品 ID 取得商品資料
//若找不到商品資料，導回商品總覽頁
if (!$product) {
    $_SESSION['error']='查無該商品';
    header("Location: /final_project/products/list_products.php");
    exit;
}
?>
<!--引入頁首導航欄-->
<?php include_once __DIR__.'/../includes/header.php';?>
<!--顯示成功訊息-->
<?php if (isset($_SESSION['success'])):?>
    <div class="alert alert-success"><?=htmlspecialchars($_SESSION['success'])?></div>
    <?php unset($_SESSION['success']);?>
<?php endif;?>
<!--顯示錯誤訊息-->
<?php if (isset($_SESSION['error'])):?>
    <div class="alert alert-danger"><?=htmlspecialchars($_SESSION['error'])?></div>
    <?php unset($_SESSION['error']);?>
<?php endif;?>
<div class="container py-5">
    <div class="row">
        <!--左側：商品圖片與購買按鈕-->
        <div class="col-md-6">
            <div class="card mb-4">
                <img src="/final_project/images/<?=$product['name']?>.jpg" 
                     class="card-img-top" 
                     alt="<?=htmlspecialchars($product['name'])?>"
                     style="max-height: 500px; object-fit: contain;">
            </div>
            <!--行動按鈕區塊-->
            <div class="d-grid gap-2">
                <?php if (isAdmin()):?>
                    <!--管理者可見：前往商品編輯頁-->
                    <a href="/final_project/admin/edit_product.php?id=<?=$product['product_id']?>" class="btn btn-outline-primary flex-grow-2">商品管理</a>
                <?php else:?>
                    <!--一般使用者登入後可購買商品-->
                    <?php if (isLoggedIn()):?>
                        <?php if ($product['inventory'] !=0):?>
                            <!--有庫存才顯示購買表單-->
                            <form action="/final_project/cart/add_to_cart.php" method="post" class="flex-grow-1">
                                <input type="hidden" name="product_id" value="<?=$product['product_id']?>">
                                <div class="input-group">
                                    <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?=$product['inventory']?>" style="width: 70px;">
                                    <button class="btn btn-outline-primary" type="submit">加入購物車</button>
                                </div>
                            </form>
                        <?php else:?>
                            <!--無庫存提示-->
                            <a class="btn btn-outline-primary flex-grow-2">商品無庫存</a>
                        <?php endif;?>
                    <?php else:?>
                        <!--未登入者提示登入-->
                        <a href="/final_project/user/login.php" class="btn btn-primary">登入以購買</a>
                    <?php endif;?>
                <?php endif;?>
                <!--返回商品列表-->
                <a href="/final_project/products/list_products.php" class="btn btn-outline-secondary">返回商品列表</a>
            </div>
        </div>

        <!--右側：商品資訊-->
        <div class="col-md-6">
            <!--商品名稱-->
            <h1 class="mb-3"><?=htmlspecialchars($product['name'])?></h1>            

            <!--價格與庫存狀態-->
            <div class="d-flex align-items-center mb-4">
                <span class="h3 text-primary me-3">NT$ <?=number_format($product['price'])?></span>
                <span class="badge bg-<?=($product['inventory'] > 0)? 'success' : 'danger'?>">
                    <?=($product['inventory'] > 0)? '有現貨' : '缺貨中'?>
                </span>
            </div>

            <!--商品簡介-->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">商品簡介</h5>
                    <p class="card-text"><?=nl2br(htmlspecialchars($product['description']))?></p>
                </div>
            </div>

            <!--商品詳細規格-->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">詳細規格</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>庫存</span>
                            <span><?=htmlspecialchars($product['inventory']?? '未提供')?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__.'/../includes/footer.php';//載入頁尾?>
