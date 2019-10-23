<?php

namespace App\Http\Controllers\Admin;
use App\Shared\UserHelper;
use App\User;
use App\Role;
use App\Company;
use App\State;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public function showStaff(){
        $staff = User::all();
        return view('admin.staff', ['staffs' => $staff]);
    }

    public function searchStaff(Request $req){
      $input = $req->inputstaff;
      $auth = $req->auth;
      $mgmt = $req->mgmt;
      $staff = [];
      $staff = User::where('staff_no', trim($input))->get();
      if(!empty($input)){
        if(count($staff)==0){
          $staff = User::where('name', 'LIKE', '%' .$input. '%')->orderBy('name', 'ASC')->get();
        }
        if(count($staff)==0){
          $req->session()->flash('feedback',true);
          $req->session()->flash('feedback_text',"No maching records found. Try to search again.");
          $req->session()->flash('feedback_icon',"remove");
          $req->session()->flash('feedback_color',"#D9534F");
        }
      }else{
        $staff = User::all();
      }
      if(!empty($auth)){
        return redirect(route('staff.list.auth',[],false))->with(['staffs'=>$staff]);
      }else if(!empty($mgmt)){
        return redirect(route('staff.list.mgmt',[],false))->with(['staffs'=>$staff]);
      }else{
        return redirect(route('staff.list',[],false))->with(['staffs'=>$staff]);
      }
    }

    public function showRole(Request $req){
      $auth = true;
      $role = Role::all();
      if($req->session()->has('staffs')) {
        $staff = $req->session()->get('staffs');
        return view('admin.staff',[
          'staffs' => $req->session()->get('staffs'),
          'auth' => $auth,
          'roles' => $role,
          'feedback' => $req->session()->get('feedback'),
          'feedback_text' => $req->session()->get('feedback_text'),
          'feedback_icon' => $req->session()->get('feedback_icon'),
          'feedback_color' =>  $req->session()->get('feedback_color')
        ]);
      }else{
        $staff = User::all();
        return view('admin.staff',['staffs' => $staff, 'roles' => $role, 'auth'=>$auth]);
      }
    }

    public function showMgmt(Request $req){
      $mgmt = true;
      $company = Company::all();
      $state = State::all();
      if($req->session()->has('staffs')) {
        $staff = $req->session()->get('staffs');
        return view('admin.staff',[
          'staffs' => $req->session()->get('staffs'),
          'mgmt' => $mgmt,
          'companies' => $company,
          'states' => $state,
          'feedback' => $req->session()->get('feedback'),
          'feedback_text' => $req->session()->get('feedback_text'),
          'feedback_icon' => $req->session()->get('feedback_icon'),
          'feedback_color' =>  $req->session()->get('feedback_color')
        ]);
      }else{
        $staff = User::all();
        return view('admin.staff',['staffs' => $staff, 'companies' => $company, 'states' => $state, 'mgmt'=>$mgmt]);
      }
    }
    
    public function updateRole(Request $req){
      $role = $req->role;
      $update_staff = User::find($req->inputid);
      $update_staff->roles()->sync($role);
      $execute = UserHelper::LogUserAct($req, "User Management", "Update " .$req->inputname. " authorization");
      $feedback = true;
      $feedback_text = "Successfully updated " .$req->inputno. " roles.";
      $feedback_icon = "ok";
      $feedback_color = "#5CB85C";
      $staff = User::all(); 
      return redirect(route('staff.list.auth',[],false))->with([
          'staffs'=>$staff,
          'feedback' => $feedback,
          'feedback_text' => $feedback_text,
          'feedback_icon' => $feedback_icon,
          'feedback_color' => $feedback_color]
      );
  }
    public function updateMgmt(Request $req){
      $role = $req->role;
      $update_staff = User::find($req->inputid);
      $update_staff->company_id = $req->company;
      $update_staff->state_id = $req->state;
      $update_staff->save();
      $execute = UserHelper::LogUserAct($req, "User Management", "Update user " .$req->inputname);
      $feedback = true;
      $feedback_text = "Successfully updated " .$req->inputno. ".";
      $feedback_icon = "ok";
      $feedback_color = "#5CB85C";
      $staff = User::all(); 
      return redirect(route('staff.list.mgmt',[],false))->with([
          'staffs'=>$staff,
          'feedback' => $feedback,
          'feedback_text' => $feedback_text,
          'feedback_icon' => $feedback_icon,
          'feedback_color' => $feedback_color]
      );
  }
}