<?php

use App\Http\Controllers\AddonOptionController;
use App\Http\Controllers\Admin\OrderRoomController;
use App\Http\Controllers\Admin\OrderProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Front\CustomerLoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DarkModeController;
use App\Http\Controllers\Front\FrontHomeController;
use App\Http\Controllers\Front\FrontClockInController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CheerChargeController;
use App\Http\Controllers\ColorSchemeController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\Front\OrderCusController;
use App\Http\Controllers\pos\POSController;
use App\Http\Controllers\pos\RoomPOSController;
use App\Http\Controllers\SalesCommissionTierController;
use App\Http\Controllers\RoomGroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/clc', function () {
    
	Artisan::call('cache:clear');
	Artisan::call('config:clear');
	Artisan::call('config:cache');
	Artisan::call('view:clear');
    Artisan::call('route:clear');
  
	return "Cleared!";
  
});

Route::middleware('auth')->prefix('pos')->name('pos.')->group(function () {
    Route::get('/api/search-users', function (Request $request) {
        $q = $request->get('q', '');
        $users = \App\Models\Customer::query()
            ->where('phone', 'like', "%{$q}%")
            ->orWhere('name', 'like', "%{$q}%")
            ->limit(20)
            ->get(['id', 'name', 'phone']);

        return response()->json($users);
    });
    Route::get('/api/search-staff', [POSController::class, 'searchStaff'])->name('api.searchStaff');
    Route::get('/api/search-addons', [PosController::class, 'searchAddons'])->name('api.searchAddons');
    Route::get('/api/sales-staff', [PosController::class, 'searchSalesStaff'])->name('api.searchSalesStaff');

    Route::controller(RoomPOSController::class)->group(function () {
        Route::get('/room', 'index')->name('room.index');
        Route::get('/room/{roomId}/customers', 'getCustomers')->name('room.customers');
    });

    Route::controller(POSController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/product', 'product')->name('product');
        Route::get('/product/{product_id}', 'product')->name('product');
        Route::post('/add/{id}', 'addToCart')->name('add');
        Route::post('/update/{id}', 'updateCart')->name('update');
        Route::post('/remove/{id}', 'removeFromCart')->name('remove');
        Route::post('/checkout', 'checkout')->name('checkout');
        Route::get('/get-user', 'get_user')->name('pos-get-user');
        Route::post('/calculate', 'calculate')->name('api.calculate');
        Route::post('/api/calculate-summary', 'calculateSummary')->name('api.calculateSummary');
        Route::get('/{room_id}', 'index')->name('index');
    });

});

Route::get('/', function () {
    return redirect('login');
});
// API: Get AddonOption by branch
Route::get('/api/addon-options/{branchId}', function ($branchId) {
    return \App\Models\AddonOption::where('branch', $branchId)->orderBy('price', 'asc')->get();
});

Route::get('/register', [CustomerLoginController::class, 'showRegisterForm'])
    ->name('customer.register');

Route::post('/register', [CustomerLoginController::class, 'register'])
    ->name('customer.register.submit');

Route::middleware('guest:customer')->group(function () {
    Route::get('/login', [CustomerLoginController::class, 'showLoginForm'])->name('customer.login');
    Route::post('/login', [CustomerLoginController::class, 'login']);
});

Route::middleware('auth:customer')->group(function () {
    Route::get('/api/get-users-by-branch/{branchId}', function ($branchId) {
        return \App\Models\User::where('ref_status_id', 1)
            ->where('ref_branch_id', $branchId)
            ->get(['id', 'name', 'nickname', 'salary', 'image_name']);
    });
    Route::get('/customer/orders', [OrderCusController::class, 'index'])->name('customer.orders.history');

    Route::post('/logout', [CustomerLoginController::class, 'logout'])->name('customer.logout');

    Route::controller(FrontHomeController::class)->group(function () {
        Route::get('calculate-all', 'calculate_all')->name('calculate-all');
        Route::get('get-name-mama/{id}', 'get_name_mama')->name('get-name-mama');
        Route::get('home/', 'index')->name('dashboard');
        Route::get('{branch}/service-more/{id}', 'service_more')->name('service');
        Route::get('{branch}/service/{id}', 'service')->name('service');
        Route::post('/service', 'insert')->name('insert');
        Route::get('dashboard/overdue', 'overdue')->name('dashboard.overdue');
        Route::get('dashboard/overdue/{id}', 'invoice')->name('dashboard.invoice');
        Route::get('check-availability/{branchId}', 'checkAvailability')->name('check-availability');
        Route::get('check-room-availability/{branchId}', 'checkRoomAvailability')->name('check-room-availability');
    });

    Route::controller(FrontClockInController::class)->group(function () {
        Route::get('{branch}/clock-in', 'index')->name('clock-in');
        Route::post('{branch}/clock-in', 'clock_in')->name('clock-in');
    });
});

Route::get('dark-mode-switcher', [DarkModeController::class, 'switch'])->name('dark-mode-switcher');
Route::get('color-scheme-switcher/{color_scheme}', [ColorSchemeController::class, 'switch'])->name('color-scheme-switcher');

Route::prefix('admin')->group(function () {
    // Massage Default Setting CRUD
    Route::controller(App\Http\Controllers\MassageDefaultSettingController::class)->group(function () {
        Route::get('massage-default-setting', 'index')->name('massage_default_setting.index');
        Route::post('massage-default-setting', 'store')->name('massage_default_setting.store');
        Route::put('massage-default-setting/{id}', 'update')->name('massage_default_setting.update');
    });
    // Sales Commission Tier CRUD
    Route::controller(SalesCommissionTierController::class)->group(function () {
        Route::get('sales-commission-tier', 'index')->name('sales_commission_tier.index');
        Route::post('sales-commission-tier', 'store')->name('sales_commission_tier.store');
        Route::delete('sales-commission-tier/{id}', 'destroy')->name('sales_commission_tier.destroy');
        // Cheer Charge CRUD
        Route::post('cheer-charge', 'storeCheer')->name('cheer_charge.store');
        Route::delete('cheer-charge/{id}', 'destroyCheer')->name('cheer_charge.destroy');
    });
    Route::get('dark-mode-switcher', [DarkModeController::class, 'switch'])->name('dark-mode-switcher');
    Route::get('color-scheme-switcher/{color_scheme}', [ColorSchemeController::class, 'switch'])->name('color-scheme-switcher');

    // Commission CRUD
    Route::controller(CommissionController::class)->group(function () {
        Route::get('commission', 'index')->name('commission.index');
        Route::get('commission/create', 'create')->name('commission.create');
        Route::post('commission', 'store')->name('commission.store');
        Route::get('commission/{id}/edit', 'edit')->name('commission.edit');
        Route::put('commission/{id}', 'update')->name('commission.update');
        Route::delete('commission/{id}', 'destroy')->name('commission.destroy');
        Route::get('commission/view-massage', 'view_massage')->name('commission.view_massage');
        Route::get('commission/view-sales', 'view_sales')->name('commission.view_sales');
        Route::get('commission/sales-orders', 'salesOrders')->name('commission.sales_orders');
        Route::get('commission/massage-orders', 'massageOrders')->name('commission.massage_orders');
    });

    /// Room Group CRUD include assign/remove rooms to/from group
    Route::prefix('room-groups')->controller(RoomGroupController::class)->group(function () {
        Route::get('/', 'index')->name('room_groups.index');
        Route::get('/datatable', 'datatable')->name('room_groups.datatable');
        Route::get('/getAll/{id?}', 'getRoom')->name('room_groups.getAll');
        Route::get('/getRoom/{id}', 'getRoom')->name('room_groups.getRoom');
        Route::post('/addRoom/{id}', 'addRoomToGroup')->name('room_groups.addRoom');
        Route::post('/removeRoom/{roomId}', 'removeRoomFromGroup')->name('room_groups.removeRoom');
        Route::put('update/{id}', 'update')->name('room_groups.update');
        Route::post('/create', 'create')->name('room_groups.create');
        Route::delete('/delete/{id}', 'delete')->name('room_groups.delete');
    });


    ////
    Route::controller(CheerChargeController::class)->group(function () {
        Route::get('cheer-charge', 'index')->name('cheer_charge.index');
        Route::post('cheer-charge', 'store')->name('cheer_charge.store');
        Route::delete('cheer-charge/{id}', 'destroy')->name('cheer_charge.destroy');
    });
    Route::controller(AuthController::class)->middleware('loggedin')->group(function () {
        Route::get('login', 'loginView')->name('admin.login');
        Route::post('login', 'login')->name('login.check');
        Route::get('register', 'registerView')->name('register.index');
        Route::post('register', 'register')->name('register.store');
    });

    Route::middleware('auth')->group(function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get('customer', 'index')->name('customer.index');
            Route::get('customer/datatable', 'datatable')->name('customer.datatable');
            Route::post('customer', 'store')->name('customer.store');
            Route::get('customer/{id}', 'show')->name('customer.show');
            Route::put('customer/{id}', 'updateCus')->name('customer.update');
            Route::post('customer/{customer}/lock', 'lock')->name('customer.lock');
            Route::post('customer/{customer}/unlock', 'unlock')->name('customer.unlock');
            Route::post('customer/{id}/reset-password', 'resetPassword');
        });

        Route::prefix('order-rooms')->group(function () {
            Route::get('/', [OrderRoomController::class, 'index'])->name('order-rooms.index');
            Route::get('/datatable', [OrderRoomController::class, 'datatable'])->name('order-rooms.datatable');
            Route::get('/{id}', [OrderRoomController::class, 'show'])->name('order-rooms.show');
            Route::post('/{id}/status', [OrderRoomController::class, 'updateStatus'])->name('order-rooms.update-status');
            Route::post('/{id}/update-payment-method', [OrderRoomController::class, 'updatePaymentMethod'])->name('order-rooms.update-payment-method');
        });

        Route::prefix('order-products')->group(function () {
            Route::get('/', [OrderProductController::class, 'index'])->name('order-products.index');
            Route::get('/datatable', [OrderProductController::class, 'datatable'])->name('order-products.datatable');
            Route::get('/pdf', [OrderProductController::class, 'pdf'])->name('order-products.pdf');
            Route::post('/closures', [OrderProductController::class, 'closures'])->name('order-products.closures');
            Route::get('/{id}', [OrderProductController::class, 'show'])->name('order-products.show');
            Route::post('/{id}/confirm-payment', [OrderProductController::class, 'confirmPayment'])->name('order-products.update-confirm-payment');
            Route::post('/{id}/update-payment-method', [OrderProductController::class, 'updatePaymentMethod'])->name('order-products.update-payment-method');
        });

        Route::controller(ReportController::class)->group(function () {
            Route::get('report/view-overview', 'view_overview')->name('report.view_overview');
            Route::get('report/rent-bill', 'rent_bill')->name('report.rent_bill');
            Route::get('report/move-in', 'move_in')->name('report.move_in');
            Route::get('report/move-out', 'move_out')->name('report.move_out');
            Route::get('report/bad-debt', 'badDebt')->name('report.bad_debt');
            Route::get('report/monthly-booking', 'monthly_booking')->name('report.monthly_booking');
            Route::get('report/coupon-report-datatable', 'coupon_report_datatable')->name('report.coupon_report.datatable');
            Route::get('report/coupon-report/pdf', 'coupon_report_pdf')->name('report.coupon-report-pdf');
            Route::get('report/coupon-report', 'coupon_report')->name('report.coupon_report');
            Route::get('report/monthly-sale', 'monthly_sale')->name('report.monthly_sale');
            Route::get('report/monthly-sale/pdf', 'monthly_sale_pdf')->name('report.monthly_sale-pdf');
            Route::get('report/report-sale-monthly', 'monthly_sale_datatable')->name('report-sale-monthly.datatable');
            Route::get('report/oversee-employee/pdf', 'oversee_employee_pdf')->name('report.oversee-employee-pdf');
            Route::get('report/oversee-employee', 'oversee_employee')->name('report.oversee_employee');
            Route::get('report/oversee-employee-datatable', 'oversee_employee_datatable')->name('report-oversee-employee.datatable');
        });

        Route::controller(SettingController::class)->group(function () {
            Route::get('setting/fine', 'fine')->name('setting.fine');
            Route::get('setting/fine/datatable', 'fine_datatable')->name('setting.fine-datatable');
            Route::get('setting/fine/{id}', 'fine_edit')->name('setting.fine-edit');
            Route::post('setting/fine/update/{id}', 'fine_update')->name('setting.fine-update');
            Route::get('setting/bank', 'bank')->name('setting.bank');
            Route::get('setting/bank/datatable', 'bank_datatable')->name('setting.bank-datatable');
            Route::post('setting/bank/insert', 'bank_insert')->name('setting.bank-insert');
            Route::get('setting/bank/{id}', 'bank_edit')->name('setting.bank-edit');
            Route::post('setting/bank/update/{id}', 'bank_update')->name('setting.bank-update');
            Route::delete('setting/bank/{id}', 'bank_delete')->name('setting.bank-delete');
        });

        Route::controller(UserController::class)->group(function () {
            Route::post('clock-in', 'clock_in')->name('clock-in');
            Route::delete('user/{id}', 'destroy')->name('user.destroy');
            Route::post('user/update-sort/{id}', 'update_sort')->name('user.update-sort');
            Route::post('user/change-status/{id}', 'change_status')->name('user.change-status');
            Route::get('user', 'index')->name('user');
            Route::get('user/datatable', 'datatable')->name('user.datatable');
            Route::post('user', 'store')->name('user.insert');
            Route::get('user/{id}', 'edit')->name('user');
            Route::get('user/commission-room/{id}', 'edit_commission_room')->name('user.edit-commission-room');
            Route::get('user/commission-option/{id}', 'edit_commission_option')->name('user.edit-commission-option');
            Route::post('user/{id}', 'update')->name('user.update');
            Route::post('user/commission-option/{id}', 'update_commission_option')->name('user.update-commission-option');
            Route::post('user/commission-room/{id}', 'update_commission_room')->name('user.update-commission-room');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('product', 'index')->name('product');
            Route::get('product/datatable', 'datatable')->name('product.datatable');
            Route::post('product/update-sort/{id}', 'update_sort')->name('product.update-sort');
            Route::get('card_stock_report', 'card_stock_report')->name('card_stock_report');
            Route::get('card_stock_report/datatable', 'card_stock_report_datatable')->name('card_stock_report.datatable');
            Route::post('card_stock_report', 'card_stock_report_store')->name('card_stock_report.insert');
            Route::post('product', 'store')->name('product.insert');
            Route::post('product/withdraw-product', 'withdraw')->name('product.withdraw');
            Route::get('product/{id}', 'edit')->name('product');
            Route::post('product/{id}', 'update')->name('product.update');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::get('order', 'index')->name('order');
            Route::get('order/datatable', 'datatable')->name('order.datatable');
            Route::get('sales_report', 'sales_report')->name('sales_report');
            Route::get('sales_report/datatable', 'sales_report_datatable')->name('sales_report.datatable');
        });

        Route::controller(RoomController::class)->group(function () {
            Route::get('room', 'index')->name('room');
            Route::get('room/datatable', 'datatable')->name('room.datatable');
            Route::post('room/change-status/{id}', 'change_status')->name('room.change-status');
            Route::post('room/update-sort/{id}', 'update_sort')->name('room.update-sort');
            Route::post('room', 'store')->name('room.insert');
            Route::get('room/{id}', 'edit')->name('room');
            Route::post('room/{id}', 'update')->name('room.update');
            Route::delete('room/{id}', 'delete')->name('room.delete');    //////////////////////////
        });

        Route::controller(CourseController::class)->group(function () {
            Route::get('course', 'index')->name('course');
            Route::get('course/datatable', 'datatable')->name('course.datatable');
            Route::post('course/update-sort/{id}', 'update_sort')->name('course.update-sort');
            Route::post('course/change-status/{id}', 'change_status')->name('course.change-status');
            Route::post('course', 'store')->name('course.insert');
            Route::get('course/{id}', 'edit')->name('course');
            Route::post('course/{id}', 'update')->name('course.update');
            Route::delete('course/{id}', 'delete')->name('course.delete');    //////////////////////////
        });
        
        Route::controller(RoomTypeController::class)->group(function () {
            Route::get('room-type', 'index')->name('room-type');
            Route::get('room-type/datatable', 'datatable')->name('room-type.datatable');
            Route::post('room-type/update-sort/{id}', 'update_sort')->name('room.update-sort');
            Route::post('room-type/change-status/{id}', 'change_status')->name('room.change-status');
            Route::post('room-type', 'store')->name('room-type.insert');
            Route::get('room-type/{id}', 'edit')->name('room-type');
            Route::post('room-type/{id}', 'update')->name('room-type.update');
            Route::delete('room-type/{id}', 'destroy')->name('room-type.destroy');
        });

        Route::controller(AuditController::class)->group(function () {
            Route::get('audit', 'index')->name('audit');
        });

        Route::controller(BranchController::class)->group(function () {
            Route::get('branch', 'index')->name('branch');
            Route::get('branch/add', 'add')->name('branch.add');
            Route::post('branch/add', 'store')->name('branch.insert');
            Route::get('branch/manage', 'manage')->name('branch.manage');
        });
        // AddonOption CRUD
        Route::controller(AddonOptionController::class)->group(function () {
            Route::get('addon-options', 'index')->name('addon_options.index');
            Route::get('addon-options/create', 'create')->name('addon_options.create');
            Route::post('addon-options', 'store')->name('addon_options.store');
            Route::get('addon-options/{id}/edit', 'edit')->name('addon_options.edit');
            Route::post('addon-options/{id}', 'update')->name('addon_options.update');
            Route::delete('addon-options/{id}', 'destroy')->name('addon_options.destroy');
        });
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    });
});

Route::get('change_date_format/{date}', [UserController::class, 'ChangeDateFormat'])->name('change_date_format');
Route::get('admin/commission/order-detail/{orderId}', [CommissionController::class, 'orderDetailAjax']);
