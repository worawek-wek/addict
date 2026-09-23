-- =====================================================================
-- ADDICT — จ่ายเงินแยกหลายวิธี (split payment)
--   ตาราง order_payments: 1 ออเดอร์มีได้หลายแถว (วิธี + จำนวนเงินต่อวิธี)
--   รันซ้ำได้ (idempotent)  |  DB: zwek_addict  |  MySQL 8.x
--   หมายเหตุ orders.id เป็น int -> ref_order_id ใช้ int ตาม
-- =====================================================================

SET NAMES utf8mb4;

-- 1) ตารางเก็บการชำระเงินแยกต่อวิธี
CREATE TABLE IF NOT EXISTS `order_payments` (
  `id`           bigint unsigned NOT NULL AUTO_INCREMENT,
  `ref_order_id` int NOT NULL,
  `method`       varchar(30) NOT NULL COMMENT 'cash, qr_code, credit_card, alipay, wechat, ewallet',
  `amount`       decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at`   timestamp NULL DEFAULT NULL,
  `updated_at`   timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_payments_order_idx` (`ref_order_id`),
  KEY `order_payments_method_idx` (`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Backfill ออเดอร์เก่าที่มี payment_method อยู่แล้ว -> สร้าง 1 แถวต่อบิล
--    (normalize promptpay -> qr_code ให้ตรงกับรายงาน) ข้ามบิลที่มีแถวแล้ว
INSERT INTO `order_payments` (`ref_order_id`, `method`, `amount`, `created_at`, `updated_at`)
SELECT o.id,
       CASE WHEN o.payment_method = 'promptpay' THEN 'qr_code' ELSE o.payment_method END,
       COALESCE(o.total_price, 0),
       NOW(), NOW()
FROM orders o
WHERE o.payment_method IS NOT NULL
  AND o.payment_method <> ''
  AND NOT EXISTS (SELECT 1 FROM order_payments p WHERE p.ref_order_id = o.id);
