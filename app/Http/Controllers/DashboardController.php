<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function dashboard()
    {
        return view('dashboard', [
            'data' => ''
        ]);
    }

    public function my_profile()
    {
        $data = User::where('id', Auth::user()->id)
                ->first();
        return view('user.profile', [
            'data' => $data,
        ]);
    }

    protected function update_profile(Request $data)
    {
        $this->validate($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore(auth()->user()->id, 'id')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(auth()->user()->id, 'id')],
        ]);

        User::where('id', Auth::user()->id)->update([
            'name' => $data->name,
            'username' => $data->username,
            'email' => $data->email,
            'updated_at' => Carbon::now()
        ]);
	    return redirect()->route('my_profile');
    }

    protected function update_password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required', 'string', 'min:8'],
            'new_confirm_password' => ['same:new_password'],
        ]);
   
        User::where('id', Auth::user()->id)->update(['password'=> Hash::make($request->new_password)]);
   
        Auth::logout();
        return redirect('/login');
    }
}
