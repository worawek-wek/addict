<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * จำกัด user บางคน (User::STOCK_ONLY_IDS เช่น id=235 บัญชี) ให้เข้าได้เฉพาะหน้า stock
 *  - เมนู/หน้าอื่นทั้งหมดถูกกันเข้า (redirect กลับหน้า stock)
 *  - user คนอื่นไม่ได้รับผลกระทบ
 */
class StockOnly
{
    /** path prefix ที่ stock-only user เข้าได้ (ไม่ต้องมี / นำหน้า) */
    private const ALLOWED_PREFIXES = [
        'admin/product',                    // สินค้า + product/*
        'admin/product-type',               // ประเภทสินค้า (ajax ในหน้าสินค้า)
        'admin/card_stock_report',          // สต็อกการ์ด (สินค้า) + export_stock_store
        'admin/drink',                      // ดื่ม + drink/*
        'admin/drink_card_stock_report',    // สต็อกการ์ด (ดื่ม)
        'admin/report/stock-history',       // รายงานสต็อกการ์ด (+ /pdf)
        'admin/report/stock-history-datatable', // datatable ของรายงานสต็อก
        'admin/logout',                     // ออกจากระบบ
    ];

    /** หน้า stock ที่ให้ไปเมื่อพยายามเข้าหน้าอื่น */
    private const HOME = 'admin/product';

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && User::isStockOnly($user->id)) {
            $path = ltrim($request->path(), '/'); // เช่น "admin/product"

            if (!$this->isAllowed($path)) {
                // AJAX/datatable -> 403 JSON, หน้าเว็บปกติ -> redirect
                if ($request->ajax() || $request->wantsJson()) {
                    abort(403, 'เข้าถึงได้เฉพาะเมนูสต็อก');
                }
                return redirect(self::HOME);
            }
        }

        return $next($request);
    }

    private function isAllowed(string $path): bool
    {
        foreach (self::ALLOWED_PREFIXES as $prefix) {
            if ($path === $prefix || Str::startsWith($path, $prefix . '/')) {
                return true;
            }
        }
        return false;
    }
}
