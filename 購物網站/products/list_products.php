<?php
session_start();//啟動 session，支援使用者登入狀態與訊息提示等功能
require_once __DIR__.'/../includes/functions.php';//載入通用函式
$success='';
$error='';
$products=getAllProducts();//取得所有商品資料
?>
<!--引入頁首導航欄-->
<?php include_once __DIR__.'/../includes/header.php';?>
<h2>商品總覽</h2>
<!--提示訊息-->
<?php if (isset($_SESSION['success'])):?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']);?></div>
<?php endif;?>
<?php if (isset($_SESSION['error'])):?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']);?></div>
<?php endif;?>
<div class="row">
    <!--若無商品，顯示提示訊息-->
    <?php if(empty($products)):?>
        <div class="alert alert-info text-center">目前沒有商品，請稍後再來</div>
    <?php else:?>
        <!--遍歷所有商品-->
        <?php foreach($products as $product):?>
            <div class="col-md-6 mb-4"><!--每個商品佔用一半寬度，底部有間距-->
                <!--商品卡片-->
                <div class="card h-100">
                    <div class="row g-0">
                        <!--商品圖片-->
                        <div class="col-md-4">
                            <img src="/final_project/images/<?=$product['name']?>.jpg" 
                                class="img-fluid rounded-start" 
                                alt="商品名稱:<?=htmlspecialchars($product['name'])?>"
                                style="object-fit:cover;">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <!--商品名稱-->
                                <h4 class="card-title">商品名稱:<?=htmlspecialchars($product['name'])?></h4>
                                <!--商品介紹-->
                                <p class="card-text">介紹:<?=htmlspecialchars($product['description'])?></p>
                                <!--價格與庫存-->
                                <p class="text-primary fw-bold">價格:NT$ <?=number_format($product['price'])?></p>
                                <p class="text-primary fw-bold">庫存:<?=number_format($product['inventory'])?></p>

                                <div class="d-flex gap-2">
                                    <!--管理員顯示商品管理按鈕-->
                                    <?php if(isAdmin()):?>
                                        <a href="/final_project/admin/edit_product.php?id=<?=$product['product_id']?>" class="btn btn-outline-primary flex-grow-2">商品管理</a>
                                    <!--一般使用者-->
                                    <?php else:?>
                                        <!--商品有庫存時-->
                                        <?php if(($product['inventory']) !=0):?>    
                                            <!--已登入才能加入購物車-->
                                            <?php if(isLoggedIn()):?>
                                                <form action="/final_project/cart/add_to_cart.php" method="post" class="flex-grow-1">
                                                    <input type="hidden" name="product_id" value="<?=$product['product_id']?>">
                                                    <div class="input-group">
                                                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?=$product['inventory']?>" style="width:70px;">
                                                        <button class="btn btn-outline-primary" type="submit">加入購物車</button>
                                                    </div>
                                                </form>
                                            <!--未登入顯示登入按鈕-->
                                            <?php else:?>
                                                <a href="/final_project/user/login.php" class="btn btn-outline-primary flex-grow-2">登入以購買</a>
                                            <?php endif;?>
                                        <!--商品無庫存時-->
                                        <?php else:?>
                                            <a class="btn btn-outline-primary active flex-grow-2">商品無庫存</a>
                                        <?php endif;?>
                                    <?php endif;?>
                                    <!--商品詳情按鈕（所有人都能看到）-->
                                    <a href="/final_project/products/detail.php?id=<?=$product['product_id']?>" class="btn btn-outline-primary">查看詳情</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    <?php endif;?>
</div>
<?php require_once __DIR__.'/../includes/footer.php';//載入頁尾 HTML?>
