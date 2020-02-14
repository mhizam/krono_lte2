<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserVerifier;
use DataTables;
use DB;

class UserVerifierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function staffsearch(Request $req)
    {
        //    	        
        $data = [];
        if($req->has('q')){
            $search = $req->q;
            $data = User::select("id","name")
            		->where('name','LIKE',"%$search%")
                    ->get();
        }         
        return response()->json($data);
    }

    public function subordSearch(Request $req)
    {
        //    	        
        $data = [];
        if($req->has('q')){
            $search = $req->q;
            $data = User::select("id","name")
                    ->where('reptto','=',$req->user()->id)
                    ->where('name','LIKE',"%$search%")
                    //->orWhere('id', $req->user()->id)
                    ->get();
            if($data->count() == 0){                
                $data = User::select("id","name")
                ->where('reptto','=',$req->user()->id)
                ->where('id','LIKE',"%$search%")
                ->get();
            }
        }         
        return response()->json($data);
    }
    
    public function staffverifier(Request $req)
    {
        $dataUser = User::where('id', '=', $req->user()->id)->latest('updated_at')->first();
        $dataVerifier = UserVerifier::where('user_id', '=', $req->user()->id)->get();
        $dataSubordinate = UserVerifier::where('verifier_id', '=', $req->user()->id)->get();

        return view('admin.verifier.index')
        //->with('userdata', $dataUser)
        //->with('verifiers', $dataVerifier)
        //->with('subordinates', $dataSubordinate)
        ->with([
            'userdata' => $dataUser,
            'verifiers' => $dataVerifier,
            'subordinates' => $dataSubordinate
        ]);
    }

    public function ajaxAdvSearchSubord(Request $req)
    {          
        //dd($req->dataSearch);
        $empl_name = strtoupper($req->dataSearch[0]['value']);
        $empl_persno = $req->dataSearch[1]['value'];
        $empl_staffno = strtoupper($req->dataSearch[2]['value']);
        $empl_position = $req->dataSearch[3]['value'];
        $empl_compcode = $req->dataSearch[4]['value'];
        $empl_costcenter = $req->dataSearch[5]['value'];
        $empl_personalarea = $req->dataSearch[6]['value'];
        $empl_personalsubarea = $req->dataSearch[7]['value'];
        $empl_subgroup = $req->dataSearch[8]['value'];
        $empl_email = strtoupper($req->dataSearch[9]['value']);
        $empl_mobilenumber = $req->dataSearch[10]['value'];
        $empl_officenumber = $req->dataSearch[11]['value'];

        //check if no condition
        $checkCondition = 0;
        $data = [];
        $data = User::query();
        $data = $data->select("id","name","staff_no","email","company_id","persarea","perssubarea","empgroup","empsgroup");
        if(strlen(trim($empl_name)) > 0){
            $data = $data->orWhere(DB::raw('upper(name)'),'LIKE','%' .$empl_name. '%');
            $checkCondition++;
        }
        if(strlen(trim($empl_persno)) > 0){
            $data = $data->orWhere('id','LIKE','%' .$empl_persno. '%');
            $checkCondition++;
        }
        if(strlen(trim($empl_staffno)) > 0){
            $data = $data->orWhere(DB::raw('upper(staff_no)'),'LIKE','%' .$empl_staffno. '%');
            $checkCondition++;
        }
        if(strlen(trim($empl_email)) > 0){
            $data = $data->orWhere(DB::raw('upper(email)'),'LIKE','%' .$empl_email. '%');
            $checkCondition++;
        }
        if(strlen(trim($empl_compcode)) > 0){
            $data = $data->orWhere('company_id','LIKE','%' .$empl_compcode. '%');
            $checkCondition++;
        }
        if(strlen(trim($empl_personalarea)) > 0){
            $data = $data->orWhere('persarea','LIKE','%' .$empl_personalarea. '%');
            $checkCondition++;
        }
        if(strlen(trim($empl_personalsubarea)) > 0){
            $data = $data->orWhere('perssubarea','LIKE','%' .$empl_personalsubarea. '%');
            $checkCondition++;
        }
        if(strlen(trim($empl_subgroup)) > 0){
            $data = $data->orWhere('empsgroup','LIKE','%' .$empl_subgroup. '%');
            $checkCondition++;
        }
        if($checkCondition == 0){
            $data = $data->Where('id','=',0);
        }
        
        $data = $data->orderBy('name', 'asc');        
        $data = $data->get();
        //$data = $data->toSql();

        return response()->json($data);
    }

    public function advSearchSubord(Request $req)
    {   
        $dataUser = User::where('id', '=', $req->user()->id)
        ->latest('updated_at')
        ->first();
        $verifierGroups = VerifierGroup::where('approver_id', '=', $req->user()->id)
        ->get();

        $empl_name = $req->empl_name;
        $empl_persno = $req->empl_persno;
        $empl_staffno = $req->empl_staffno;
        $empl_position = $req->empl_position;
        $empl_compcode = $req->empl_compcode;
        $empl_costcenter = $req->empl_costcenter;
        $empl_personalarea = $req->empl_personalarea;
        $empl_personalsubarea = $req->empl_personalsubarea;
        $empl_subgroup = $req->empl_subgroup;
        $empl_email = $req->empl_email;
        $empl_mobilenumber = $req->empl_mobilenumber;
        $empl_officenumber = $req->empl_officenumber;

        $resultAdvSearch = User::select("id","name","staff_no",
        "email","company_id","persarea","perssubarea",
        "empgroup","empsgroup")
        ->orWhere('name','LIKE',"%$empl_name%")
        ->orWhere('id','LIKE',"%$empl_persno%")
        ->orWhere('staff_no','LIKE',"%$empl_staffno%")
        ->orWhere('email','LIKE',"%$empl_email%")
        ->orWhere('company_id','LIKE',"%$empl_compcode%")
        ->orWhere('persarea','LIKE',"%$empl_personalarea%")
        ->orWhere('perssubarea','LIKE',"%$empl_personalsubarea%")
        ->orWhere('empsgroup','LIKE',"%$empl_subgroup%")
        ->limit(20)
        ->get();
  
        return view('verifier.index')
        ->with([
            'userdata' => $dataUser, 
            'verifierGroups' => $verifierGroups,
            'resultAdvSearch' => $resultAdvSearch
            ]);
    }
}