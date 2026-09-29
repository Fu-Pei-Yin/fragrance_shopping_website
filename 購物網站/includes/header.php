<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8"><!--設定網頁字元編碼為 UTF-8-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><!--響應式設計設定-->
    <title>購物車系統</title><!--頁面標題-->
    <!--Bootstrap 5 樣式表-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--自訂 CSS-->
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!--導覽列-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!--網站標題或首頁連結-->
            <a class="navbar-brand" href="/final_project/index.php">首頁</a>
            <!--手機版的漢堡選單按鈕-->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!--導覽選單內容-->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!--左側導覽項目-->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <?php if (isAdmin()):?>
                            <!--若為管理員，顯示管理介面入口-->
                            <a class="nav-link" href="/final_project/products/list_products.php">商品介面預覽</a>
                        <?php else:?>
                            <!--一般用戶看到的是購物入口-->
                            <a class="nav-link" href="/final_project/products/list_products.php">立即購物</a>
                        <?php endif;?>
                    </li>
                    <li class="nav-item">
                        <!--關於我們連結-->
                        <a class="nav-link" href="/final_project/about_us.php">關於我們</a>
                    </li>
                    <li class="nav-item">
                        <!--聯絡我們連結-->
                        <a class="nav-link" href="/final_project/contact.php">聯絡我們</a>
                    </li>
                    <li class="nav-item">
                        <!--常見問題連結-->
                        <a class="nav-link" href="/final_project/faq.php">常見問題</a>
                    </li>
                </ul>
                <!--右側導覽項目（登入/註冊/會員中心）-->
                <ul class="navbar-nav">
                    <?php if (!isLoggedIn()):?>
                        <!--尚未登入：顯示註冊與登入-->
                        <li class="nav-item">
                            <a class="nav-link" href="/final_project/user/register.php">註冊</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/final_project/user/login.php">登入</a>
                        </li>
                    <?php else:?>
                        <!--已登入：顯示使用者名稱、功能連結-->
                        <li class="nav-item">
                            <a class="nav-link" href="/final_project/user/profile.php"><?= htmlspecialchars($_SESSION['username'])?></a>
                        </li>
                        <?php if (isAdmin()):?>
                            <!--管理者功能-->
                            <li class="nav-item">
                                <a class="nav-link" href="/final_project/admin/products.php">商品管理</a>
                            </li>
                        <?php else:?>
                            <!--一般用戶功能：購物車與訂單-->
                            <li class="nav-item">
                                <a class="nav-link" href="/final_project/cart/view_cart.php">購物車</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/final_project/user/orders.php">我的訂單</a>
                            </li>
                        <?php endif;?>
                        <!--登出-->
                        <li class="nav-item">
                            <a class="nav-link" href="/final_project/user/logout.php">登出</a>
                        </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </nav>
    <!--主內容容器（所有頁面共用）-->
    <div class="container mt-4">
