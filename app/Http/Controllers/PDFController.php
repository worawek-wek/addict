<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeaveController;
use App\Models\User;
use App\Models\Bank;
use App\Models\Building;
use App\Models\Service;
use App\Models\RoomHasService;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Branch;
use App\Models\Apartment;
use App\Models\Province;
use App\Models\District;
use App\Models\Subdistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

DB::beginTransaction();

class PDFController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['buildings'] = Building::where('ref_branch_id', session("branch_id"))->get();
        $data['floors'] = Floor::whereHas('building', function ($query) {
                                            $query->where('ref_branch_id', session("branch_id"));
                                        })->get();
        $data['page_url'] = 'setting/fine';

        return view('pdf/index', $data);
    }
    
    public function receipt()
    {
        $html = view('pdf/receipt')->render();

        // $pdf = new \Mpdf\Mpdf();
        $pdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sarabun'
        ]);
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->WriteHTML($html);
        $pdf->Output();
    }
    
}
