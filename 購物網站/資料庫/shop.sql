-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-06-02 15:10:46
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `shop`
--

-- --------------------------------------------------------

--
-- 資料表結構 `cart`
--

CREATE TABLE `cart` (
  `user_id` int(100) NOT NULL,
  `cart_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `cart`
--

INSERT INTO `cart` (`user_id`, `cart_id`) VALUES
(33, 0),
(34, 34),
(35, 35),
(36, 36),
(37, 37),
(38, 38);

-- --------------------------------------------------------

--
-- 資料表結構 `cart_item`
--

CREATE TABLE `cart_item` (
  `product_id` int(255) NOT NULL,
  `cart_id` int(255) NOT NULL,
  `quantity` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `cart_item`
--

INSERT INTO `cart_item` (`product_id`, `cart_id`, `quantity`) VALUES
(14, 38, 1),
(17, 38, 1),
(18, 38, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `feedback`
--

CREATE TABLE `feedback` (
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` int(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `order`
--

CREATE TABLE `order` (
  `order_id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `total_price` int(100) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `order`
--

INSERT INTO `order` (`order_id`, `user_id`, `total_price`, `status`, `created_at`, `updated_at`) VALUES
(1, 38, 3220, 'pending', '2025-06-02 21:06:58', '2025-06-02 21:06:58'),
(2, 36, 2240, 'paid', '2025-06-02 21:07:22', '2025-06-02 21:10:00'),
(3, 36, 1960, 'pending', '2025-06-02 21:07:29', '2025-06-02 21:09:52');

-- --------------------------------------------------------

--
-- 資料表結構 `order_item`
--

CREATE TABLE `order_item` (
  `product_id` int(100) NOT NULL,
  `order_id` int(100) NOT NULL,
  `quantity` int(100) NOT NULL,
  `price` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `order_item`
--

INSERT INTO `order_item` (`product_id`, `order_id`, `quantity`, `price`) VALUES
(14, 1, 1, 980),
(16, 1, 1, 980),
(17, 1, 1, 980),
(25, 1, 1, 280),
(25, 2, 1, 280),
(14, 2, 1, 980),
(20, 2, 1, 980),
(15, 3, 1, 980),
(16, 3, 1, 980);

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `product_id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(100) NOT NULL,
  `inventory` int(100) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `product`
--

INSERT INTO `product` (`product_id`, `name`, `price`, `inventory`, `description`) VALUES
(14, '花漾龍舌蘭香氛蠟燭', 980, 798, '靈感來自墨西哥龍舌蘭日出，融合柑橘與龍舌蘭酒香，營造熱帶度假氛圍。前調西柚與青檸的清新，中調龍舌蘭酒香微醺，尾調琥珀與香草帶來溫暖質感。'),
(15, '晨沐禪茶香氛蠟燭', 980, 995, '清晨禪寺的靜謐茶香，以高山烏龍為主調，佐以佛手柑與雪松。點燃時彷彿置身薄霧茶園，適合冥想與晨間儀式。無煙配方讓空間更純淨。'),
(16, '夜沐黑茶香氛蠟燭', 980, 995, '深夜書房的沉穩香調，普洱老茶與廣藿香交織，後調檀木與皮革增添深邃層次。特別添加植物精油，燃燒時同步釋放舒壓氣息。'),
(17, '生椰咖啡香氛蠟燭', 980, 998, '南洋咖啡館的醇香記憶，新鮮椰奶與深度烘焙咖啡豆碰撞，搭配焦糖與可可尾韻。採用大豆蠟與椰子蠟混合基材，燃燒時間達60小時。'),
(18, '懷舊可樂糖香氛蠟燭', 980, 999, '復古雜貨店的甜蜜回憶，完美重現經典可樂糖氣泡感，搭配香草與肉桂辛香。燭杯設計靈感來自1950年代玻璃汽水瓶。'),
(19, '薰衣草香氛蠟燭', 980, 1000, '普羅旺斯薰衣草田的寧靜香氣，選用法國高地真實薰衣草精油，搭配少量鼠尾草提升層次。助眠配方特別適合臥室使用。'),
(20, '玫瑰香氛蠟燭', 980, 998, '大馬士革玫瑰清晨採摘的鮮嫩香氣，保加利亞玫瑰精油與荔枝果香調和，底蘊帶有蜂蜜與雪松木質調。手工灌注的漸層燭體。'),
(21, '茉莉花香氛蠟燭', 980, 1000, '江南夏夜的茉莉園香韻，採用傳統脂吸法萃取的小花茉莉，搭配依蘭與白茶香調。陶瓷燭杯繪有傳統水墨茉莉圖樣。'),
(22, '肉桂香氛蠟燭', 980, 999, '冬日暖爐旁的辛甜香氣，錫蘭肉桂與香橙完美比例，後調融入香草與零陵香豆。特別設計的木芯燃燒時會發出細微篝火聲。'),
(23, '馬鞭草香氛蠟燭', 980, 999, '地中海豔陽下的清新草本香，法國馬鞭草與檸檬馬鞭草雙重萃取，加入薄荷與羅勒增添綠意。適合辦公空間提神醒腦。'),
(24, '雪松香氛蠟燭', 980, 998, '阿爾卑斯山森林的木質調，加拿大紅雪松精油為主軸，前調杜松子與後調香根草構成三層香韻。黑色磨砂燭杯展現低調奢華。'),
(25, '旅行小茶罐', 280, 997, '鋁製隨身茶葉罐，內附可拆式茶濾隔層。僅手掌大小的精巧設計，卻能容納10g茶葉，密封防潮設計保持茶香新鮮。共六款馬卡龍配色。'),
(26, '荔枝氣泡酒香氛蠟燭', 980, 1000, '夏日慶典的微醺時刻，台灣荔枝與法國香檳的奢華組合，氣泡感香調技術讓香氣更生動。添加天然蜂蠟延長留香時間。');

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `user_id` int(255) NOT NULL,
  `password` varchar(21) NOT NULL,
  `email` varchar(255) NOT NULL,
  `regtime` datetime NOT NULL DEFAULT current_timestamp(),
  `username` varchar(20) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(16) NOT NULL,
  `locations` varchar(101) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`user_id`, `password`, `email`, `regtime`, `username`, `is_admin`, `phone`, `locations`) VALUES
(36, 'user2-secret', 'user2@example.com', '2025-06-02 00:49:31', 'user2', 0, '0912-345-678', 'zxcvbnm'),
(37, 'manager-secret', 'manager@example.com', '2025-06-02 01:21:47', 'manager', 1, '0998-765-432', 'zxcvbnm'),
(38, 'user1-secret', 'user1@example.com', '2025-06-02 21:06:33', 'user1', 0, '0998-765-432', 'asdfghjkl');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`);

--
-- 資料表索引 `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`user_id`);

--
-- 資料表索引 `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`);

--
-- 資料表索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `cart`
--
ALTER TABLE `cart`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `feedback`
--
ALTER TABLE `feedback`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
