<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\User_role;
use App\Models\Criteria_category;
use App\Models\Criteria;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function users(Request $request) {
        $roles = Role::get();
        $department = User::select('department')->groupBy('department')->get();
        return view('settings.users', ['roles'=> $roles, 'department' => $department]);
    }

    public function user_add(Request $request) {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 'name'=> ['required', 'string', 'max:255'],
            'email'=> ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'username'=> ['required', 'string', 'max:255', Rule::unique('users')],
            'nidn'=> ['nullable', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8','same:password_confirmation'],
            'gender'=> ['required'],
            'roles'=> ['required'],
            ]);
            if(isset($request->nidn))
            {
                $this->validate($request,
                    [
                        'nidn' => [ 'max:100', Rule::unique('users')],
                    ]);
            }
            $data=User::insert([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'nidn' => $request->nidn,
                'department' => $request->department,
                'study_program' => $request->study_program,
                'phone' => $request->phone,
                'job' => $request->job,
                'gender' => $request->gender,
                'password'=> Hash::make($request->password),
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now()
            ]);

            $attachvalidator = Validator::make($request->all(),User_role::$role_attach_roles);
            if ($attachvalidator->fails()){
                return redirect()->back()->withErrors($attachvalidator)->withInput();
            }
            $attach = User::where('email',$request->email)->first()->roles()->attach($request->roles);
            return redirect()->route('settings.users');
        }
        $roles=Role::all();
        return view('settings.users_add', compact('roles'));
    }

    public function user_edit($id, Request $request) {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 'name'=> ['required', 'string', 'max:255'],
            'email'=> ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id, 'id')],
            'username'=> ['required', 'string', 'max:255', Rule::unique('users')->ignore($id, 'id')],
            'nidn'=> ['nullable', 'max:255', Rule::unique('users')->ignore($id, 'id')],
            'roles'=> ['required'],
            'gender'=> ['required'],
            ]);
            if(isset($request->nidn))
            {
                $this->validate($request,
                    [
                        'nidn' => [ 'max:100', Rule::unique('users')->ignore($id, 'id')],
                    ]);
            }
            $user = User::find($id)->update(request()->all());
            if(isset($request->email_verified_at))
            {
                $user2 = User::where('id', $id)->update([ 'email_verified_at' => Carbon::now(),
                'updated_at'=> Carbon::now() ]);
            } 
            $detach = User::find($id)->roles()->detach();
            $attach = User::find($id)->roles()->attach($request->roles);
            return redirect()->route('settings.users');
        }
        
        $data = User::find($id);
        if($id == 1 || $data == null){
            abort(403, "Cannot access to restricted page");
        }
        $roles = Role::all();
        return view('settings.users_edit', compact('data','roles'));
    }

    public function user_delete(Request $request) {
        $user = User::find($request->id);
        if($user){
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User failed to delete!'
            ]);
        }
    }

    public function categories(Request $request) {
        return view('settings.categories');
    }

    public function category_add(Request $request) {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'id'=> ['required', 'string', 'max:255', Rule::unique('criteria_categories')],
                'title'=> ['required', 'string', 'max:255'],
            ]);
            Criteria_category::insert(request()->except(['_token']));
            return redirect()->route('settings.categories');
        }
        return view('settings.categories_add');
    }

    public function category_edit($id, Request $request) {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'id'=> ['required', 'string', 'max:255', Rule::unique('criteria_categories')->ignore($id, 'id')],
                'title'=> ['required', 'string', 'max:255'],
            ]);
            $data = Criteria_category::find($id)->update(request()->all());
            return redirect()->route('settings.categories');
        }
        $data = Criteria_category::find($id);
        if($data == null){
            abort(403, "Cannot access to restricted page");
        }
        return view('settings.categories_edit', compact('data'));
    }

    public function category_delete(Request $request) {
        $data = Criteria_category::find($request->id);
        if($data){
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

    public function criterias(Request $request) {
        $categories = Criteria_category::get();
        return view('settings.criterias', compact('categories'));
    }

    public function criteria_add(Request $request) {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'criteria_category_id'=> ['required'],
                'title'=> ['required', 'string', 'max:255'],
                'weight'=> ['required', 'numeric'],
            ]);
            Criteria::insert(request()->except(['_token']));
            return redirect()->route('settings.criterias');
        } 
        $categories = Criteria_category::get();
        return view('settings.criterias_add', compact('categories'));
    }

    public function criteria_edit($id, Request $request) {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'criteria_category_id'=> ['required'],
                'title'=> ['required', 'string', 'max:255'],
                'weight'=> ['required', 'numeric'],
            ]);
            $data = Criteria::find($id)->update(request()->all());
            return redirect()->route('settings.criterias');
        }
        $data = Criteria::find($id);
        if($data == null){
            abort(403, "Cannot access to restricted page");
        }
        $categories = Criteria_category::get();
        return view('settings.criterias_edit', compact('data','categories'));
    }


    public function criteria_delete(Request $request) {
        $data = Criteria::find($request->id);
        if($data){
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
}
