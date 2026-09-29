<?php
// 啟用 session
session_start();
// 引入功能函數檔案
require_once __DIR__.'/../includes/functions.php';
// 檢查用戶是否登入，若未登入則跳轉到登入頁面
if (!isLoggedIn()) {
    header("Location: /final_project/user/login.php");
    exit;
}
// 獲取當前用戶的購物車商品
$cart_item =getUserCart($_SESSION['user_id']);
// 計算購物車總金額
$total =calculateCartTotal($_SESSION['user_id']);
?>
<!--引入頁首導航欄-->
<?php include_once __DIR__.'/../includes/header.php';?>
<h2>我的購物車</h2>
    <!--顯示成功訊息-->
    <?php if (isset($_SESSION['success'])):?>
    <div class="alert alert-success"><?=$_SESSION['success']; unset($_SESSION['success']);?></div>
<?php endif;?>
<!--顯示錯誤訊息-->
<?php if (isset($_SESSION['error'])):?>
    <div class="alert alert-danger"><?=$_SESSION['error']; unset($_SESSION['error']);?></div>
<?php endif;?>
<?php
// 初始化一個標記用來判斷是否可以結帳
    $can_checkout =true;
    // 遍歷購物車中的每個商品
    foreach ($cart_item as $item): 
        // 檢查商品庫存是否為0（這裡邏輯可能有問題，應該是檢查庫存是否足夠）
        if ($item['inventory'] ==0):  // 原條件是!=0，這表示有庫存時顯示錯誤，應該是反過來的
            // 如果有商品庫存不足，設置標記為false
            $can_checkout =false;
            // 顯示錯誤訊息（只需要顯示一次）
           ?>
            <div class="alert alert-warning mb-3" align='center'>部分商品庫存不足，無法結帳</div>
            <?php
            // 發現一個商品庫存不足就可以跳出循環
            break;
        endif;
    endforeach; 
?>
<!--檢查購物車是否為空-->
<?php if (empty($cart_item)):?>
    <div class="alert alert-info">您的購物車是空的</div>
    <a href="/final_project/products/list_products.php" class="btn btn-primary">繼續購物</a>
<?php else:?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>商品圖片</th>
                    <th>商品名稱</th>
                    <th>單價</th>
                    <th>數量</th>
                    <th>小計</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <!--循環顯示購物車中的每個商品-->
                <?php foreach ($cart_item as $item):?>
                    <tr>
                        <td>
                            <!--顯示商品圖片-->
                            <img src="/final_project/images/<?=$item['name']?>.jpg" 
                                class="img-thumbnail" 
                                alt="<?=htmlspecialchars($item['name'])?>"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        </td>
                        <td><?=htmlspecialchars($item['name'])?></td>
                        <td>NT$ <?=number_format($item['price'])?></td>
                        <!--檢查商品庫存是否為0-->
                        <?php if ($item['inventory']==0):?>
                            <td>該商品無庫存，請重新選擇</td>
                            <td></td>
                            <td>
                                <!--移除商品按鈕-->
                                <a href="/final_project/cart/update_cart.php?action=delete&product_id=<?=$item['product_id']?>" 
                                    class="btn btn-sm btn-danger" 
                                    onclick="return confirm('確定要移除這個商品嗎？')">移除</a>
                            </td>
                        <?php else:?>
                            <td>
                                <!--商品數量更新表單-->
                                <form action="/final_project/cart/update_cart.php" method="post" class="d-flex align-items-center">
                                    <input type="hidden" name="product_id" value="<?=$item['product_id']?>">
                                    <!--數量輸入框，限制最大值為庫存量-->
                                    <input type="number" name="quantity" value="<?=$item['quantity']?>" 
                                        min="1" max="<?=$item['inventory']?>" 
                                        class="form-control form-control-sm me-2" style="width: 70px;">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">更新</button>
                                </form>
                            </td>
                            <!--計算並顯示小計-->
                            <td>NT$ <?=number_format($item['price'] * $item['quantity'])?></td>
                            <td>
                                <!--移除商品按鈕-->
                                <a href="/final_project/cart/update_cart.php?action=delete&product_id=<?=$item['product_id']?>" 
                                    class="btn btn-sm btn-danger" 
                                    onclick="return confirm('確定要移除這個商品嗎？')">移除</a>
                            </td>
                        <?php endif;?>
                    </tr>
                <?php endforeach;?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <!--顯示購物車總金額-->
                    <th colspan="4" class="text-end">總計</th>
                    <th colspan="2">NT$ <?=number_format($total)?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <!--繼續購物按鈕-->
        <a href="/final_project/products/list_products.php" class="btn btn-secondary">繼續購物</a>
        <!--檢查是否有商品庫存為0-->
        <?php 
        // 根據標記決定顯示哪個按鈕
        if ($can_checkout):?>
            <!--顯示結帳按鈕（只會顯示一次）-->
            <a href="/final_project/cart/checkout.php" class="btn btn-primary">結帳</a>
        <?php else:?>
            <!--顯示無法結帳按鈕（只會顯示一次）-->
            <a class="btn btn-primary disabled">無法結帳，請調整商品</a>
        <?php endif;?>
    </div>
<?php endif;?>
<!--引入頁尾檔案-->
<?php require_once __DIR__.'/../includes/footer.php';?>