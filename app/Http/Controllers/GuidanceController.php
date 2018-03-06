<?php

namespace App\Http\Controllers;
use App\diagnosis_lookup_value;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\audio_lookup_value;
use App\video_lookup_value;
use App\image_lookup_value;
use Illuminate\Support\Facades\DB;
use Carbon;
// use Illuminate\Http\Request;
use Auth;
use App\User;
use App\users_patient;
use App\patient;
use App\active_record;
use View;

class GuidanceController extends Controller
{
    public function get_video_list()
    {
        $categories = video_lookup_value::all()->toArray();
        return view('patient/orders',compact('categories'));

    }

public function post_ddx(Request $request)
    {
        $role='';
        if(Auth::check()) {
            $role = Auth::user()->role;
        }

        if($role == 'Student') {

            //Validating input data
            $this->validate($request, [
            ]);

            try {
                $diagnosis = $request['search_diagnosis_ddx'];

                //Saving medications
                foreach ((array)$diagnosis as $key=>$item) {
                    $lab_value = diagnosis_lookup_value::where('diagnosis_lookup_value_id',$item)->pluck('diagnosis_lookup_value');
                    $active_record = new active_record();
                    $active_record['patient_id'] = $request['patient_id'];
                    $active_record['navigation_id'] = '35';
                    $active_record['doc_control_id'] = '82';
                    $active_record['value'] = $lab_value[0];
                    $active_record['created_by'] = $request['user_id'];
                    $active_record['updated_by'] = $request['user_id'];
                    $active_record->save();
                }
            return redirect()->route('Demographics',[$request['patient_id']]);
        }
            catch (Exception $e) {
                return view('errors/503');
            }
        }
        else
        {
            return view('auth/not_authorized');
        }
    }
}