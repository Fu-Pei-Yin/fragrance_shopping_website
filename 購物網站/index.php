<?php
//啟用 session
session_start();
//引入自訂函數檔案
require_once __DIR__.'/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <!--基本 meta 設定-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>香氛小築-手工蠟燭專賣</title>
    <!--引入 Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--引入 Font Awesome 圖標庫-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!--引入 Bootstrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /*主視覺區樣式*/
        .hero-section{
            background:linear-gradient(rgba(0,0,0,0.6));
            background-size:cover;
            background-position:center;
            color:white;
            padding:100px 0;
            margin-bottom:30px;
        }
        /*商品卡片樣式*/
        .product-card{
            transition:transform 0.3s;
            margin-bottom:20px;
            height:100%;
        }
        .product-card:hover{
            transform:translateY(-5px);
            box-shadow:0 10px 20px rgba(0,0,0,0.1);
        }
        /*特色服務區塊樣式*/
        .feature-box{
            padding:30px;
            text-align:center;
            border-radius:5px;
            margin-bottom:30px;
            background-color:#f8f9fa;
        }
        /*特色服務圖標樣式*/
        .feature-box i{
            font-size:2.5rem;
            margin-bottom:15px;
            color:#8B4513;/*棕色調*/
        }
        /*圖片佔位符樣式*/
        .img-placeholder{
            background-color:#f5e8d0;/*淺米色*/
            display:flex;
            align-items:center;
            justify-content:center;
            height:200px;
            color:#8B4513;
        }
        body{
            background-color:#fff9f0;/*淺奶油色背景*/
        }
    </style>
</head>
<body>
    <!--引入頁首導覽列-->
    <?php include __DIR__.'/includes/header.php';?>
    <!--提示訊息-->
    <!--提示訊息-->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <!--主視覺區-->
    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">香氛小築</h1>
            <p class="lead">點燃生活的美好時刻</p>
            <!--根據用戶身份顯示不同按鈕-->
            <?php if(isAdmin()):?>
                <a href="/final_project/admin/products.php" class="btn btn-brown btn-lg mt-3">商品管理</a>
            <?php else:?>
                <a href="/final_project/products/list_products.php" class="btn btn-brown btn-lg mt-3">探索香氛</a>
            <?php endif;?>
        </div>
    </div>
    <div class="container">
        <!--特色服務區塊-->
        <div class="row text-center mb-5">
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-leaf"></i>
                    <h3>天然原料</h3>
                    <p>使用純天然大豆蠟與植物精油</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-hand-holding-heart"></i>
                    <h3>手工製作</h3>
                    <p>每件作品都是手工精心製作</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-spa"></i>
                    <h3>療癒香氛</h3>
                    <p>專業調香師調配的療癒香氣</p>
                </div>
            </div>
        </div>
        <!--熱門商品區-->
        <h2 class="text-center mb-4">熱門香氛</h2>
        <div class="row">
            <?php
            try{
                //從資料庫隨機獲取4個商品
                $stmt=$conn->prepare("SELECT*FROM product ORDER BY RAND() LIMIT 4");
                $stmt->execute();//執行查詢
                $result=$stmt->get_result();//執行查詢並獲取結果
                $featured_products=$result->fetch_all(MYSQLI_ASSOC);//將結果轉換為關聯陣列
                //如果沒有商品顯示提示訊息
                if(empty($featured_products)):?>]
                    <div class="alert alert-info text-center">目前沒有商品，請稍後再來！</div>
                <?php else:
                    //循環顯示每個商品
                    foreach($featured_products as $product):
                        //檢查商品是否已在購物車中
                        $in_cart=false;
                        if(isLoggedIn()){
                            $stmt=$conn->prepare("SELECT 1 FROM Cart_Item WHERE product_id=? AND cart_id=(SELECT cart_id FROM Cart WHERE user_id=?)");
                            $stmt->bind_param("ii",$product['product_id'],$_SESSION['user_id']);
                            $stmt->execute();
                            $in_cart=$stmt->get_result()->num_rows>0;
                        }
           ?>
                <div class="col-md-3 mb-4">
                    <div class="card product-card h-100">
                        <!--顯示商品圖片或佔位圖-->
                        <?php if(file_exists('images/'.$product['name'].'.jpg')):?>
                            <img src="/final_project/images/<?=$product['name']?>.jpg" class="card-img-top" alt="<?=htmlspecialchars($product['name'])?>" style="height:200px;object-fit:cover;">
                        <?php else:?>
                            <div class="img-placeholder">
                                <i class="fas fa-candle-holder fa-3x"></i>
                            </div>
                        <?php endif;?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?=htmlspecialchars($product['name'])?></h5>
                            <p class="card-text text-danger fw-bold">NT$ <?=number_format($product['price'])?></p>
                            <div class="mt-auto">
                                <!--根據登入狀態和用戶身份顯示不同按鈕-->
                                <?php if(isLoggedIn()):?>
                                    <?php if(isAdmin()):?>
                                        <a href="/final_project/admin/edit_product.php?id=<?=$product['product_id']?>" class="btn btn-brown w-100">商品管理</a>
                                    <?php else:?>
                                        <?php if($in_cart):?>
                                            <button class="btn btn-success w-100" disabled>已加入購物車</button>
                                        <?php else:?>
                                            <!--加入購物車表單-->
                                            <form action="/final_project/cart/add_to_cart.php" method="post">
                                                <input type="hidden" name="product_id" value="<?=$product['product_id']?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-brown w-100">加入購物車</button>
                                            </form>
                                        <?php endif;?>
                                    <?php endif;?>
                                <?php else:?>
                                    <a href="/final_project/user/login.php" class="btn btn-outline-brown w-100">登入購買</a>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach;
                endif;
                $stmt->close();
            } catch(Exception $e){
                //顯示錯誤訊息
                echo '<div class="col-12"><div class="alert alert-danger">發生錯誤:'.htmlspecialchars($e->getMessage()).'</div></div>';
            }
           ?>
        </div>
    </div>
    <!--引入頁尾-->
    <?php include __DIR__.'/includes/footer.php';?>
<?php $conn->close();?>