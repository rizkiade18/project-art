<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use RealRashid\SweetAlert\Facades\Alert as FacadesAlert;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
    	$user = User::where('id', Auth::user()->id)->first();

    	return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'password' => 'confirmed'
        ]);
    	$user = User::where('id', Auth::user()->id)->first();
        $user->name = $request->name;
        $user->nik = $request->nik;
        $user->kota = $request->kota;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->alamat = $request->alamat;
        $user->nohp = $request->nohp;
    	$user->email = $request->email;
    	
    	
        if(!empty($request->password))
    	{
    		$user->password = Hash::make($request->password);
    	}
        $user->update();

    	Alert::success('User Sukses diupdate', 'Success');
    	return redirect('profile');
    }
}
