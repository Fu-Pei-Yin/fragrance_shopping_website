<?php
// 啟用 session 功能
session_start();
// 引入共用函數檔案
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <!-- 基本 meta 設定 -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>常見問題</title>
    <!-- 引入 Font Awesome 圖標庫 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        // 頁面載入完成後執行
        document.addEventListener('DOMContentLoaded', function() {
            // 問題折疊功能
            const questions = document.querySelectorAll('.faq-question');
            questions.forEach(question => {
                question.addEventListener('click', () => {
                    // 切換問題的 active 類別
                    question.classList.toggle('active');
                    // 切換答案的顯示狀態
                    const answer = question.nextElementSibling;
                    answer.classList.toggle('show');
                });
            });
            // 搜尋功能相關元素
            const searchInput = document.getElementById('faq-search-input');
            const searchButton = document.getElementById('faq-search-button');
            const noResultsMessage = document.getElementById('no-results-message');
            // 執行搜尋函數
            function performSearch() {
                const searchTerm = searchInput.value.trim().toLowerCase();
                // 如果搜尋框為空，重置顯示所有問題
                if (searchTerm === '') {
                    document.querySelectorAll('.faq-item').forEach(item => {
                        item.style.display = '';
                    });
                    noResultsMessage.style.display = 'none';
                    return;
                }
                let foundAny = false; // 標記是否找到結果
                // 遍歷所有問題項目
                document.querySelectorAll('.faq-item').forEach(item => {
                    const questionText = item.querySelector('.faq-question').textContent.toLowerCase();
                    const answerText = item.querySelector('.faq-answer').textContent.toLowerCase();
                    // 檢查問題或答案是否包含搜尋關鍵字
                    if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                        item.style.display = ''; // 顯示匹配項目
                        foundAny = true;
                        // 自動展開匹配的問題
                        item.querySelector('.faq-question').classList.add('active');
                        item.querySelector('.faq-answer').classList.add('show');
                    } else {
                        item.style.display = 'none'; // 隱藏不匹配項目
                    }
                });
                // 根據搜尋結果顯示或隱藏「無結果」提示
                noResultsMessage.style.display = foundAny ? 'none' : 'block';
            }
            // 綁定搜尋按鈕點擊事件
            searchButton.addEventListener('click', performSearch);
            // 綁定輸入框按 Enter 鍵事件
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        });
    </script>
    <style>
        /* 常見問題容器樣式 */
        .faq-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        /* 標題區樣式 */
        .faq-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .faq-header h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .faq-header p {
            color: #666;
            font-size: 1.1rem;
        }
        /* 問題分類區樣式 */
        .faq-category {
            margin-bottom: 40px;
        }
        .faq-category h2 {
            color: #e74c3c;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        /* 單個問題項目樣式 */
        .faq-item {
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
        }
        /* 問題標題樣式 */
        .faq-question {
            background-color: #f9f9f9;
            padding: 15px 20px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }
        .faq-question:hover {
            background-color: #f0f0f0;
        }
        /* 展開/收合圖標 */
        .faq-question:after {
            content: '+';
            font-size: 1.5rem;
            color: #e74c3c;
        }
        .faq-question.active:after {
            content: '-';
        }
        /* 答案區域樣式 */
        .faq-answer {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease;
            background-color: #fff;
        }
        .faq-answer.show {
            padding: 20px;
            max-height: 1000px;
        }
        /* 搜尋框樣式 */
        .faq-search {
            margin-bottom: 30px;
            position: relative;
        }
        .faq-search input {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .faq-search button {
            position: absolute;
            right: 5px;
            top: 5px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            cursor: pointer;
        }
        /* 客服聯繫區樣式 */
        .contact-support {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 5px;
            text-align: center;
            margin-top: 40px;
        }
        .contact-support h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .contact-support p {
            margin-bottom: 20px;
            color: #666;
        }
        .contact-btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .contact-btn:hover {
            background-color: #c0392b;
        }
        /* 無搜尋結果提示 */
        .no-results {
            text-align: center;
            padding: 20px;
            color: #e74c3c;
            font-weight: bold;
            display: none;
        }
    </style>
</head>
<body>
    <!-- 引入頁首 -->
    <?php include_once __DIR__ . '/includes/header.php'; ?>
    
    <!-- 常見問題主容器 -->
    <div class="faq-container">
        <!-- 標題區 -->
        <div class="faq-header">
            <h1>常見問題</h1>
            <p>在這裡您可以找到關於購物流程、付款方式、配送與退貨等問題的解答</p>
        </div>
        
        <!-- 搜尋框 -->
        <div class="faq-search">
            <input type="text" id="faq-search-input" placeholder="搜尋問題...">
            <button type="button" id="faq-search-button"><i class="fas fa-search"></i></button>
        </div>
        
        <!-- 無搜尋結果提示 -->
        <div class="no-results" id="no-results-message">
            沒有找到相關問題，請嘗試其他關鍵詞。
        </div>
        
        <!-- 訂購與付款分類 -->
        <div class="faq-category">
            <h2>訂購與付款</h2>
            <div class="faq-item">
                <div class="faq-question">如何下訂單？</div>
                <div class="faq-answer">
                    <p>下訂單非常簡單：</p>
                    <ol>
                        <li>瀏覽我們的商品並選擇您想要的商品</li>
                        <li>點擊「加入購物車」按鈕</li>
                        <li>完成購物後，點擊右上角的購物車圖標</li>
                        <li>確認商品無誤後，點擊「結帳」按鈕</li>
                        <li>填寫您的送貨信息和付款方式</li>
                        <li>最後確認訂單並完成付款</li>
                    </ol>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">有哪些付款方式可供選擇？</div>
                <div class="faq-answer">
                    <p>我們接受以下付款方式：</p>
                    <ul>
                        <li>信用卡/金融卡 (Visa, MasterCard, JCB)</li>
                        <li>第三方支付 (Line Pay, Apple Pay, Google Pay)</li>
                        <li>銀行轉帳</li>
                        <li>超商代碼繳費</li>
                        <li>貨到付款 (部分商品不適用)</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- 配送與運費分類 -->
        <div class="faq-category">
            <h2>配送與運費</h2>
            <div class="faq-item">
                <div class="faq-question">如何追蹤我的訂單配送狀態？</div>
                <div class="faq-answer">
                    <p>您可以通過以下方式追蹤您的訂單：</p>
                    <ol>
                        <li>登入您的帳戶，在「我的訂單」頁面查看最新狀態</li>
                        <li>點擊訂單詳情中的「追蹤物流」按鈕</li>
                        <li>我們會在商品出貨時發送包含追蹤號碼的通知郵件給您</li>
                    </ol>
                    <p>如果您有任何疑問，也可以隨時聯繫我們的客服團隊。</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">可以指定送貨時間嗎？</div>
                <div class="faq-answer">
                    <p>是的，我們提供指定送貨時間的服務：</p>
                    <ul>
                        <li>平日：上午9:00-12:00 / 下午13:00-17:00 / 晚間17:00-21:00</li>
                        <li>週六：上午9:00-12:00（需額外支付$100指定費）</li>
                        <li>週日及國定假日不提供配送服務</li>
                    </ul>
                    <p>請在結帳時選擇您希望的送貨時段。</p>
                </div>
            </div>
        </div>
        
        <!-- 退換貨與退款分類 -->
        <div class="faq-category">
            <h2>退換貨與退款</h2>
            <div class="faq-item">
                <div class="faq-question">退換貨政策是什麼？</div>
                <div class="faq-answer">
                    <p>根據消費者保護法，您享有收到商品後7天猶豫期的權益。</p>
                    <p>以下情況恕不接受退換貨：</p>
                    <ul>
                        <li>商品已拆封使用或因人為因素而產生的損壞</li>
                        <li>超過7天猶豫期</li>
                        <li>個人衛生用品（如內衣、襪子等）</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- 客服聯繫區 -->
        <div class="contact-support">
            <h3>沒有找到您需要的答案？</h3>
            <p>我們的客服團隊隨時準備為您提供幫助</p>
            <a href="contact.php" class="contact-btn">聯繫客服</a>
        </div>
    </div>
    <!-- 引入頁尾 -->
    <?php include_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>