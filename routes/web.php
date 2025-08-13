<?php

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
use App\Http\Controllers\UserTimeController;
use App\Http\Controllers\DarkModeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Front\FrontHomeController;
use App\Http\Controllers\Front\FrontClockInController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\RenterController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\IncomeExpensesController;
use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\WorkShiftController;
use App\Http\Controllers\WelfareController;
use App\Http\Controllers\ExportExcelController;
use App\Http\Controllers\AnnualHolidayController;
use App\Http\Controllers\ColorSchemeController;

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

    return "Cleared!";
});
Route::get('/', function () {
    return redirect('login');
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

    // logout
    Route::post('/logout', [CustomerLoginController::class, 'logout'])->name('customer.logout');

    // protected controllers
    Route::controller(FrontHomeController::class)->group(function () {
        Route::get('calculate-all', 'calculate_all')->name('calculate-all');
        Route::get('get-name-mama/{id}', 'get_name_mama')->name('get-name-mama');
        Route::get('home/', 'index')->name('dashboard');
        Route::get('{branch}/service-more/{id}', 'service_more')->name('service');
        Route::get('{branch}/service/{id}', 'service')->name('service');
        // Route::post('{branch}/service/{id}', 'insert')->name('insert');
        Route::post('/service', 'insert')->name('insert');

        Route::get('dashboard/overdue', 'overdue')->name('dashboard.overdue');
        Route::get('dashboard/overdue/{id}', 'invoice')->name('dashboard.invoice');
        // Route::get('dashboard/{branch_id}', 'index')->name('dashboard');
    });

    Route::controller(FrontClockInController::class)->group(function () {
        Route::get('{branch}/clock-in', 'index')->name('clock-in');
        Route::post('{branch}/clock-in', 'clock_in')->name('clock-in');
    });
});
// Route::controller(DashboardController::class)->group(function() {                    //////////////////////////
//     Route::get('pos', 'index')->name('dashboard');    //////////////////////////
//     Route::get('dashboard/datatable', 'datatable')->name('room.datatable');    //////////////////////////
//     Route::get('dashboard/overdue', 'overdue')->name('dashboard.overdue');    //////////////////////////
//     Route::get('dashboard/overdue/{id}', 'invoice')->name('dashboard.invoice');    //////////////////////////
//     Route::get('dashboard/{branch_id}', 'index')->name('dashboard');    //////////////////////////
// });

Route::get('dark-mode-switcher', [DarkModeController::class, 'switch'])->name('dark-mode-switcher');
Route::get('color-scheme-switcher/{color_scheme}', [ColorSchemeController::class, 'switch'])->name('color-scheme-switcher');
Route::prefix('admin')->group(function () {
    Route::get('dark-mode-switcher', [DarkModeController::class, 'switch'])->name('dark-mode-switcher');
    Route::get('color-scheme-switcher/{color_scheme}', [ColorSchemeController::class, 'switch'])->name('color-scheme-switcher');

    Route::controller(AuthController::class)->middleware('loggedin')->group(function () {
        Route::get('login', 'loginView')->name('admin.login');
        Route::post('login', 'login')->name('login.check');
        Route::get('register', 'registerView')->name('register.index');
        Route::post('register', 'register')->name('register.store');
    });
    Route::middleware('auth')->group(function () {

        Route::controller(ReportController::class)->group(function () {                    //////////////////////////
            Route::get('report/view-overview', 'view_overview')->name('report.view_overview');    //////////////////////////
            Route::get('report/rent-bill', 'rent_bill')->name('report.rent_bill');    //////////////////////////
            Route::get('report/move-in', 'move_in')->name('report.move_in');    //////////////////////////
            Route::get('report/move-out', 'move_out')->name('report.move_out');    //////////////////////////
            Route::get('report/bad-debt', 'badDebt')->name('report.bad_debt');    //////////////////////////
            Route::get('report/monthly-booking', 'monthly_booking')->name('report.monthly_booking');    //////////////////////////
        });
        Route::controller(SettingController::class)->group(function () {                    //////////////////////////

            Route::get('setting/fine', 'fine')->name('setting.fine');    //////////////////////////
            Route::get('setting/fine/datatable', 'fine_datatable')->name('setting.fine-datatable');    //////////////////////////
            Route::get('setting/fine/{id}', 'fine_edit')->name('setting.fine-edit');    //////////////////////////
            Route::post('setting/fine/update/{id}', 'fine_update')->name('setting.fine-update');    //////////////////////////

            // ////////////////
            Route::get('setting/bank', 'bank')->name('setting.bank');    //////////////////////////
            Route::get('setting/bank/datatable', 'bank_datatable')->name('setting.bank-datatable');    //////////////////////////
            Route::post('setting/bank/insert', 'bank_insert')->name('setting.bank-insert');    //////////////////////////
            Route::get('setting/bank/{id}', 'bank_edit')->name('setting.bank-edit');    //////////////////////////
            Route::post('setting/bank/update/{id}', 'bank_update')->name('setting.bank-update');    //////////////////////////
            Route::delete('setting/bank/{id}', 'bank_delete')->name('setting.bank-delete');    //////////////////////////
            ////////////////
        });
        // Route::controller(PDFController::class)->group(function() {                    //////////////////////////
        //     Route::get('pdf', 'index')->name('dashboard');    //////////////////////////
        //     Route::get('pdf/receipt', 'receipt')->name('Receipt');    //////////////////////////
        // });
        Route::controller(UserController::class)->group(function () {                   //////////////////////////
            Route::post('clock-in', 'clock_in')->name('clock-in');    //////////////////////////
            Route::delete('user/{id}', 'destroy')->name('user.destroy');    //////////////////////////
            Route::post('user/change-status/{id}', 'change_status')->name('user.change-status');    //////////////////////////
            Route::get('user', 'index')->name('user');    //////////////////////////
            Route::get('user/datatable', 'datatable')->name('user.datatable');    //////////////////////////
            Route::post('user', 'store')->name('user.insert');    //////////////////////////
            Route::get('user/{id}', 'edit')->name('user');    //////////////////////////
            Route::post('user/{id}', 'update')->name('user.update');    //////////////////////////
        });
        Route::controller(ProductController::class)->group(function () {                   //////////////////////////
            Route::get('product', 'index')->name('product');    //////////////////////////
            Route::get('product/datatable', 'datatable')->name('product.datatable');    //////////////////////////
            Route::get('card_stock_report', 'card_stock_report')->name('card_stock_report');    //////////////////////////
            Route::get('card_stock_report/datatable', 'card_stock_report_datatable')->name('card_stock_report.datatable');    //////////////////////////
            Route::post('card_stock_report', 'card_stock_report_store')->name('card_stock_report.insert');    //////////////////////////
            Route::post('product', 'store')->name('product.insert');    //////////////////////////
            Route::get('product/{id}', 'edit')->name('product');    //////////////////////////
            Route::post('product/{id}', 'update')->name('product.update');    //////////////////////////
        });
        Route::controller(CustomerController::class)->group(function () {                   //////////////////////////
            Route::get('customer', 'index')->name('customer');    //////////////////////////
            Route::get('customer/datatable', 'datatable')->name('customer.datatable');    //////////////////////////
        });
        Route::controller(OrderController::class)->group(function () {                   //////////////////////////
            Route::get('order', 'index')->name('order');    //////////////////////////
            Route::get('order/datatable', 'datatable')->name('order.datatable');    //////////////////////////
            Route::get('sales_report', 'sales_report')->name('sales_report');    //////////////////////////
            Route::get('sales_report/datatable', 'sales_report_datatable')->name('sales_report.datatable');    //////////////////////////
        });
        Route::controller(RoomController::class)->group(function () {                   //////////////////////////
            Route::get('room', 'index')->name('room');    //////////////////////////
            Route::get('room/datatable', 'datatable')->name('room.datatable');    //////////////////////////
            Route::post('room', 'store')->name('room.insert');    //////////////////////////
            Route::get('room/{id}', 'edit')->name('room');    //////////////////////////
            Route::post('room/{id}', 'update')->name('room.update');    //////////////////////////
        });
        Route::controller(AuditController::class)->group(function () {                   //////////////////////////
            Route::get('audit', 'index')->name('audit');    //////////////////////////
        });
        Route::controller(BranchController::class)->group(function () {                    //////////////////////////
            Route::get('branch', 'index')->name('branch');    //////////////////////////
            Route::get('branch/add', 'add')->name('branch.add');    //////////////////////////
            Route::post('branch/add', 'store')->name('branch.insert');    //////////////////////////
            Route::get('branch/manage', 'manage')->name('branch.manage');    //////////////////////////
        });
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    });
});
/////////////// Ajax ////////////////
Route::get('change_date_format/{date}', [UserController::class, 'ChangeDateFormat'])->name('change_date_format');    //////////////////////////
