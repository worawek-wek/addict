-- =====================================================================
-- ADDICT — แก้ error: Unknown column 'category'
--   เพิ่มคอลัมน์ category (หมวด service/drink) และปรับ unique index
--   ให้ commission_ranks / commission_monthly_progress รองรับ 2 หมวด
--   รันซ้ำได้ปลอดภัย (idempotent)  |  DB: zwek_addict  |  MySQL 8.x
-- =====================================================================

SET NAMES utf8mb4;

DROP PROCEDURE IF EXISTS `_add_col_if_missing`;
DROP PROCEDURE IF EXISTS `_drop_index_if_exists`;
DROP PROCEDURE IF EXISTS `_add_index_if_missing`;
DELIMITER $$

CREATE PROCEDURE `_add_col_if_missing`(IN p_tbl VARCHAR(64), IN p_col VARCHAR(64), IN p_ddl TEXT)
BEGIN
  IF (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_tbl AND COLUMN_NAME = p_col) = 0 THEN
    SET @s = CONCAT('ALTER TABLE `', p_tbl, '` ADD COLUMN ', p_ddl);
    PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
  END IF;
END$$

CREATE PROCEDURE `_drop_index_if_exists`(IN p_tbl VARCHAR(64), IN p_idx VARCHAR(64))
BEGIN
  IF (SELECT COUNT(*) FROM information_schema.STATISTICS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_tbl AND INDEX_NAME = p_idx) > 0 THEN
    SET @s = CONCAT('ALTER TABLE `', p_tbl, '` DROP INDEX `', p_idx, '`');
    PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
  END IF;
END$$

CREATE PROCEDURE `_add_index_if_missing`(IN p_tbl VARCHAR(64), IN p_idx VARCHAR(64), IN p_ddl TEXT)
BEGIN
  IF (SELECT COUNT(*) FROM information_schema.STATISTICS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_tbl AND INDEX_NAME = p_idx) = 0 THEN
    SET @s = CONCAT('ALTER TABLE `', p_tbl, '` ADD ', p_ddl);
    PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
  END IF;
END$$

DELIMITER ;

-- ---------------------------------------------------------------------
-- 1) commission_ranks : เพิ่ม category + สลับ unique ให้รวม category
--    (ข้อมูลเดิมทั้งหมดกลายเป็น service อัตโนมัติ)
-- ---------------------------------------------------------------------
CALL `_add_col_if_missing`('commission_ranks','category',
  '`category` ENUM(''service'',''drink'') NOT NULL DEFAULT ''service'' COMMENT ''service=นวด+สินค้า, drink=ดื่ม'' AFTER `ref_branch_id`');
CALL `_drop_index_if_exists`('commission_ranks','commission_ranks_branch_mode_rank_unique');
CALL `_add_index_if_missing`('commission_ranks','commission_ranks_branch_cat_mode_rank_unique',
  'UNIQUE `commission_ranks_branch_cat_mode_rank_unique` (`ref_branch_id`,`category`,`mode`,`rank_no`)');

-- ---------------------------------------------------------------------
-- 2) commission_monthly_progress : เพิ่ม category + สลับ unique
-- ---------------------------------------------------------------------
CALL `_add_col_if_missing`('commission_monthly_progress','category',
  '`category` ENUM(''service'',''drink'') NOT NULL DEFAULT ''service'' AFTER `period_ym`');
CALL `_drop_index_if_exists`('commission_monthly_progress','commission_progress_staff_period_unique');
CALL `_add_index_if_missing`('commission_monthly_progress','commission_progress_staff_period_cat_unique',
  'UNIQUE `commission_progress_staff_period_cat_unique` (`ref_staff_id`,`period_ym`,`category`)');

-- ---------------------------------------------------------------------
-- 3) users : โหมดคอมมิชชั่นต่อคน (เผื่อ server ยังไม่มีคอลัมน์หมวดดื่ม)
-- ---------------------------------------------------------------------
CALL `_add_col_if_missing`('users','commission_mode',
  '`commission_mode` ENUM(''sales'',''rounds'') NULL DEFAULT ''sales'' COMMENT ''โหมดคอมฯ นวด+สินค้า'' AFTER `ref_position_id`');
CALL `_add_col_if_missing`('users','drink_commission_mode',
  '`drink_commission_mode` ENUM(''sales'',''rounds'') NULL DEFAULT ''sales'' COMMENT ''โหมดคอมฯ ดื่ม'' AFTER `commission_mode`');

DROP PROCEDURE IF EXISTS `_add_col_if_missing`;
DROP PROCEDURE IF EXISTS `_drop_index_if_exists`;
DROP PROCEDURE IF EXISTS `_add_index_if_missing`;
