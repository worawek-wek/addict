-- =====================================================================
-- ระบบลงเวลาเข้างานแบบแตะบัตร (real-time)
-- Raw SQL (เทียบเท่า migration 2026_07_14_000001_create_work_attendances_table.php)
-- DB: zwek_addict
-- =====================================================================

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
  KEY `work_attendances_work_date_branch_index` (`work_date`,`ref_branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
