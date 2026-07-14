-- =====================================================================
-- ADDICT — ติดตั้งฐานข้อมูลรวมทุกฟีเจอร์ (รันซ้ำได้ ปลอดภัย)
--   1) ระบบคอมมิชชั่นทีมมาม่าแบบ Rank (2 หมวด: service = นวด+สินค้า, drink = ดื่ม)
--   2) ประวัติรอบคอมมิชชั่น (snapshot Rank)
--   3) ระบบลงเวลาเข้างาน (แตะบัตร)
--
-- ตาราง = CREATE TABLE IF NOT EXISTS
-- คอลัมน์บนตารางเดิม (users, history_commissions) = เพิ่มเฉพาะเมื่อยังไม่มี (ผ่าน stored procedure)
-- DB: zwek_addict  |  MySQL 8.x
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1) commission_ranks : บันได Rank (แก้ได้จากหลังบ้าน) แยกหมวด/โหมด
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `commission_ranks` (
  `id`            bigint unsigned NOT NULL AUTO_INCREMENT,
  `ref_branch_id` bigint unsigned DEFAULT NULL COMMENT 'null = ค่ากลาง ใช้ทุกสาขา',
  `category`      enum('service','drink') NOT NULL DEFAULT 'service' COMMENT 'service=นวด+สินค้า, drink=ดื่ม',
  `mode`          enum('sales','rounds') NOT NULL COMMENT 'บันไดยอดขาย หรือ จำนวนรอบ',
  `rank_no`       tinyint unsigned NOT NULL COMMENT '1-5',
  `min_threshold` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'ยอดสะสมขั้นต่ำ(บาท) หรือ จำนวนรอบขั้นต่ำ',
  `rate`          decimal(5,2)  NOT NULL DEFAULT '0.00' COMMENT '% คอมมิชชั่นของ rank นี้',
  `fixed_amount`  decimal(12,2) DEFAULT NULL COMMENT 'เงินคงที่ (payout_type fixed/fixed_per_round)',
  `payout_type`   enum('percent','fixed_per_round','fixed') NOT NULL DEFAULT 'percent',
  `created_at`    timestamp NULL DEFAULT NULL,
  `updated_at`    timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commission_ranks_branch_cat_mode_rank_unique` (`ref_branch_id`,`category`,`mode`,`rank_no`),
  KEY `commission_ranks_cat_mode_min_index` (`category`,`mode`,`min_threshold`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2) commission_monthly_progress : สถานะสะสมรายเดือน + snapshot ที่ย้อนตรวจได้
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `commission_monthly_progress` (
  `id`                    bigint unsigned NOT NULL AUTO_INCREMENT,
  `ref_staff_id`          bigint unsigned NOT NULL,
  `ref_branch_id`         bigint unsigned DEFAULT NULL,
  `period_ym`             char(7) NOT NULL COMMENT "รอบเดือน เช่น '2026-07'",
  `category`              enum('service','drink') NOT NULL DEFAULT 'service',
  `mode`                  enum('sales','rounds') NOT NULL,
  `accumulated_sales`     decimal(14,2) NOT NULL DEFAULT '0.00',
  `accumulated_rounds`    int unsigned  NOT NULL DEFAULT '0',
  `current_rank`          tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'ค้างไว้ = ค่าสูงสุดของเดือน',
  `applied_rate`          decimal(5,2)  NOT NULL DEFAULT '0.00',
  `applied_min_threshold` decimal(12,2) NOT NULL DEFAULT '0.00',
  `applied_payout_type`   enum('percent','fixed_per_round','fixed') NOT NULL DEFAULT 'percent',
  `applied_fixed_amount`  decimal(12,2) DEFAULT NULL,
  `commission_amount`     decimal(14,2) NOT NULL DEFAULT '0.00',
  `rank_table_snapshot`   json DEFAULT NULL COMMENT 'บันได rank ทั้งชุด ณ ตอนคำนวณ/ปิดเดือน',
  `period_start`          datetime DEFAULT NULL,
  `period_end`            datetime DEFAULT NULL,
  `is_finalized`          tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = ปิดเดือนแล้ว ค่าคงที่',
  `finalized_at`          timestamp NULL DEFAULT NULL,
  `created_at`            timestamp NULL DEFAULT NULL,
  `updated_at`            timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commission_progress_staff_period_cat_unique` (`ref_staff_id`,`period_ym`,`category`),
  KEY `commission_progress_period_cat_mode_index` (`period_ym`,`category`,`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3) work_attendances : ลงเวลาเข้างานแบบแตะบัตร
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `work_attendances` (
  `id`            bigint unsigned NOT NULL AUTO_INCREMENT,
  `ref_staff_id`  bigint unsigned NOT NULL,
  `ref_branch_id` bigint unsigned DEFAULT NULL,
  `work_date`     date NOT NULL,
  `check_in_at`   datetime DEFAULT NULL,
  `check_out_at`  datetime DEFAULT NULL,
  `status`        enum('working','left','auto_ended') NOT NULL DEFAULT 'working'
                  COMMENT 'working=กำลังทำงาน, left=แตะออก/ลา, auto_ended=ตี3ปิดอัตโนมัติ',
  `created_at`    timestamp NULL DEFAULT NULL,
  `updated_at`    timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `work_attendances_staff_date_unique` (`ref_staff_id`,`work_date`),
  KEY `work_attendances_date_branch_index` (`work_date`,`ref_branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4) เพิ่มคอลัมน์บนตารางเดิม เฉพาะเมื่อยังไม่มี (idempotent)
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `_add_col_if_missing`;
DELIMITER $$
CREATE PROCEDURE `_add_col_if_missing`(IN p_tbl VARCHAR(64), IN p_col VARCHAR(64), IN p_ddl TEXT)
BEGIN
  IF (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_tbl AND COLUMN_NAME = p_col) = 0 THEN
    SET @s = CONCAT('ALTER TABLE `', p_tbl, '` ADD COLUMN ', p_ddl);
    PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
  END IF;
END$$
DELIMITER ;

-- users: โหมดคอมมิชชั่นต่อคน (นวด+สินค้า และ ดื่ม)
CALL `_add_col_if_missing`('users','commission_mode',
  '`commission_mode` ENUM(''sales'',''rounds'') NULL DEFAULT ''sales'' COMMENT ''โหมดคอมฯ นวด+สินค้า'' AFTER `ref_position_id`');
CALL `_add_col_if_missing`('users','drink_commission_mode',
  '`drink_commission_mode` ENUM(''sales'',''rounds'') NULL DEFAULT ''sales'' COMMENT ''โหมดคอมฯ ดื่ม'' AFTER `commission_mode`');

-- history_commissions: snapshot Rank สำหรับปุ่ม "พิมพ์ + บันทึกรอบ"
CALL `_add_col_if_missing`('history_commissions','rank_no',
  '`rank_no` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `type`');
CALL `_add_col_if_missing`('history_commissions','mode',
  '`mode` VARCHAR(20) NULL AFTER `rank_no`');
CALL `_add_col_if_missing`('history_commissions','payout_type',
  '`payout_type` VARCHAR(20) NULL AFTER `mode`');
CALL `_add_col_if_missing`('history_commissions','accumulated_rounds',
  '`accumulated_rounds` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `sales_received`');

DROP PROCEDURE IF EXISTS `_add_col_if_missing`;

-- ---------------------------------------------------------------------
-- (ตัวอย่าง seed) บันได Rank ยอดขาย 5 ขั้น หมวดนวด+สินค้า ค่ากลาง — เปิดใช้ได้ตามต้องการ
-- ---------------------------------------------------------------------
-- INSERT INTO `commission_ranks`
--   (`ref_branch_id`,`category`,`mode`,`rank_no`,`min_threshold`,`rate`,`payout_type`,`created_at`,`updated_at`) VALUES
--   (NULL,'service','sales',1,0,1.00,'percent',NOW(),NOW()),
--   (NULL,'service','sales',2,20000,1.50,'percent',NOW(),NOW()),
--   (NULL,'service','sales',3,50000,2.00,'percent',NOW(),NOW()),
--   (NULL,'service','sales',4,80000,2.50,'percent',NOW(),NOW()),
--   (NULL,'service','sales',5,120000,3.00,'percent',NOW(),NOW());
