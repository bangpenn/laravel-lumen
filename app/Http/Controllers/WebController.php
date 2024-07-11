<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrasiEmail;

class WebController extends Controller
{

    public function save_registrasi(Request $request)
    {
        $this->validate($request, [
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required',
            'username'=>'required',
            'password'=>'required|confirmed',
            'password_confirmation'=>'required',
        ]);

        $model = new User;
        $model->first_name = $request->input('first_name');
        $model->last_name = $request->input('last_name');
        $model->username = $request->input('username');
        $model->email = $request->input('email');
        $model->password = Hash::make($request->input('password'));
        $model->token = Str::random(40) . $request->input('email');
        $model->active = 'N';

        $save = $model->save();

        if($save)
        {
            $receiver = $model->email;
            $full_name = $model->first_name.' '.$model->last_name;
            $link_activation = env('FE_URL').'/activation/'.$model->token;

            Mail::to($receiver)->send(new RegistrasiEmail($full_name, $link_activation));
            
            $data = array( 
                'success'=>true,
                'message'=>'Registration successfull' 
            );
        }else{
            $data = array( 
                'success'=>false,
                'message'=>'Registration failed'
            );
        }

        return $data;
    }

    public function verifikasi_email(Request $request)
    {
        $id = $request->input('id');

        $user = User::where('token', $id)
            ->where('active','N')
            ->first();

        if($user)
        {
            $ac = \App\Models\User::find($user->id);
            $ac->active = 'Y';
            $ac->email_verified_at = date('Y-m-d H:i:s');
            $ac->token = Str::random(40) . $user->email;
            $ac->save();

            $data = array( 
                'success'=>true,
                'message'=>'Your Account has been Activated, <br> Now you can Login using your <strong>Username / Email</strong>',
            );
        }else{
            $data = array( 
                'success'=>false,
                'message'=>'Account not found'
            );
        }

        return $data;
    }
}