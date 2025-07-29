<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderHasProduct;
use App\Models\Room;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        // if(!is_null($id)){
            session(["branch_id" => $id]);
        //     return redirect('dashboard');
        // }
        $data['user'] = User::where('ref_status_id', 1)->get();
        $data['page_url'] = 'home';
        // $data['summary'] = $this->summary(session("branch_id"));

        return view('home/index', $data);
    }
    public function service($branch = null, $id = null)
    {
        $data['page_url'] = $branch.'/service';
        $data['rooms'] = Room::get();
        $data['products'] = Product::get();
        $data['user'] = User::find($id);
        $data['id'] = $id;
        // $data['summary'] = $this->summary(session("branch_id"));

        return view('home/service', $data);
    }
    public function service_more($branch = null, $order_id = null)
    {
        $data['page_url'] = $branch.'/service';
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
        $user = User::where('user_code',$id)->first();

        return $user->name;
    }
    public function insert(Request $request, $branch = null, $id = null)
    {
        try{
            // return $request;
            $ref_seller_id = User::where('user_code', $request->ref_seller_id)->first()->id;
            $user = User::find($id);
            $room = Room::find($request->ref_room_id);
            $price = $this->calculate_all($request);
            if($request->service == 'sixty_minutes'){
                $service = '60 นาที';
            }else{
                $service = '90 นาที';
            }

            $customer_find = Customer::where('name', $request->customer_name)->first();


            $order_number = 1;
            $latestOrder = Order::latest()->first();
            if(@$latestOrder){
                $order_number = $latestOrder->order_number+1;
            }
            $order = new Order;
            $order->order_number = 1;
            $order->ref_branch_id = 1;
            
            if(@$customer_find){
                $order->ref_customer_id = $customer_find->id;
                
            }else{
                
                $customer = new Customer;
                $customer->name = $request->customer_name;
                $customer->ref_branch_id = 1;
                $customer->save();

                $order->ref_customer_id = $customer->id;

            }

            $order->ref_user_id = $id;
            $order->ref_seller_id = $ref_seller_id;
            $order->ref_room_id = $request->ref_room_id;
            $order->service_laundry_cost = $request->service;
            $order->ref_status_id = 2;
            $order->save();
            $td = '';
            if(@$request->ref_product_id){
                foreach($request->ref_product_id as $product){

                    $pro = Product::find($product);
                    
                    $td .= "+$pro->name(".$request->product_qty[$product].')';

                    $order_product = new OrderHasProduct;
                    $order_product->ref_order_id = $order->id;
                    $order_product->ref_product_id = $product;
                    $order_product->price = $pro->price;
                    $order_product->quantity = $request->product_qty[$product];
                    $order_product->save();
                }
            }
        
            DB::commit();
            $qr = QrCode::size(230)->generate(url("/addict-one/service-more/$order->id"));

            $slip = "<!DOCTYPE html>
            <html lang='th'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>ใบแจ้งหนี้ชั่วคราว</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 11px; }
                    .invoice { width: 69mm; font-size: 11px; }
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
                    <p><strong>เปิดห้อง:</strong> ".date('d/m/Y H:i')."</p>
                    <p><strong>เช็คบิล:</strong> ".date('d/m/Y H:i:s')."</p>
                    <table>
                        <tr>
                            <th>จำนวน</th>
                            <th>รายการสินค้า</th>
                            <th>@ ราคา</th>
                            <th>รวม</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>$user->nickname + $service $room->name $td </td>
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
    public function calculate_all(Request $request)
    {
        $price = 0;
        $data['rooms'] = Room::get();
        if(@$request->ref_product_id){
            foreach($request->ref_product_id as $product){
                if(@$request->product_qty[$product]){
                    $price += Product::find($product)->price*$request->product_qty[$product];
                }
                
            }
        }
        if(@$request->ref_room_id && $request->service){
            $room = Room::find($request->ref_room_id);
            if($request->service == 'sixty_minutes'){
                $price += $room->sixty_minutes;
            }else{
                $price += $room->ninety_minutes;
            }
        }

        return number_format($price);
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
        $results = RentBill::orderBy('id','DESC')
                                ->join('room_for_rents', 'rent_bills.ref_room_for_rent_id', '=', 'room_for_rents.id')
                                ->join('renters', 'room_for_rents.ref_renter_id', '=', 'renters.id')
                                ->join('rooms', 'room_for_rents.ref_room_id', '=', 'rooms.id')
                                ->Where('rent_bills.ref_status_id', 3)
                                ->select('rent_bills.*', 'renters.prefix' , DB::raw('CONCAT(renters.name, " ", COALESCE(renters.surname, "")) as renter_name'), 'rooms.name as room_name', 'rooms.rent');
        
        if(@$request->search){
            $results = $results->Where(function ($query) use ($request) {
                                    $query->whereRaw("CONCAT(renters.prefix ,' ' , renters.name, ' ', renters.surname) LIKE ?", ["%{$request->search}%"])
                                        ->orWhere('rooms.name','LIKE','%'.$request->search.'%');
                                });
        }

        $limit = 15;
        if(@$request['limit']){
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
}
