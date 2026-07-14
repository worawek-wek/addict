# แผนพัฒนา: ระบบคอมมิชชั่นทีมมาม่าแบบ Rank (Mama Rank Commission)

> สถานะ: **เสร็จครบทุก Phase (0-6)** — commission พร้อมใช้งาน (เหลือรัน SQL 2 ไฟล์ + ตั้ง cron)
> อ้างอิงสเปกจาก `COMMISSION_FUTURE_WORK.txt` + Concept ที่ผู้ใช้ยืนยัน 2026-07-13
> หน้าเป้าหมาย: `/admin/commission/view-sales`

## ความคืบหน้า
- [x] **Phase 0** — migration `2026_07_13_000001_create_mama_rank_commission_tables.php` + raw SQL `database/sql/mama_rank_commission.sql`
- [x] **Phase 1** — `app/Models/CommissionRank.php`, `app/Support/MamaCommissionCalculator.php`, `User::scopeMama()` + `commission_mode` fillable
- [x] **Phase 4** — `view_sales_datatable()` + `view_sales_table.blade.php` แสดง โหมด/ยอดสะสม/รอบ/Rank/เรต/คอมฯ (กรองมาม่า `!= 2`)
- [x] **Phase 2** — `CommissionRankController` + `commission_ranks.blade.php` (บันได 5 ขั้น × 2 โหมด ต่อสาขา/ค่ากลาง) + route `commission-ranks` + ปุ่มเข้าจากหน้า view-sales
- [x] **Phase 3** — dropdown `commission_mode` ในหน้าแก้ไข user (`user/view.blade.php`) + save ใน `UserController@store/update` (โชว์เฉพาะ position != 2)
- [x] **Phase 5** — command `commission:close-month` (+`--current`/`--period=`) เขียน snapshot ลง `commission_monthly_progress` (latch rank, กันเดือนที่ปิดแล้ว, upsert ไม่ซ้ำ) + เปิด scheduler ใน Kernel (ปิดรอบวันที่ 1 เวลา 00:05, อัปเดตเดือนปัจจุบันทุกวัน 00:15) — ทดสอบรันจริงผ่าน php 8.2 แล้ว
- [x] **Phase 6** — `view_sales_pdf` + `save_commission_history` ใช้ `MamaCommissionCalculator` แล้ว (กรองมาม่า != 2), PDF/ประวัติรอบ แสดงคอลัมน์ Rank ตรงกับหน้าจอ, เพิ่มคอลัมน์ snapshot ใน `history_commissions` (rank_no, mode, payout_type, accumulated_rounds) — ต้องรัน `database/sql/history_commissions_rank_columns.sql`

## ข้อสรุปที่ยืนยันแล้ว (Decisions)

1. **วิธีตัด Rank** = สะสม**รายเดือน** + rank **ค้างไว้** (latch) — rank เลื่อนขึ้นเมื่อยอด/รอบสะสมของเดือนนั้นถึงเกณฑ์ และไม่ลดลงภายในเดือน คอมฯ คิดจาก % ของ rank ปัจจุบัน
2. **1 รอบ = 1 order** — โหมดนับรอบนับจากจำนวน order ที่ `ref_seller_id = มาม่า`
3. **มาม่า = users ที่ `ref_position_id != 2`** (ทุกตำแหน่งยกเว้นพนักงานนวด) — เปลี่ยนจากเดิมที่กรองแค่ `= 1`
4. รีเซ็ตยอด/รอบสะสมอัตโนมัติทุกเดือน แต่เก็บประวัติย้อนหลัง

## สถาปัตยกรรมปัจจุบัน (ที่จะถูกแทน/ต่อยอด)

- คิดคอมฯ ใน `view_sales_table.blade.php` (คำนวณใน blade) + `save_commission_history()`
- ยอดขาย = `SUM(order_has_products.price)` ของ order `ref_seller_id=staff` `type IN (1,2)` ในช่วงวันทำการ
- Tier = `sales_commission_tiers` (bracket min-max, `commission_by` 1=%/2=บาทคงที่) — **ไม่มีเลข rank, ไม่สะสม, ไม่มีโหมดนับรอบ, ไม่มีการเลือกโหมดต่อคน**
- ประวัติ = `history_commissions` (snapshot ต่อ round, บันทึกมือ)

---

## Phase 0 — Database (migrations ใหม่ทั้งหมด)

> เพิ่มตารางใหม่ ไม่แตะ `sales_commission_tiers` เดิม (คงไว้ให้ระบบ drink/ของเดิมไม่พัง)

### 0.1 `commission_ranks` — บันไดขั้น Rank (แก้ได้จากหลังบ้าน)
| คอลัมน์ | ชนิด | หมายเหตุ |
|---|---|---|
| id | PK | |
| ref_branch_id | int null | null = ค่า default ทุกสาขา (fallback แบบเดียวกับ CourseCommissionCalculator) |
| mode | enum('sales','rounds') | บันได 2 ชุดแยกกัน (ตามสเปกข้อ "แยกเงื่อนไข ยอด/รอบ") |
| rank_no | tinyint (1–5) | |
| min_threshold | decimal(12,2) | ยอดสะสมขั้นต่ำ (บาท) หรือ จำนวนรอบขั้นต่ำ ที่ทำให้ถึง rank นี้ |
| rate | decimal(5,2) | % คอมฯ ของ rank นี้ (เช่น 1.00, 1.50 … 3.00) |
| fixed_amount | decimal(12,2) null | สำหรับกรณีจ่ายคงที่ (เผื่อโหมดรอบจ่ายบาท/รอบ) |
| payout_type | enum('percent','fixed_per_round','fixed') default 'percent' | วิธีจ่ายของ rank นี้ |

Unique: (`ref_branch_id`, `mode`, `rank_no`)

### 0.2 `users.commission_mode` — โหมดของพนักงานแต่ละคน
- `ALTER TABLE users ADD commission_mode ENUM('sales','rounds') NULL DEFAULT 'sales'`
- คอลัมน์เดียว = การันตี "เลือกได้ 1 ระบบ" โดยธรรมชาติ

### 0.3 `commission_monthly_progress` — สถานะสะสมรายเดือน + snapshot (แหล่งข้อมูล Dashboard **และ** ประวัติ)
| คอลัมน์ | ชนิด | หมายเหตุ |
|---|---|---|
| id | PK | |
| ref_staff_id | bigint | |
| ref_branch_id | bigint null | |
| period_ym | char(7) | เช่น '2026-07' — key แยกเดือน |
| mode | enum('sales','rounds') | snapshot โหมดของคนนั้นในเดือนนั้น |
| accumulated_sales | decimal(14,2) | ยอดสะสมของเดือน |
| accumulated_rounds | int | รอบสะสมของเดือน |
| current_rank | tinyint | **ค้างไว้** = `max(ที่เก็บ, ที่คำนวณใหม่)` |
| applied_rate | decimal(5,2) | **snapshot** เรต % ที่ใช้จริง |
| applied_min_threshold | decimal(12,2) | **snapshot** เกณฑ์ของ rank ที่ตัดได้ |
| applied_payout_type | enum(...) | **snapshot** วิธีจ่าย |
| applied_fixed_amount | decimal(12,2) null | **snapshot** เงินคงที่ |
| commission_amount | decimal(14,2) | คอมฯ ที่คำนวณได้ |
| rank_table_snapshot | json null | **บันได rank ทั้งชุด ณ ตอนนั้น** (ตรวจ "ตัดยังไง" ได้ครบ) |
| period_start / period_end | datetime | ช่วงวันทำการของเดือนนั้น |
| is_finalized / finalized_at | bool / ts | ปิดเดือนแล้ว = ค่าคงที่ ไม่คำนวณใหม่ |

Unique: (`ref_staff_id`, `period_ym`)
> เดือนใหม่ = แถวใหม่ (period ต่างกัน) → รีเซ็ตสะสมอัตโนมัติ + ประวัติเดือนเก่าคงอยู่เอง

### 0.4 Auditability — ย้อนตรวจได้แม้เดือนหน้าเปลี่ยนเกณฑ์
**หลักการ: config แยกจาก snapshot**
- `commission_ranks` = ค่าปัจจุบันที่แก้ได้ → การแก้กระทบเฉพาะการคำนวณ**ในอนาคต**
- แถวใน `commission_monthly_progress` เก็บค่าที่ **ใช้จริง** แบบ denormalized (`current_rank`, `applied_rate`, `applied_min_threshold`, `applied_payout_type`, `applied_fixed_amount`, `commission_amount`) + `rank_table_snapshot` (JSON บันไดทั้งชุด)
- ปิดเดือน → `is_finalized=1`, freeze ค่า → แก้ config เดือนถัดไป**ไม่แตะ**แถวที่ปิดแล้ว

ผลลัพธ์: เปิดดูคน X เดือน 2026-07 (รอบนั้น) เห็นครบว่า **rank อะไร / ตัดที่เกณฑ์เท่าไหร่ / เรตกี่ % / ได้คอมเท่าไหร่ / บันไดทั้งชุดตอนนั้นเป็นยังไง** — ไม่เพี้ยนตามการแก้เดือนใหม่

---

## Phase 1 — Domain layer (Service + Models)

- `app/Models/CommissionRank.php` — model + scope `forBranchMode($branchId,$mode)` พร้อม fallback global
- `app/Support/MamaCommissionCalculator.php` — หัวใจการคำนวณ:
  - input: `User $staff`, `Carbon $periodStart`, `Carbon $periodEnd` (หรือ period_ym)
  - รวมยอด/นับรอบจาก order (`ref_seller_id=staff`, type ที่กำหนด, ช่วงเวลาแบบ AdminBusinessDay)
  - หา rank = rank_no สูงสุดที่ `min_threshold <= ค่าสะสม`
  - คำนวณคอมฯ ตาม `payout_type`:
    - percent → `rate% × accumulated_sales`
    - fixed_per_round → `fixed_amount × accumulated_rounds`
    - fixed → `fixed_amount`
  - คืน `{acc_sales, acc_rounds, rank, rate, commission, payout_type}`
- ใช้ซ้ำได้ทั้ง Dashboard, PDF, และ command ปิดเดือน (เลิกคำนวณใน blade)

---

## Phase 2 — หลังบ้าน: จัดการ Rank (Admin CRUD)

- ต่อยอด `SalesCommissionTierController` หรือแยก `CommissionRankController`
- 2 หน้า/แท็บ: **บันได Rank (ยอดขาย)** และ **บันได Rank (จำนวนรอบ)** — เลือกสาขา, แก้ 5 ranks ต่อชุด (rank_no, min_threshold, rate/amount, payout_type)
- routes เพิ่มใต้ `admin` group + เมนูใน `app/Main/SideMenu.php`

## Phase 3 — ตั้งโหมดต่อพนักงาน

- หน้าแก้ไข user (`UserController@edit` + view): เพิ่ม dropdown `commission_mode` (ยอดขาย % / จำนวนรอบ)
- `UserController@update` บันทึกค่า

## Phase 4 — ปรับ Dashboard `view-sales`

- แก้ `view_sales_datatable` + `view_sales_table.blade.php`:
  - กรองมาม่า `where('ref_position_id','!=',2)` (แทน `=1`)
  - คอลัมน์ใหม่: ชื่อ | โหมด | ยอดสะสม | จำนวนรอบ | **Rank ปัจจุบัน** | คอมมิชชั่น | สาขา
  - เรียก `MamaCommissionCalculator` แทนการคิดใน blade
  - toggle มุมมอง **รายวัน / รายเดือน** + เลือกเดือน
- อัปเดต `view_sales_pdf` ให้มีคอลัมน์ rank/รอบ

## Phase 5 — รีเซ็ตรายเดือน + ประวัติ

- Command ใหม่ `commission:close-month` — snapshot `commission_monthly_progress` เดือนที่ปิด → `history_commissions` (ขยายฟิลด์ rank/rounds), เดือนใหม่เริ่มที่ 0 เอง
- เปิด scheduler ใน `app/Console/Kernel.php` (ตอนนี้ comment อยู่) → รันวันที่ 1 เวลา 00:05 + ตั้ง cron `php artisan schedule:run`
- มี CloseExpiredOrders command อยู่แล้วเป็นตัวอย่าง pattern

## Phase 6 — รายงาน

- ขยาย `history_commissions` (เพิ่ม `rank_no`, `accumulated_rounds`, `mode`) + วิว round history / PDF ให้โชว์ rank & รอบ
- รายงานช่วงวันที่ (มีโครงเดิม) ต่อยอดให้ครบ: ยอด/รอบ/คอมฯ ต่อคน + สรุปรายเดือน

---

## คำถามที่ยังต้องเคลียร์ก่อนลงมือ (Open questions)

1. **โหมดนับรอบจ่ายยังไง?** สเปกระบุแค่ "rank ตามจำนวนรอบ" แต่ไม่ได้บอกสูตรจ่าย — จ่าย % ของยอดขาย (โดย rank มาจากจำนวนรอบ) หรือ จ่ายบาทคงที่/รอบ? (แผนรองรับทั้งคู่ผ่าน `payout_type`)
2. **order type ที่นับ** — ยอดขาย/รอบ ปัจจุบันกรอง `type IN (1,2)` ใช่เกณฑ์เดียวกันไหม?
3. **รวมผู้จัดการ (position 3) เป็นมาม่าด้วยไหม?** — เกณฑ์ `!= 2` จะรวมทุกตำแหน่งรวมผจก./แคชเชียร์
4. **Rank แยกตามสาขา หรือใช้ชุดเดียวทั้งระบบ?** (แผนรองรับ per-branch + fallback global)
5. **ข้อมูล tier เดิม** ใน `sales_commission_tiers` — ต้อง migrate เข้ารูปแบบ rank ใหม่ หรือปล่อยทิ้ง/คงไว้เฉพาะ drink?

## ลำดับการทำ (แนะนำ)
Phase 0 → 1 (แกนคำนวณ + ทดสอบด้วยข้อมูลจริง) → 4 (ให้เห็นผลบน Dashboard เร็ว) → 2 → 3 → 5 → 6
