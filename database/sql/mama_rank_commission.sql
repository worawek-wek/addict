-- =====================================================================
-- ระบบคอมมิชชั่นทีมมาม่าแบบ Rank (Mama Rank Commission)
-- Raw SQL (เทียบเท่า migration 2026_07_13_000001_create_mama_rank_commission_tables.php)
-- DB: zwek_addict
-- =====================================================================

-- 1) บันได Rank (config ที่แก้ได้จากหลังบ้าน) -----------------------------
CREATE TABLE IF NOT EXISTS `commission_ranks` (
  `id`            bigint unsigned NOT NULL AUTO_INCREMENT,
  `ref_branch_id` bigint unsigned DEFAULT NULL COMMENT 'null = default ทุกสาขา (fallback)',
  `mode`          enum('sales','rounds') NOT NULL COMMENT 'บันไดยอดขาย หรือ บันไดจำนวนรอบ',
  `rank_no`       tinyint unsigned NOT NULL COMMENT '1-5',
  `min_threshold` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'ยอดสะสมขั้นต่ำ(บาท) หรือ จำนวนรอบขั้นต่ำ',
  `rate`          decimal(5,2)  NOT NULL DEFAULT '0.00' COMMENT '% คอมมิชชั่นของ rank นี้',
  `fixed_amount`  decimal(12,2) DEFAULT NULL COMMENT 'เงินคงที่ (payout_type fixed / fixed_per_round)',
  `payout_type`   enum('percent','fixed_per_round','fixed') NOT NULL DEFAULT 'percent',
  `created_at`    timestamp NULL DEFAULT NULL,
  `updated_at`    timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commission_ranks_branch_mode_rank_unique` (`ref_branch_id`,`mode`,`rank_no`),
  KEY `commission_ranks_mode_min_threshold_index` (`mode`,`min_threshold`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) โหมดคอมมิชชั่นของพนักงานแต่ละคน (1 คน = 1 โหมด) --------------------
ALTER TABLE `users`
  ADD COLUMN `commission_mode` enum('sales','rounds') NULL DEFAULT 'sales'
  COMMENT 'โหมดคิดคอมมิชชั่นของมาม่าคนนี้'
  AFTER `ref_position_id`;

-- 3) สถานะสะสมรายเดือน + snapshot ที่ย้อนตรวจได้ ------------------------
--    เก็บค่าที่ "ใช้จริง" แบบ denormalized -> เดือนหน้าแก้เกณฑ์ rank
--    ก็ยังย้อนดูได้ว่าเดือนนั้นคนนั้นตัด rank อะไร ที่เกณฑ์เท่าไหร่ เรตกี่ %
CREATE TABLE IF NOT EXISTS `commission_monthly_progress` (
  `id`                    bigint unsigned NOT NULL AUTO_INCREMENT,
  `ref_staff_id`          bigint unsigned NOT NULL,
  `ref_branch_id`         bigint unsigned DEFAULT NULL,
  `period_ym`             char(7) NOT NULL COMMENT "รอบเดือน เช่น '2026-07'",
  `mode`                  enum('sales','rounds') NOT NULL COMMENT 'โหมดของคนนี้ ณ เดือนนั้น',
  `accumulated_sales`     decimal(14,2) NOT NULL DEFAULT '0.00',
  `accumulated_rounds`    int unsigned  NOT NULL DEFAULT '0',
  `current_rank`          tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'rank ที่ได้ (ค้างไว้=ค่าสูงสุดของเดือน)',
  `applied_rate`          decimal(5,2)  NOT NULL DEFAULT '0.00' COMMENT 'เรต % ที่ใช้คำนวณจริง',
  `applied_min_threshold` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'เกณฑ์ขั้นต่ำของ rank ที่ตัดได้',
  `applied_payout_type`   enum('percent','fixed_per_round','fixed') NOT NULL DEFAULT 'percent',
  `applied_fixed_amount`  decimal(12,2) DEFAULT NULL,
  `commission_amount`     decimal(14,2) NOT NULL DEFAULT '0.00',
  `rank_table_snapshot`   json DEFAULT NULL COMMENT 'ภาพรวมบันได rank ทั้งชุด ณ ตอนคำนวณ/ปิดเดือน',
  `period_start`          datetime DEFAULT NULL,
  `period_end`            datetime DEFAULT NULL,
  `is_finalized`          tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = ปิดเดือนแล้ว ค่าคงที่',
  `finalized_at`          timestamp NULL DEFAULT NULL,
  `created_at`            timestamp NULL DEFAULT NULL,
  `updated_at`            timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commission_progress_staff_period_unique` (`ref_staff_id`,`period_ym`),
  KEY `commission_monthly_progress_period_ym_mode_index` (`period_ym`,`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- (ตัวอย่าง seed) บันได Rank ยอดขาย 5 ขั้น แบบ default ทุกสาขา
-- ปรับ min_threshold ตามจริงได้ในหลังบ้าน
-- =====================================================================
-- INSERT INTO `commission_ranks`
--   (`ref_branch_id`,`mode`,`rank_no`,`min_threshold`,`rate`,`payout_type`,`created_at`,`updated_at`) VALUES
--   (NULL,'sales',1,0,1.00,'percent',NOW(),NOW()),
--   (NULL,'sales',2,20000,1.50,'percent',NOW(),NOW()),
--   (NULL,'sales',3,50000,2.00,'percent',NOW(),NOW()),
--   (NULL,'sales',4,80000,2.50,'percent',NOW(),NOW()),
--   (NULL,'sales',5,120000,3.00,'percent',NOW(),NOW());
