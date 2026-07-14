-- =====================================================================
-- เพิ่มฟิลด์ snapshot ระบบ Rank ลงตารางประวัติรอบคอมมิชชั่น
-- ใช้กับปุ่ม "พิมพ์ + บันทึกรอบ" ในหน้ารายงานค่าคอม (นวด+สินค้า)
-- DB: zwek_addict
-- =====================================================================

ALTER TABLE `history_commissions`
  ADD COLUMN `rank_no`            tinyint unsigned NOT NULL DEFAULT 0 AFTER `type`,
  ADD COLUMN `mode`               varchar(20) NULL AFTER `rank_no`,
  ADD COLUMN `payout_type`        varchar(20) NULL AFTER `mode`,
  ADD COLUMN `accumulated_rounds` int unsigned NOT NULL DEFAULT 0 AFTER `sales_received`;

-- หมายเหตุ:
-- รันซ้ำไม่ได้ ถ้าเคยเพิ่มคอลัมน์แล้วจะ error Duplicate column ให้ข้าม
-- ก่อนรัน SQL นี้ ปุ่มบันทึกรอบยังทำงานได้ (โค้ดกันไว้ด้วย hasColumn) แต่จะยังไม่เก็บ Rank/รอบ ในประวัติ
