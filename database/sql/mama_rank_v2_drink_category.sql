-- =====================================================================
-- Mama Rank Commission v2 : ยกค่าดื่มเป็นระบบ Rank (เพิ่มมิติ category)
-- รันต่อจาก mama_rank_commission.sql และ history_commissions_rank_columns.sql
-- DB: zwek_addict
-- หมายเหตุ: MySQL ไม่รองรับ ADD COLUMN IF NOT EXISTS -> ถ้าคอลัมน์มีแล้วให้ข้าม statement นั้น
-- =====================================================================

-- 1) commission_ranks : แยกหมวด service (นวด+สินค้า) / drink (ดื่ม)
ALTER TABLE `commission_ranks`
  ADD COLUMN `category` ENUM('service','drink') NOT NULL DEFAULT 'service'
  COMMENT 'service = นวด+สินค้า, drink = ดื่ม' AFTER `ref_branch_id`;

-- เปลี่ยน unique ให้รวม category (ของเดิมทั้งหมดกลายเป็น service อัตโนมัติ)
ALTER TABLE `commission_ranks` DROP INDEX `commission_ranks_branch_mode_rank_unique`;
ALTER TABLE `commission_ranks`
  ADD UNIQUE `commission_ranks_branch_cat_mode_rank_unique` (`ref_branch_id`,`category`,`mode`,`rank_no`);

-- 2) users : โหมดคอมมิชชั่นดื่ม (แยกจากโหมดนวด+สินค้า)
ALTER TABLE `users`
  ADD COLUMN `drink_commission_mode` ENUM('sales','rounds') NULL DEFAULT 'sales'
  COMMENT 'โหมดคิดคอมมิชชั่นดื่ม' AFTER `commission_mode`;

-- 3) commission_monthly_progress : แยกหมวด
ALTER TABLE `commission_monthly_progress`
  ADD COLUMN `category` ENUM('service','drink') NOT NULL DEFAULT 'service'
  COMMENT 'service = นวด+สินค้า, drink = ดื่ม' AFTER `period_ym`;

ALTER TABLE `commission_monthly_progress` DROP INDEX `commission_progress_staff_period_unique`;
ALTER TABLE `commission_monthly_progress`
  ADD UNIQUE `commission_progress_staff_period_cat_unique` (`ref_staff_id`,`period_ym`,`category`);

-- =====================================================================
-- (ตัวอย่าง seed) บันได Rank ดื่ม 5 ขั้น แบบค่ากลางทุกสาขา
-- =====================================================================
-- INSERT INTO `commission_ranks`
--   (`ref_branch_id`,`category`,`mode`,`rank_no`,`min_threshold`,`rate`,`payout_type`,`created_at`,`updated_at`) VALUES
--   (NULL,'drink','sales',1,0,1.00,'percent',NOW(),NOW()),
--   (NULL,'drink','sales',2,20000,1.50,'percent',NOW(),NOW()),
--   (NULL,'drink','sales',3,50000,2.00,'percent',NOW(),NOW()),
--   (NULL,'drink','sales',4,80000,2.50,'percent',NOW(),NOW()),
--   (NULL,'drink','sales',5,120000,3.00,'percent',NOW(),NOW());
