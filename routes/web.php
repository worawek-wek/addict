<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AnalysisController;
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

Route::get('/clc', function() {

	Artisan::call('cache:clear');
	Artisan::call('config:clear');
	Artisan::call('config:cache');
	Artisan::call('view:clear');

	return "Cleared!";

  });
Route::get('/', function () {
    return view('frontend.index');
});

Route::controller(DashboardController::class)->group(function() {                    //////////////////////////
    Route::get('pos', 'index')->name('dashboard');    //////////////////////////
    Route::get('dashboard/datatable', 'datatable')->name('room.datatable');    //////////////////////////
    Route::get('dashboard/overdue', 'overdue')->name('dashboard.overdue');    //////////////////////////
    Route::get('dashboard/overdue/{id}', 'invoice')->name('dashboard.invoice');    //////////////////////////
    Route::get('dashboard/{branch_id}', 'index')->name('dashboard');    //////////////////////////
});
Route::controller(FrontHomeController::class)->group(function() {                    //////////////////////////
    Route::get('calculate-all', 'calculate_all')->name('calculate-all');    //////////////////////////
    Route::get('get-name-mama/{id}', 'get_name_mama')->name('get-name-mama');    //////////////////////////
    Route::get('{branch}/', 'index')->name('dashboard');    //////////////////////////
    Route::get('{branch}/service-more/{id}', 'service_more')->name('service');    //////////////////////////
    Route::get('{branch}/service/{id}', 'service')->name('service');    //////////////////////////
    Route::post('{branch}/service/{id}', 'insert')->name('insert');    //////////////////////////
    Route::get('dashboard/overdue', 'overdue')->name('dashboard.overdue');    //////////////////////////
    Route::get('dashboard/overdue/{id}', 'invoice')->name('dashboard.invoice');    //////////////////////////
    Route::get('dashboard/{branch_id}', 'index')->name('dashboard');    //////////////////////////
});
Route::controller(FrontClockInController::class)->group(function() {                    //////////////////////////
    Route::get('{branch}/clock-in', 'index')->name('clock-in');    //////////////////////////
    Route::post('{branch}/clock-in', 'clock_in')->name('clock-in');    //////////////////////////
});

Route::get('dark-mode-switcher', [DarkModeController::class, 'switch'])->name('dark-mode-switcher');
Route::get('color-scheme-switcher/{color_scheme}', [ColorSchemeController::class, 'switch'])->name('color-scheme-switcher');
Route::prefix('admin')->group(function () {
    Route::get('dark-mode-switcher', [DarkModeController::class, 'switch'])->name('dark-mode-switcher');
    Route::get('color-scheme-switcher/{color_scheme}', [ColorSchemeController::class, 'switch'])->name('color-scheme-switcher');

    Route::controller(AuthController::class)->middleware('loggedin')->group(function() {
        Route::get('login', 'loginView')->name('login.index');
        Route::post('login', 'login')->name('login.check');
        Route::get('register', 'registerView')->name('register.index');
        Route::post('register', 'register')->name('register.store');
    });

    Route::controller(AnalysisController::class)->group(function() {                    //////////////////////////
        Route::get('analysis/monthly-rent', 'monthly_rent')->name('analysis.monthly-rent');    //////////////////////////
        Route::get('analysis/income-expense', 'income_expense')->name('analysis.income-expense');    //////////////////////////
        Route::get('analysis/water', 'water')->name('analysis.water');    //////////////////////////
        Route::get('analysis/elect', 'elect')->name('analysis.elect');    //////////////////////////
        Route::get('analysis/meter', 'meter')->name('analysis.meter');    //////////////////////////
        Route::get('analysis/tenants', 'tenants')->name('analysis.tenants');    //////////////////////////
    });

    Route::controller(ReportController::class)->group(function() {                    //////////////////////////
        Route::get('report/view-overview', 'view_overview')->name('report.view_overview');    //////////////////////////
        Route::get('report/rent-bill', 'rent_bill')->name('report.rent_bill');    //////////////////////////
        Route::get('report/move-in', 'move_in')->name('report.move_in');    //////////////////////////
        Route::get('report/move-out', 'move_out')->name('report.move_out');    //////////////////////////
        Route::get('report/bad-debt', 'badDebt')->name('report.bad_debt');    //////////////////////////
        Route::get('report/monthly-booking', 'monthly_booking')->name('report.monthly_booking');    //////////////////////////
    });
    Route::controller(SettingController::class)->group(function() {                    //////////////////////////

        Route::get('setting/fine', 'fine')->name('setting.fine');    //////////////////////////
        Route::get('setting/fine/datatable', 'fine_datatable')->name('setting.fine-datatable');    //////////////////////////
        Route::get('setting/fine/{id}', 'fine_edit')->name('setting.fine-edit');    //////////////////////////
        Route::post('setting/fine/update/{id}', 'fine_update')->name('setting.fine-update');    //////////////////////////

        // Route::get('setting/manage-bill', 'manage_bill')->name('setting.manage_bill');    //////////////////////////
        // Route::get('setting/rental-contract', 'rental_contract')->name('setting.rental_contract');    //////////////////////////
        // Route::get('setting/dorm-info', 'dorm_info')->name('setting.dorm_info');    //////////////////////////
        // Route::get('setting/room-rent', 'room_rent')->name('setting.room_rent');    //////////////////////////
        // Route::get('setting/room-rent/datatable', 'room_rent_datatable')->name('setting.room-rent-datatable');    //////////////////////////
        // Route::get('setting/room-rent/{id}', 'room_rent_edit')->name('setting.room-rent-edit');    //////////////////////////
        // Route::post('setting/room-rent/update/{id}', 'room_rent_update')->name('setting.room-rent-update');    //////////////////////////

        // Route::get('setting/room-layout', 'room_layout')->name('setting.room_layout');    //////////////////////////
        // Route::post('insert_all', 'insert_all')->name('setting.insert_all');    //////////////////////////
        // ////////////////
        // Route::get('setting/room-layout/building', 'room_layout_building')->name('setting.room_layout_building');    //////////////////////////
        // Route::post('setting/room-layout/building', 'room_layout_building_insert')->name('setting.room_layout_building_insert');    //////////////////////////
        // Route::delete('setting/room-layout/building/{id}', 'room_layout_building_delete')->name('setting.room_layout_building_delete');    //////////////////////////
        // Route::get('setting/room-layout/floor/{building_id}', 'room_layout_floor')->name('setting.room_layout_floor');    //////////////////////////
        // Route::post('setting/room-layout/floor', 'room_layout_floor_insert')->name('setting.room_layout_floor_insert');    //////////////////////////
        // Route::delete('setting/room-layout/floor/{id}', 'room_layout_floor_delete')->name('setting.room_layout_floor_delete');    //////////////////////////
        // Route::get('setting/room-layout/room/{floor_id}', 'room_layout_room')->name('setting.room_layout_room');    //////////////////////////
        // Route::post('setting/room-layout/room/{id}', 'room_layout_room_update')->name('setting.room_layout_room_update');    //////////////////////////
        // Route::post('setting/room-layout/room', 'room_layout_room_insert')->name('setting.room_layout_room_insert');    //////////////////////////
        // Route::delete('setting/room-layout/room/{id}', 'room_layout_room_delete')->name('setting.room_layout_room_delete');    //////////////////////////
        // ////////////////
        // Route::get('setting/water-electric-bill', 'water_electric_bill')->name('setting.water_electric_bill');    //////////////////////////
        // Route::get('setting/water-electric-bill/datatable', 'water_electric_bill_datatable')->name('setting.water_electric_bill-datatable');    //////////////////////////
        // Route::get('setting/water-bill/{id}', 'water_bill_edit')->name('setting.water-bill-edit');    //////////////////////////
        // Route::post('setting/water-bill/update/{id}', 'water_bill_update')->name('setting.water-bill-update');    //////////////////////////
        // Route::get('setting/electric-bill/{id}', 'electric_bill_edit')->name('setting.electric-bill-edit');    //////////////////////////
        // Route::post('setting/electric-bill/update/{id}', 'electric_bill_update')->name('setting.water-electric-bill-update');    //////////////////////////
        // Route::get('setting/service-discount', 'service_discount')->name('setting.service_discount');    //////////////////////////
        // Route::get('setting/service/datatable', 'service_datatable')->name('setting.service-datatable');    //////////////////////////
        // Route::get('setting/service/get_service', 'get_service')->name('setting.service-get_service');    //////////////////////////
        // Route::post('setting/service/insert', 'service_insert')->name('setting.service-insert');    //////////////////////////
        // Route::post('setting/service/update/{id}', 'service_update')->name('setting.service-update');    //////////////////////////
        // Route::get('setting/service/room/{id}', 'service_room')->name('setting.service-room');    //////////////////////////
        // Route::post('setting/service/room/update', 'service_room_update')->name('setting.service-room-update');    //////////////////////////
        // Route::delete('setting/service/delete_on_room', 'service_on_room_delete')->name('setting.service-on-room-delete');    //////////////////////////
        // Route::delete('setting/service/delete/{id}', 'service_delete')->name('setting.service-delete');    //////////////////////////
        // ////////////////
        Route::get('setting/bank', 'bank')->name('setting.bank');    //////////////////////////
        Route::get('setting/bank/datatable', 'bank_datatable')->name('setting.bank-datatable');    //////////////////////////
        Route::post('setting/bank/insert', 'bank_insert')->name('setting.bank-insert');    //////////////////////////
        Route::get('setting/bank/{id}', 'bank_edit')->name('setting.bank-edit');    //////////////////////////
        Route::post('setting/bank/update/{id}', 'bank_update')->name('setting.bank-update');    //////////////////////////
        Route::delete('setting/bank/{id}', 'bank_delete')->name('setting.bank-delete');    //////////////////////////
        ////////////////
    });
    // Route::controller(DashboardController::class)->group(function() {                    //////////////////////////
    //     Route::get('dashboard', 'index')->name('dashboard');    //////////////////////////
    //     Route::get('dashboard/datatable', 'datatable')->name('room.datatable');    //////////////////////////
    //     Route::get('dashboard/overdue', 'overdue')->name('dashboard.overdue');    //////////////////////////
    //     Route::get('dashboard/overdue/{id}', 'invoice')->name('dashboard.invoice');    //////////////////////////
    //     Route::get('dashboard/{branch_id}', 'index')->name('dashboard');    //////////////////////////
    // });
    Route::controller(PDFController::class)->group(function() {                    //////////////////////////
        Route::get('pdf', 'index')->name('dashboard');    //////////////////////////
        Route::get('pdf/receipt', 'receipt')->name('Receipt');    //////////////////////////
    });
    // Route::controller(RoomController::class)->group(function() {                    //////////////////////////
    //     Route::get('room', 'index')->name('room');    //////////////////////////
    //     Route::post('room', 'store')->name('insert');    //////////////////////////
    //     Route::post('room/insert_contract', 'insert_contract')->name('insert_contract');    //////////////////////////
    //     Route::get('room/get-room-rental-contract/{id}', 'get_room_rental_contract')->name('get_room_rental_contract');    //////////////////////////
    //     Route::get('room/get-bill/{id}', 'get_bill')->name('get_bill');    //////////////////////////
    //     Route::get('get-districts/{id}', 'get_districts')->name('get-districts');    //////////////////////////
    //     Route::get('get-subdistricts/{id}', 'get_subdistricts')->name('get-subdistricts');    //////////////////////////
    //     Route::get('get-floors/{id}', 'get_floors')->name('get-floors');    //////////////////////////
    //     Route::get('room/selected', 'selected')->name('room-selected');    //////////////////////////
    //     Route::get('room/datatable', 'datatable')->name('room.datatable');    //////////////////////////
    //     Route::get('room/summary', 'room_summary')->name('room.summary');    //////////////////////////
    //     Route::get('room/{id}', 'show')->name('show');    //////////////////////////

    // });
    Route::controller(MeterController::class)->group(function() {                   //////////////////////////
        Route::get('meter', 'index')->name('meter');    //////////////////////////
        Route::get('meter/water/datatable', 'water_datatable')->name('meter.water-datatable');    //////////////////////////
        Route::post('meter/water_unit/{id}', 'water_unit_update')->name('meter.water-unit_update');    //////////////////////////
        Route::get('meter/electricity/datatable', 'electricity_datatable')->name('meter.electricity-datatable');    //////////////////////////
    });
    Route::controller(RenterController::class)->group(function() {                   //////////////////////////
        Route::get('renter', 'index')->name('renter');    //////////////////////////
    });
    Route::controller(VehicleController::class)->group(function() {                   //////////////////////////
        Route::get('vehicle', 'index')->name('vehicle');    //////////////////////////
    });
    Route::controller(UserController::class)->group(function() {                   //////////////////////////
        Route::post('clock-in', 'clock_in')->name('clock-in');    //////////////////////////
        Route::delete('user/{id}', 'destroy')->name('user.destroy');    //////////////////////////
        Route::post('user/change-status/{id}', 'change_status')->name('user.change-status');    //////////////////////////
        Route::get('user', 'index')->name('user');    //////////////////////////
        Route::get('user/datatable', 'datatable')->name('user.datatable');    //////////////////////////
        Route::post('user', 'store')->name('user.insert');    //////////////////////////
        Route::get('user/{id}', 'edit')->name('user');    //////////////////////////
        Route::post('user/{id}', 'update')->name('user.update');    //////////////////////////
    });
    Route::controller(ProductController::class)->group(function() {                   //////////////////////////
        Route::get('product', 'index')->name('product');    //////////////////////////
        Route::get('product/datatable', 'datatable')->name('product.datatable');    //////////////////////////
        Route::get('card_stock_report', 'card_stock_report')->name('card_stock_report');    //////////////////////////
        Route::get('card_stock_report/datatable', 'card_stock_report_datatable')->name('card_stock_report.datatable');    //////////////////////////
        Route::post('card_stock_report', 'card_stock_report_store')->name('card_stock_report.insert');    //////////////////////////
        Route::post('product', 'store')->name('product.insert');    //////////////////////////
        Route::get('product/{id}', 'edit')->name('product');    //////////////////////////
        Route::post('product/{id}', 'update')->name('product.update');    //////////////////////////
    });
    Route::controller(CustomerController::class)->group(function() {                   //////////////////////////
        Route::get('customer', 'index')->name('customer');    //////////////////////////
        Route::get('customer/datatable', 'datatable')->name('customer.datatable');    //////////////////////////
    });
    Route::controller(OrderController::class)->group(function() {                   //////////////////////////
        Route::get('order', 'index')->name('order');    //////////////////////////
        Route::get('order/datatable', 'datatable')->name('order.datatable');    //////////////////////////
        Route::get('sales_report', 'sales_report')->name('sales_report');    //////////////////////////
        Route::get('sales_report/datatable', 'sales_report_datatable')->name('sales_report.datatable');    //////////////////////////
    });
    Route::controller(RoomController::class)->group(function() {                   //////////////////////////
        Route::get('room', 'index')->name('room');    //////////////////////////
        Route::get('room/datatable', 'datatable')->name('room.datatable');    //////////////////////////
        Route::post('room', 'store')->name('room.insert');    //////////////////////////
        Route::get('room/{id}', 'edit')->name('room');    //////////////////////////
        Route::post('room/{id}', 'update')->name('room.update');    //////////////////////////
    });
    Route::controller(AuditController::class)->group(function() {                   //////////////////////////
        Route::get('audit', 'index')->name('audit');    //////////////////////////
    });
    Route::controller(BillController::class)->group(function() {                    //////////////////////////
        Route::get('bill', 'index')->name('bill');    //////////////////////////
        Route::get('bill/summary', 'bill_summary')->name('bill.summary');    //////////////////////////
        Route::get('bill/datatable', 'datatable')->name('bill.datatable');    //////////////////////////
        Route::get('bill/waiting-for-confirmation', 'waiting_for_confirmation')->name('bill.waiting-for-confirmation');    //////////////////////////
        Route::post('bill/incomplete_update', 'incomplete_update')->name('bill.incomplete_update');    //////////////////////////
        Route::get('bill/{id}', 'invoice')->name('bill.invoice');    //////////////////////////
        Route::post('bill/change_status_bill/{id}', 'change_status_bill')->name('bill.change-status-bill');    //////////////////////////
    });
    Route::controller(ApartmentController::class)->group(function() {                    //////////////////////////
        Route::get('apartment', 'index')->name('apartment');    //////////////////////////
        Route::get('apartment/add', 'add')->name('apartment.add');    //////////////////////////
        Route::post('apartment/add', 'store')->name('apartment.insert');    //////////////////////////
        Route::get('apartment/manage', 'manage')->name('apartment.manage');    //////////////////////////
    });
    Route::controller(BuildingController::class)->group(function() {                    //////////////////////////
        Route::get('building', 'index')->name('building');    //////////////////////////
        Route::get('building/add', 'add')->name('building.add');    //////////////////////////
        Route::post('building/add', 'store')->name('building.insert');    //////////////////////////
        Route::get('building/manage', 'manage')->name('building.manage');    //////////////////////////
    });
    Route::controller(BranchController::class)->group(function() {                    //////////////////////////
        Route::get('branch', 'index')->name('branch');    //////////////////////////
        Route::get('branch/add', 'add')->name('branch.add');    //////////////////////////
        Route::post('branch/add', 'store')->name('branch.insert');    //////////////////////////
        Route::get('branch/manage', 'manage')->name('branch.manage');    //////////////////////////
    });
    Route::controller(IncomeExpensesController::class)->group(function() {                    //////////////////////////
        Route::get('income-expenses', 'index')->name('income-expenses');    //////////////////////////
        Route::post('income-expenses', 'store')->name('income-expenses.insert');    //////////////////////////
        Route::get('income-expenses/datatable', 'datatable')->name('income-expenses.datatable');    //////////////////////////
    });
    Route::middleware('auth')->group(function() {
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');

        // Route::post('dashboard/edit_news', [DashboardController::class, 'edit_news'])->name('dashboard.edit_news');    //////////////////////////
        // Route::controller(DashboardController::class)->group(function() {                    //////////////////////////
        //     Route::get('dashboard', 'index')->name('dashboard');    //////////////////////////
        //     Route::get('change_password', 'change_password_form')->name('change_password_form');    //////////////////////////
        // });

        // Route::get('profile',  [UserController::class, 'profile'])->name('profile');    //////////////////////////
        // Route::get('profile/{id}',  [UserController::class, 'profile'])->name('profile.id');    //////////////////////////
        // // Route::get('profile',  [UserController::class, 'profile'])->name('profile');    //////////////////////////

        // Route::get('user/datatable', [UserController::class, 'datatable'])->name('user.datatable');    //////////////////////////
        // Route::resource('user', UserController::class);                         //////////////////////////
        // Route::controller(UserController::class)->group(function() {
        //     Route::post('edit_news/{id}', 'edit_news')->name('edit_news');    //////////////////////////
        //     Route::post('edit_news', 'edit_news')->name('edit_news');    //////////////////////////               //////////////////////////
        //     Route::post('change_password', 'change_password')->name('change_password');    //////////////////////////
        //     Route::get('check_password', 'check_password')->name('check_password');    //////////////////////////
        //     Route::post('user/import_excel_user', 'import_excel_user')->name('user.import_excel_user');    //////////////////////////
        //     Route::post('user/{id}', 'update')->name('user.update');    //////////////////////////
        //     Route::get('user-page', 'index')->name('user');    //////////////////////////
        // });

        Route::controller(ExportExcelController::class)->group(function() {                    //////////////////////////
            Route::get('export-excel/import-excel-user', 'user_detail')->name('export-excel.import_excel_user');    //////////////////////////
            Route::get('export-excel/import-excel-user-leave', 'user_leave')->name('export-excel.import_excel_user_leave');    //////////////////////////
            Route::get('export-excel-page', 'index')->name('export-excel');    //////////////////////////
        });

        Route::get('user-time/change_input_date_from_to', [UserTimeController::class, 'change_input_date_from_to'])->name('user-time.change_input_date_from_to');    //////////////////////////
        Route::get('user-time/datatable', [UserTimeController::class, 'datatable'])->name('user-time.datatable');    //////////////////////////
        Route::get('user-time/{id}/datatable_by_id', [UserTimeController::class, 'datatable_by_id'])->name('user-time.datatable_by_id');    //////////////////////////
        Route::get('user-time/{id}',  [UserTimeController::class, 'detail'])->name('user-time.detail');    //////////////////////////
        Route::resource('user-time', UserTimeController::class);                         //////////////////////////
        Route::controller(UserTimeController::class)->group(function() {                    //////////////////////////
            Route::post('user-time/import_excel_user', 'import_excel_user')->name('user-time.import_excel_user');    //////////////////////////
            Route::post('user-time/check_import_excel_user', 'check_import_excel_user')->name('user-time.check_import_excel_user');    //////////////////////////
            Route::post('user-time/{id}', 'update')->name('user-time.update');    //////////////////////////
            Route::get('user-time-page', 'index')->name('user-time');    //////////////////////////
        });

        Route::get('position/datatable', [PositionController::class, 'datatable'])->name('position.datatable');    //////////////////////////
        Route::resource('position', PositionController::class);                         //////////////////////////
        Route::controller(PositionController::class)->group(function() {                    //////////////////////////
            Route::post('position/{id}', 'update')->name('position.update');    //////////////////////////
            Route::get('position-page', 'index')->name('position');    //////////////////////////
        });


        Route::get('work-shift/datatable', [WorkShiftController::class, 'datatable'])->name('work-shift.datatable');    //////////////////////////
        Route::resource('work-shift', WorkShiftController::class);                         //////////////////////////
        Route::controller(WorkShiftController::class)->group(function() {                    //////////////////////////
            Route::post('work-shift/{id}', 'update')->name('work-shift.update');    //////////////////////////
            Route::get('work-shift-page', 'index')->name('work-shift');    //////////////////////////
        });

        Route::get('welfare/datatable', [WelfareController::class, 'datatable'])->name('welfare.datatable');    //////////////////////////
        Route::resource('welfare', WelfareController::class);                         //////////////////////////
        Route::controller(WelfareController::class)->group(function() {                    //////////////////////////
            Route::post('welfare/{id}', 'update')->name('welfare.update');    //////////////////////////
            Route::get('welfare-page', 'index')->name('welfare');    //////////////////////////
        });

        Route::get('annual-holiday/datatable', [AnnualHolidayController::class, 'datatable'])->name('annual-holiday.datatable');    //////////////////////////
        Route::resource('annual-holiday', AnnualHolidayController::class);                         //////////////////////////
        Route::controller(AnnualHolidayController::class)->group(function() {                    //////////////////////////
            Route::post('annual-holiday/{id}', 'update')->name('annual-holiday.update');    //////////////////////////
            Route::get('annual-holiday-page', 'index')->name('annual-holiday');    //////////////////////////
        });

        // Route::get('user-setting/datatable', [UserSettingController::class, 'datatable'])->name('user-setting.datatable');    //////////////////////////
        Route::controller(UserSettingController::class)->group(function() {                    //////////////////////////
            // Route::get('user-setting/work_shift', 'work_shift')->name('user-setting.work_shift');    //////////////////////////
            Route::post('user-setting/work_shift', 'work_shifts')->name('user-setting.work_shifts');    //////////////////////////
            Route::get('user-setting-page', 'index')->name('user-setting');    //////////////////////////
        });

        Route::get('leave/datatable', [LeaveController::class, 'datatable'])->name('leave.datatable');    //////////////////////////
        Route::resource('leave', LeaveController::class);                         //////////////////////////
        Route::controller(LeaveController::class)->group(function() {      //////////////////////////
            Route::get('leave-page', 'index')->name('leave');    //////////////////////////
        });

        Route::get('check-employee-leave/datatable', [LeaveController::class, 'datatable'])->name('check-employee-leave.datatable');    //////////////////////////
        // Route::resource('check-employee-leave', LeaveController::class);                         //////////////////////////
        Route::controller(LeaveController::class)->group(function() {                    //////////////////////////
            Route::post('check-employee-leave/change_status/{id}', 'change_status')->name('check-employee-leave.change_status');    //////////////////////////
            Route::post('check-employee-leave/change_boss_status/{id}', 'change_boss_status')->name('check-employee-leave.change_boss_status');    //////////////////////////
            Route::get('check-employee-leave-page', 'check_leave_index')->name('check-employee-leave');    //////////////////////////
        });

    });
});
/////////////// Ajax ////////////////
Route::get('change_date_format/{date}', [UserController::class, 'ChangeDateFormat'])->name('change_date_format');    //////////////////////////

/////////////// Ajax ////////////////

Route::get('check', [PositionController::class, 'check'])->name('check');    //////////////////////////





///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////









    Route::controller(PageController::class)->group(function() {
        Route::get('dashboard2-overview-1-page', 'dashboardOverview1')->name('dashboard2-overview-1');
        Route::get('dashboard2-overview-2-page', 'dashboardOverview2')->name('dashboard2-overview-2');
        Route::get('dashboard2-overview-3-page', 'dashboardOverview3')->name('dashboard2-overview-3');
        Route::get('dashboard2-overview-4-page', 'dashboardOverview4')->name('dashboard2-overview-4');
        Route::get('inbox-page', 'inbox')->name('inbox');
        Route::get('file-manager-page', 'fileManager')->name('file-manager');
        Route::get('point-of-sale-page', 'pointOfSale')->name('point-of-sale');
        Route::get('chat-page', 'chat')->name('chat');
        Route::get('post-page', 'post')->name('post');
        Route::get('calendar-page', 'calendar')->name('calendar');
        Route::get('crud-data-list-page', 'crudDataList')->name('crud-data-list');
        Route::get('crud-form-page', 'crudForm')->name('crud-form');
        // Route::get('users-layout-1-page', 'usersLayout1')->name('users-layout-1');
        // Route::get('users-layout-2-page', 'usersLayout2')->name('users-layout-2');
        // Route::get('users-layout-3-page', 'usersLayout3')->name('users-layout-3');
        Route::get('profile-overview-1-page', 'profileOverview1')->name('profile-overview-1');
        Route::get('profile-overview-2-page', 'profileOverview2')->name('profile-overview-2');
        Route::get('profile-overview-3-page', 'profileOverview3')->name('profile-overview-3');
        Route::get('wizard-layout-1-page', 'wizardLayout1')->name('wizard-layout-1');
        Route::get('wizard-layout-2-page', 'wizardLayout2')->name('wizard-layout-2');
        Route::get('wizard-layout-3-page', 'wizardLayout3')->name('wizard-layout-3');
        Route::get('blog-layout-1-page', 'blogLayout1')->name('blog-layout-1');
        Route::get('blog-layout-2-page', 'blogLayout2')->name('blog-layout-2');
        Route::get('blog-layout-3-page', 'blogLayout3')->name('blog-layout-3');
        Route::get('pricing-layout-1-page', 'pricingLayout1')->name('pricing-layout-1');
        Route::get('pricing-layout-2-page', 'pricingLayout2')->name('pricing-layout-2');
        Route::get('invoice-layout-1-page', 'invoiceLayout1')->name('invoice-layout-1');
        Route::get('invoice-layout-2-page', 'invoiceLayout2')->name('invoice-layout-2');
        Route::get('faq-layout-1-page', 'faqLayout1')->name('faq-layout-1');
        Route::get('faq-layout-2-page', 'faqLayout2')->name('faq-layout-2');
        Route::get('faq-layout-3-page', 'faqLayout3')->name('faq-layout-3');
        Route::get('login-page', 'login')->name('login');
        Route::get('register-page', 'register')->name('register');
        Route::get('error-page-page', 'errorPage')->name('error-page');
        Route::get('update-profile-page', 'updateProfile')->name('update-profile');
        Route::get('change-password-page', 'changePassword')->name('change-password');
        Route::get('regular-table-page', 'regularTable')->name('regular-table');
        Route::get('tabulator-page', 'tabulator')->name('tabulator');
        Route::get('modal-page', 'modal')->name('modal');
        Route::get('slide-over-page', 'slideOver')->name('slide-over');
        Route::get('notification-page', 'notification')->name('notification');
        Route::get('tab-page', 'tab')->name('tab');
        Route::get('accordion-page', 'accordion')->name('accordion');
        Route::get('button-page', 'button')->name('button');
        Route::get('alert-page', 'alert')->name('alert');
        Route::get('progress-bar-page', 'progressBar')->name('progress-bar');
        Route::get('tooltip-page', 'tooltip')->name('tooltip');
        Route::get('dropdown-page', 'dropdown')->name('dropdown');
        Route::get('typography-page', 'typography')->name('typography');
        Route::get('icon-page', 'icon')->name('icon');
        Route::get('loading-icon-page', 'loadingIcon')->name('loading-icon');
        Route::get('regular-form-page', 'regularForm')->name('regular-form');
        Route::get('datepicker-page', 'datepicker')->name('datepicker');
        Route::get('tom-select-page', 'tomSelect')->name('tom-select');
        Route::get('file-upload-page', 'fileUpload')->name('file-upload');
        Route::get('wysiwyg-editor-classic', 'wysiwygEditorClassic')->name('wysiwyg-editor-classic');
        Route::get('wysiwyg-editor-inline', 'wysiwygEditorInline')->name('wysiwyg-editor-inline');
        Route::get('wysiwyg-editor-balloon', 'wysiwygEditorBalloon')->name('wysiwyg-editor-balloon');
        Route::get('wysiwyg-editor-balloon-block', 'wysiwygEditorBalloonBlock')->name('wysiwyg-editor-balloon-block');
        Route::get('wysiwyg-editor-document', 'wysiwygEditorDocument')->name('wysiwyg-editor-document');
        Route::get('validation-page', 'validation')->name('validation');
        Route::get('chart-page', 'chart')->name('chart');
        Route::get('slider-page', 'slider')->name('slider');
        Route::get('image-zoom-page', 'imageZoom')->name('image-zoom');
});
