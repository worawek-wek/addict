<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\AddonOption;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderHasProduct;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RoomTypeHasCourse;
use App\Models\Branch;
use App\Models\Course;
use App\Models\OrderHasAddonOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

DB::beginTransaction();

class FrontHomeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = null)
    {
        // return Auth::guard('customer')->user()->id_card;
        // if(!is_null($id)){
        session(["branch_id" => $id]);
        //     return redirect('dashboard');
        // }
        $data['course'] = Course::orderBy('sort')
            ->where('ref_status_id', 1)
            ->where('show_online_booking', 1)
            ->get();

        $data['user'] = User::where('ref_status_id', 1)
            ->where('ref_branch_id', 1)
            ->where('ref_status_id', 1)
            ->get();
        $data['page_url'] = 'home';
        $data['rooms'] = Room::get();
        $data['products'] = Product::get();
        $data['room_type'] = RoomType::where('ref_front_status_id', 1)->get();
        $branch_id = $id ?? session('branch_id');
        if ($branch_id) {
            $data['option'] = AddonOption::where('branch', $branch_id)->orderBy('price', 'asc')->get();
        } else {
            $data['option'] = collect();
        }
        $data['branches'] = Branch::all();

        return view('frontend.home', $data);

        // return view('home/index', $data);
    }
    public function service($branch = null, $id = null)
    {
        $data['page_url'] = $branch . '/service';
        $data['rooms'] = Room::get();
        $data['products'] = Product::get();
        $data['user'] = User::find($id);
        $data['id'] = $id;
        // $data['summary'] = $this->summary(session("branch_id"));

        return view('home/service', $data);
    }
    public function service_more($branch = null, $order_id = null)
    {
        $data['page_url'] = $branch . '/service';
        $data['rooms'] = Room::get();
        $data['products'] = Product::get();
        $order = Order::find($order_id);
        $data['customer'] = Customer::find($order->ref_customer_id);
        $data['mama'] = User::find($order->ref_seller_id);
        $data['id'] = $order_id;
        // $data['summary'] = $this->summary(session("branch_id"));

        return view('home/service-more', $data);
    }
    public function get_name_mama($id = null)
    {
        $user = User::where('user_code', $id)->first();

        return $user->name;
    }
    public function insert(Request $request, $branch = null, $id = null)
    {
        $id = $request->selected_user;
        try {
            $ref_seller_id = Auth::guard('customer')->user()->id;
            $user = User::find($request->selected_user);
            $room = RoomType::find($request->roomType);
            $course = Course::where('ref_status_id', 1)
                ->where('show_online_booking', 1)
                ->find($request->timeService);
            if (!$course) {
                DB::rollBack();
                return back()->with('error', 'ไม่พบ Time Period ที่เปิดให้จองออนไลน์');
            }
            // $massage_price = $user ? $user->salary : 0;
            $room_price = 0;
            // if (!empty($request->roomType) && $request->timeService) {
            //     switch ($request->timeService) {
            //         case 'forty_minutes':
            //             $room_price = $room ? $room->forty_minutes : 0;
            //             break;
            //         case 'sixty_minutes':
            //             $room_price = $room ? $room->sixty_minutes : 0;
            //             break;
            //         case 'ninety_minutes':
            //             $room_price = $room ? $room->ninety_minutes : 0;
            //             break;
            //     }
            // }
            $price = $this->calculate_all($request);
            $order_price = $price;

            // // กำหนดค่าระยะเวลาเป็นตัวเลข
            // if ($request->timeService == 'forty_minutes') {
            //     $service = '40';
            // } elseif ($request->timeService == 'sixty_minutes') {
            //     $service = '60';
            // } elseif ($request->timeService == 'ninety_minutes') {
            //     $service = '90';
            // } else {
            //     $service = null;
            // }

            $customer_find = Auth::guard('customer')->user();

            // Generate order_number: ONLINE + 7 random digits, must be unique
            do {
                $order_number = 'ONLINE' . str_pad(strval(rand(0, 9999999)), 7, '0', STR_PAD_LEFT);
            } while (Order::where('order_number', $order_number)->exists());
            $order = new Order;
            $order->ref_account_id = Auth::id();
            $order->order_number = $order_number;
            $order->ref_branch_id = $request->ref_branch_id;

            if (@$customer_find) {
                $order->ref_customer_id = $customer_find->id;
            } else {
                $customer = new Customer;
                $customer->name = $request->customer_name;
                $customer->ref_branch_id = 1;
                $customer->save();
                $order->ref_customer_id = $customer->id;
            }
            $order->ref_room_type_id = $request->roomType;
            $order->ref_user_id = $request->selected_user;
            $order->ref_room_id = $request->roomType;
            $order->service_laundry_cost = $request->timeService;
            $order->ref_status_id = 1;
            $order->booking_date = $request->booking_date;
            $order->start_time = $request->booking_time;
            $order->total_price = number_format($order_price, 2, '.', '');
            $order->price = number_format($order_price, 2, '.', '');
            $start = \Carbon\Carbon::createFromFormat('H:i', $request->booking_time);

            // switch ($request->timeService) {
            //     case 'forty_minutes':
                $course_name = $course->name;

                preg_match('/\d+/', $course_name, $matches);

                $minute = $matches[0] ?? 0;
                $end = $start->copy()->addMinutes($minute);
            //         break;
            //     case 'sixty_minutes':
            //         $end = $start->copy()->addMinutes(60);
            //         break;
            //     case 'ninety_minutes':
            //         $end = $start->copy()->addMinutes(90);
            //         break;
            //     default:
            //         $end = $start;
            // }

            $order->end_time = $end->format('H:i');

            $order->save();
            $td = '';
            if (@$request->ref_product_id) {
                foreach ($request->ref_product_id as $product) {
                    $pro = Product::find($product);
                    $td .= "+$pro->name(" . $request->product_qty[$product] . ')';
                    $order_product = new OrderHasProduct;
                    $order_product->ref_order_id = $order->id;
                    $order_product->ref_product_id = $product;
                    $order_product->price = $pro->price;
                    $order_product->quantity = $request->product_qty[$product];
                    $order_product->save();
                }
            }
            if (@$request->ref_option_id) {
                foreach ($request->ref_option_id as $option_id) {
                    $option = AddonOption::find($option_id);
                    $td .= "+$option->name";
                    $order_option = new OrderHasAddonOption();
                    $order_option->ref_order_id = $order->id;
                    $order_option->ref_option_id = $option_id;
                    $order_option->price = $option->price;
                    $order_option->save();
                }
            }

            DB::commit();
            $qr = QrCode::size(230)->generate(url("admin/order-rooms/$order->id"));

            $slip = "<!DOCTYPE html>
                        <html lang='th'>
                        <head>
                            <meta charset='UTF-8'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                            <title>ใบแจ้งหนี้</title>
                            <style>
                                body { font-family: Arial, sans-serif; font-size: 11px; }
                                .invoice { width: 69mm; font-size: 11px;padding: 20px; }
                                .header { display: flex; justify-content: space-between; align-items: end; font-weight: bold; font-size: 10px; }
                                .title { flex-grow: 1; text-align: center; font-size: 11px; }
                                .right-align { text-align: right; }
                                table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 11px; border-top: 1px solid #000; }
                                th, td { padding: 2px; text-align: left; font-size: 11px; }
                                th { border-bottom: 1px solid #000; }
                                td { border-bottom: 1px solid #000; }

                                @media print {
                                    @page {
                                        size: 69mm auto;
                                        margin: 0;
                                    }

                                    body {
                                        width: 69mm;
                                        margin: 0;
                                    }

                                    .invoice {
                                        width: 69mm;
                                    }
                                }
                            </style>

                        </head>
                        <body>
                            <div class='invoice'>
                                <div class='header' align='right'>
                                    <span class='title'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  ใบแจ้งหนี้ชั่วคราว </span>
                                    <span class='right-align'>No_: $order_number</span>
                                </div>
                                <p class='right-align'><strong>แคชเชียร์:</strong> Addict</p>
                                <p><strong>ห้อง:</strong> $room->name</p>
                            <p><strong>เปิดห้อง:</strong> " . \Carbon\Carbon::parse($order->booking_date . ' ' . $order->start_time)->format('d/m/Y H:i') . "</p>
            <p><strong>เช็คบิล:</strong> " . \Carbon\Carbon::parse($order->booking_date . ' ' . $order->end_time)->format('d/m/Y H:i:s') . "</p>

                                <table>
                                    <tr>
                                        <th>จำนวน</th>
                                        <th>รายการสินค้า</th>
                                        <th>@ ราคา</th>
                                        <th>รวม</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>$user->nickname + $course_name $room->name $td </td>
                                        <td>$price</td>
                                        <td>$price</td>
                                    </tr>
                                </table>
                                <div style='padding: 10px;'>
                                $qr
                                </div>
                            </div>
                        </body>
                    </html>
                    ";
            return $slip;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function calculate_all(Request $request): float
    {
        $price = 0;

        $user = User::find($request->selected_user);
        $price += $user?->salary ?? 0;

        if (!empty($request->ref_product_id)) {
            foreach ($request->ref_product_id as $product) {
                if (!empty($request->product_qty[$product])) {
                    $productModel = Product::find($product);
                    $price += ($productModel?->price ?? 0) * $request->product_qty[$product];
                }
            }
        }

        if (!empty($request->ref_option_id)) {
            foreach ($request->ref_option_id as $optionId) {
                $option = AddonOption::find($optionId);
                if ($option) {
                    $price += $option->price;
                }
            }
        }

        if (!empty($request->roomType) && $request->timeService) {
            $roomCoursePrice = RoomTypeHasCourse::where('ref_room_type_id', $request->roomType)
                ->where('ref_course_id', $request->timeService)
                ->value('price');

            $price += $roomCoursePrice ?? 0;
        }

        return $price; // float
    }

    public function overdue(Request $request)
    {
        $all_overdue_payment = RentBill::where('rent_bills.ref_status_id', 3)
            ->join('room_for_rents', 'rent_bills.ref_room_for_rent_id', '=', 'room_for_rents.id')
            ->join('rooms', 'room_for_rents.ref_room_id', '=', 'rooms.id')
            ->sum(DB::raw('rooms.rent + rent_bills.electricity_amount + rent_bills.water_amount'));

        $data['page_url'] = 'dashboard';
        $data['summary'] = $this->summary(session("branch_id"));
        $data['all_overdue_payment'] = $all_overdue_payment;
        return view('admin/dashboard/overdue', $data);
    }
    public function datatable(Request $request)
    {
        $results = RentBill::orderBy('id', 'DESC')
            ->join('room_for_rents', 'rent_bills.ref_room_for_rent_id', '=', 'room_for_rents.id')
            ->join('renters', 'room_for_rents.ref_renter_id', '=', 'renters.id')
            ->join('rooms', 'room_for_rents.ref_room_id', '=', 'rooms.id')
            ->Where('rent_bills.ref_status_id', 3)
            ->select('rent_bills.*', 'renters.prefix', DB::raw('CONCAT(renters.name, " ", COALESCE(renters.surname, "")) as renter_name'), 'rooms.name as room_name', 'rooms.rent');

        if (@$request->search) {
            $results = $results->Where(function ($query) use ($request) {
                $query->whereRaw("CONCAT(renters.prefix ,' ' , renters.name, ' ', renters.surname) LIKE ?", ["%{$request->search}%"])
                    ->orWhere('rooms.name', 'LIKE', '%' . $request->search . '%');
            });
        }

        $limit = 15;
        if (@$request['limit']) {
            $limit = $request['limit'];
        }
        // $data['prefix'] = [ 1 => 'บริษัท', 2 => 'นาย', 3 => 'นางสาว', 4 => 'นาง'];
        $results = $results->paginate($limit);
        // return $results->items();
        // dd($results);
        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['list_data'] = $results;

        return view('admin/dashboard/table', $data);
    }
    public function change_password_form()
    {
        $user = Auth::user();

        $user->work_start_date_th = $this->ChangeDateToTH($user->work_start_date);
        $user->birthday_th = $this->ChangeDateToTH($user->birthday);

        $data['user'] = $user;
        return view('admin/dashboard/change_password', $data);
    }
    public function invoice($id)
    {
        $data['page_url'] = 'dashboard';
        $invoice = RentBill::find($id);
        $data['invoice'] = $invoice;

        return view('admin/dashboard/invoice', $data);
    }

    public function ChangeDateToTH($date)
    {
        ////////////////////// แปลงรูปแบบวันเกิดเป็น ไทย
        // สร้าง Carbon instance จากวันที่
        $m = date('m', strtotime($date));
        $date = Carbon::createFromFormat('Y-m-d', $date);

        // คำนวณปีพุทธศักราช
        $buddhistYear = $date->year + 543;

        // แปลงวันที่เป็นรูปแบบไทย
        $thaiDate = $date->formatLocalized('%e %B ' . $buddhistYear);

        $monthTH = [
            "01" => "มกราคม",
            "02" => "กุมภาพันธ์",
            "03" => "มีนาคม",
            "04" => "เมษายน",
            "05" => "พฤษภาคม",
            "06" => "มิถุนายน",
            "07" => "กรกฎาคม",
            "08" => "สิงหาคม",
            "09" => "กันยายน",
            "10" => "ตุลาคม",
            "11" => "พฤศจิกายน",
            "12" => "ธันวาคม"
        ];
        $monthEN = [
            "01" => "January",
            "02" => "February",
            "03" => "March",
            "04" => "April",
            "05" => "May",
            "06" => "June",
            "07" => "July",
            "08" => "August",
            "09" => "September",
            "10" => "October",
            "11" => "November",
            "12" => "December"
        ];
        return str_replace($monthEN[$m], $monthTH[$m], $thaiDate);
    }

    public function checkAvailability(Request $request, $branchId)
    {
        $bookingDate = $request->booking_date;
        $startTime   = $request->start_time;
        $endTime     = $request->end_time;

        $users = User::where('ref_status_id', 1)
            ->where('ref_branch_id', $branchId)
            ->where('ref_position_id', 2)
            ->get()
            ->map(function ($user) use ($bookingDate, $startTime, $endTime) {
                $hasConflict = Order::where('ref_user_id', $user->id)
                    ->where('booking_date', $bookingDate)
                    ->where('ref_status_id', '!=', 4) // 👈 ข้าม order ที่ถูกยกเลิก
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                    })
                    ->exists();

                return [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'nickname'  => $user->nickname,
                    'salary'    => $user->salary,
                    'image'     => $user->image_name ?? 'default.png',
                    'available' => !$hasConflict,
                ];
            });

        return response()->json($users);
    }

    public function checkRoomAvailability(Request $request, $branchId)
    {
        $bookingDate = $request->booking_date;
        $startTime   = $request->start_time;
        $endTime     = $request->end_time;

        $rooms = RoomType::where('ref_branch_id', $branchId)->get()
            ->map(function ($room) use ($bookingDate, $startTime, $endTime) {
                $coursePrices = RoomTypeHasCourse::where('ref_room_type_id', $room->id)
                    ->pluck('price', 'ref_course_id');

                $hasConflict = Order::where('ref_room_id', $room->id)
                    ->where('booking_date', $bookingDate)
                    ->where('ref_status_id', '!=', 4) // 👈 ข้าม order ที่ถูกยกเลิก
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                    })
                    ->exists();

                return [
                    'id'        => $room->id,
                    'name'      => $room->name,
                    'forty'     => $room->forty_minutes,
                    'sixty'     => $room->sixty_minutes,
                    'ninety'    => $room->ninety_minutes,
                    'course_prices' => $coursePrices,
                    'available' => !$hasConflict,
                ];
            });

        return response()->json($rooms);
    }
}
