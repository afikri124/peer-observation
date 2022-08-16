<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use QrCode;
use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\Observation;
use App\Models\Follow_up;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Jenssegers\Date\Date;

class PdfController extends Controller
{
    public function report($id)
    {
        try {
            $s_id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return abort(404);
        }
        $data = Schedule::with('lecturer')->with('status')->with('observations')->with('observations.auditor')->findOrFail($s_id);
        $link = route('pdf.report', $id );
        Date::setLocale('id');
        $qrcode = base64_encode(QrCode::format('svg')->size(150)->errorCorrection('H')->generate($link));
        $survey = Observation::with('observation_categories')
        ->with('auditor')
        ->with('observation_categories.criteria_category')
        ->with('observation_categories.observation_criterias')
        ->with('observation_categories.observation_criterias.criteria')
        ->where('schedule_id',$s_id)->get();

        $follow_up = Follow_up::with('dean')->where('schedule_id',$s_id)->first();
        $pdf = PDF::loadview('pdf.report',['data'=>$data, 'qr' => $qrcode, 'survey' => $survey, 'follow_up' => $follow_up]);
	    return $pdf->stream("PO Report - ".$data->lecturer->name." - ".date('d-m-Y', strtotime($data->date_start)).".pdf");
    }
}
