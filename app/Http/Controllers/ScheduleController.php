<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Status;
use App\Models\Schedule;
use App\Models\Schedule_history;
use App\Models\Observation;
use App\Models\Observation_category;
use App\Models\Follow_up;
use App\Models\Setting;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Jenssegers\Date\Date;
use App\Jobs\JobNotification;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request) {
        $status = Status::get();
        $lecturer = User::select('id','name')->whereHas('roles', function($q){
                        $q->where('role_id', "LE");
                    })->where('username','!=', 'admin')->orderBy('name')->get();
        $auditor = User::select('id','email','name')->whereHas('roles', function($q){
                        $q->where('role_id', "AU");
                    })->where('username','!=', 'admin')->orderBy('name')->get();
        return view('schedules.index', compact('status','lecturer','auditor'));
    }

    public function delete(Request $request) {
        $data = Schedule::find($request->id);
        if($data){
            Log::warning(Auth::user()->username." deleted Schedule #".$data->id.", created by : ".$data->created_by);
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete!'
            ]);
        }
    }

    public function add(Request $request) {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'lecturer_id'=> ['required'],
                'study_program'=> ['required'],
                'date_start' => ['required', 'date'],
                'date_end' => ['required', 'date'],
            ]);
           
            $schedule = Schedule::create([
                'lecturer_id' => $request->lecturer_id,
                'study_program' => $request->study_program,
                'date_start' => date('Y-m-d H:i', strtotime($request->date_start)),
                'date_end' => date('Y-m-d H:i', strtotime($request->date_end)),
                'status_id' => "S00",
                'created_by' => Auth::user()->id
            ]);
            
            //TODO : SEND EMAIL TO LECTURER
            $auditee = User::find($request->lecturer_id);
            if($auditee->email != null || $auditee->email != ""){
                $d['email'] = $auditee->email;
                $d['subject'] = "Pemberitahuan Peer-Observation";
                $d['name'] = $auditee->name_with_title;
                $d['messages'] = "Anda mendapatkan jadwal <i><a href='".url('/dashboard')."'>Peer-Observation</a> </i>yang dilaksanakan oleh LPM JGU sebagaimana yang tertera dalam tabel berikut:";
                $d['study_program'] = $request->study_program;
                $d['auditee'] = $auditee->name_with_title;
                $d['auditee_hp'] = $auditee->phone;
                $d['auditee_email'] = $auditee->email;
                $d['start'] = Date::createFromDate($request->date_start)->format('l, j F Y (H:i)');
                $d['end'] = Date::createFromDate($request->date_end)->format('l, j F Y (H:i)');
    
                dispatch(new JobNotification($d)); //send Email using queue job
            }
            //--------------------end email--------------
            return redirect()->route('schedules.edit', Crypt::encrypt($schedule->id));
        } else {
            $lecturer = User::select('id','email','name')->whereHas('roles', function($q){
                            $q->where('role_id', "LE");
                        })->where('username','!=', 'admin')->orderBy('name')->get();
            $study_program = User::select('study_program')->groupBy('study_program')->get();
            return view('schedules.add', compact('lecturer','study_program'));
        }
    }

    public function edit($idd, Request $request) {
        $id = Crypt::decrypt($idd);
          if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'date_start' => ['required', 'date'],
                'date_end' => ['required', 'date'],
                'reschedule_reason' => ['required'],
            ]);
            $data = Schedule::find($id);
            $data->update([ 
                'status_id'=> ($data->status_id == 'S02' ? $data->status_id : 'S01'),
                'date_start'=> $request->date_start,
                'date_end'=> $request->date_end
            ]);
            if($data){
                $x = Schedule_history::insert([
                    'schedule_id' => $id,
                    'description' => "The observation schedule has been <u>rescheduled</u> by <b>".Auth::user()->name."</b>.",
                    'remark' => $request->reschedule_reason,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now(),
                ]);
                //TODO : SEND EMAIL RESCHEDULE TO AUDITEE & AUDITOR
                $schedule = Schedule::with('lecturer')
                    ->with('observations')
                    ->with('observations.auditor')
                    ->find($id);
                    if($schedule->lecturer->email != null || $schedule->lecturer->email != ""){
                        $d['email'] = $schedule->lecturer->email;
                        $d['subject'] = "Perubahan Jadwal Peer-Observation";
                        $d['name'] = $schedule->lecturer->name_with_title;
                        $d['messages'] = "Diinformasikan bahwa terdapat <b>perubahan jadwal</b> Peer-Observation dari jadwal yang sebelumnya menjadi berikut ini:";
                        $d['study_program'] = $schedule->study_program;
                        $d['auditee'] = $schedule->lecturer->name_with_title;
                        $d['auditee_hp'] = $schedule->lecturer->phone;
                        $d['auditee_email'] = $schedule->lecturer->email;
                        $d['start'] = Date::createFromDate($request->date_start)->format('l, j F Y (H:i)');
                        $d['end'] = Date::createFromDate($request->date_end)->format('l, j F Y (H:i)');
                        dispatch(new JobNotification($d)); //send Email using queue job
                    }
                if($schedule->observations != null){
                    foreach($schedule->observations as $o){
                        if($o->auditor->email != null || $o->auditor->email != "" || $o->attendance != 1){
                            $d['email'] = $o->auditor->email;
                            $d['subject'] = "Perubahan Jadwal Peer-Observation";
                            $d['name'] = $o->auditor->name_with_title;
                            $d['messages'] = "Diinformasikan bahwa terdapat <b>perubahan jadwal</b> Peer-Observation dari jadwal yang sebelumnya menjadi berikut ini:";
                            $d['study_program'] = $schedule->study_program;
                            $d['auditee'] = $schedule->lecturer->name_with_title;
                            $d['auditee_hp'] = $schedule->lecturer->phone;
                            $d['auditee_email'] = $schedule->lecturer->email;
                            $d['start'] = Date::createFromDate($request->date_start)->format('l, j F Y (H:i)');
                            $d['end'] = Date::createFromDate($request->date_end)->format('l, j F Y (H:i)');
                            dispatch(new JobNotification($d)); //send Email using queue job
                        }
                    }
                }
                //--------------------end email--------------
            }
            return redirect()->route('schedules.edit', Crypt::encrypt($id));
        }
        $data = Schedule::with('lecturer')->with('status')->with('follow_ups')
                        ->with('created_user')->with('histories')->findOrFail($id);
        $auditors = User::select('id','email','name')->whereHas('roles', function($q){
                        $q->where('role_id', "AU");
                    })->where('username','!=', 'admin')->where('id','!=', $data->lecturer_id)->get();
        return view('schedules.edit', compact('data','auditors'));
    }

    public function review_observations($id, Request $request){
        try {
            $s_id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return redirect()->route('schedules');
        }
        if ($request->isMethod('POST')) {
            if($request->action == "followup"){
                $data = Schedule::findOrFail($s_id)
                ->update([ 
                    'status_id'=> 'S04',
                    'remark'=> $request->remark,
                ]);
                if($data){
                    $followup = Follow_up::create([
                        'schedule_id' => $s_id,
                        'dean_id' => $request->dean_id,
                        'date_start' => date('Y-m-d H:i', strtotime($request->date_start)),
                        'date_end' => date('Y-m-d H:i', strtotime($request->date_end)),
                        'created_by' => Auth::user()->id
                    ]);
                    $x = Schedule_history::insert([
                        'schedule_id' => $s_id,
                        'description' => "<b>".Auth::user()->name."</b> changed the observation status to <u>follow-up</u>.",
                        'remark' => $request->remark,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now(),
                    ]);
                    //TODO : SEND EMAIL TO DEAN folow up
                    $schedule = Schedule::with('lecturer')->find($s_id);
                    $dean = User::find($request->dean_id);
                    if($dean->email != null || $dean->email != ""){
                        $d['email'] = $dean->email;
                        $d['subject'] = "Tindak Lanjut Peer-Observation";
                        $d['name'] = $dean->name_with_title;
                        $d['messages'] = "Anda dijadwalkan untuk melakukan <b>tindak lanjut</b> <i><a href='".url('/dashboard')."'>Peer-Observation</a></i> kepada auditee berikut ini, dimohon agar segera menghubungi auditee dan memberikan laporan melalui sistem ini sesuai jadwal yang telah ditentukan.";
                        $d['study_program'] = $schedule->study_program;
                        $d['auditee'] = $schedule->lecturer->name_with_title;
                        $d['auditee_hp'] = $schedule->lecturer->phone;
                        $d['auditee_email'] = $schedule->lecturer->email;
                        $d['start'] = Date::createFromDate($request->date_start)->format('l, j F Y (H:i)');
                        $d['end'] = Date::createFromDate($request->date_end)->format('l, j F Y (H:i)');
                        dispatch(new JobNotification($d)); //send Email using queue job
                    }
                    //--------------------end email--------------

                    return redirect()->route('schedules.review_observations', $id);
                }

            } else if($request->action == "result"){
                $data = Schedule::findOrFail($s_id)
                ->update([ 
                    'status_id'=> 'S05',
                    'remark'=> $request->remark,
                ]);
                if($data){
                    $x = Schedule_history::insert([
                        'schedule_id' => $s_id,
                        'description' => "<b>".Auth::user()->name."</b> has updated observation status to <u>Result and Recommendation</u>.",
                        'remark' => $request->remark,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now(),
                    ]);
                    //TODO : SEND EMAIL TO LECTURER (result)
                    $schedule = Schedule::with('lecturer')->find($s_id);
                    if($schedule->lecturer->email != null || $schedule->lecturer->email != ""){
                        $d['email'] = $schedule->lecturer->email;
                        $d['subject'] = "Hasil Peer-Observation";
                        $d['name'] = $schedule->lecturer->name_with_title;
                        $d['messages'] = "Menginformasikan bahwa, hasil audit <i>Peer-Observation</i> anda sudah dapat dilihat melalui tautan berikut ini <a href='".url('/pdf/report/'.Crypt::encrypt($s_id))."'>lpm.jgu.ac.id/observations/me</a>";
                        dispatch(new JobNotification($d)); //send Email using queue job
                    }
                    //--------------------end email--------------
                    return redirect()->route('schedules.edit', $id);
                }
            }
        } else {
            $data = Schedule::with('lecturer')->with('status')->with('observations')->with('observations.auditor')->findOrFail($s_id);
            $oids = array();
            foreach($data->observations as $idx)
            {
                array_push($oids, $idx->id);
            }
            $survey = Observation_category::with('criteria_category')->with('observation_criterias')->with('observation_criterias.criteria')
            ->whereIn('observation_id',$oids)->orderBy('criteria_category_id')->get()->groupBy('criteria_category_id');
            $dean = User::select('id','email','name','department')->whereHas('roles', function($q){
                $q->where('role_id', "DE");
            })->where('username','!=', 'admin')->where('id','!=', $data->lecturer_id)->get();
            $MINSCORE = Setting::findOrFail('MINSCORE');
            return view('schedules.review_observations', compact('id','data', 'survey', 'dean', 'MINSCORE'));
        }
    }
}
