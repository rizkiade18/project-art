<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Barang;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use App\Models\PesananDetail;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use RealRashid\SweetAlert\Facades\Alert as FacadesAlert;

class PesanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($id)
    {
         $barang = Barang::where('id', $id)->first();
        
        return view('pesan.index', compact('barang'));
    } 

    public function pesan(Request $request, $id)
    {   
        $defaultJumlah=1;
        $barang = Barang::where('id', $id)->first();
        // untuk tanggal
        $tanggal = Carbon::now();

        //validasi apakah melebihi stok
        if($defaultJumlah > $barang->stok)
        {
            return redirect('pesan/'.$id);
        }


        //cek validasi
        $cek_pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
        //simpan ke database pesanan
        if(empty($cek_pesanan))
        {
            // simpan ke database pesanan
            $pesanan = new Pesanan;
            $pesanan ->user_id = Auth::user()->id;
            $pesanan ->tanggal = $tanggal;
            $pesanan->status = 0;
            $pesanan->jumlah_harga = 0;
            $pesanan->kode = mt_rand(100, 999);
            $pesanan->save();
        }

        // simpan ke database pesanan detail
        $pesanan_baru = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();

        //cek pesanan detail
        $cek_pesanan_detail = PesananDetail::where('barang_id', $barang->id)->where('pesanan_id', $pesanan_baru->id)->first();
        if(empty($cek_pesanan_detail))
        {
            $pesanan_detail = new PesananDetail;
            $pesanan_detail->barang_id = $barang->id;
            $pesanan_detail->pesanan_id = $pesanan_baru->id;
            $pesanan_detail->jumlah = $defaultJumlah;
            $pesanan_detail->jumlah_harga = $barang->harga*$defaultJumlah;
            $pesanan_detail->save();
        }else 
        {
            $pesanan_detail = PesananDetail::where('barang_id', $barang->id)->where('pesanan_id', $pesanan_baru->id)->first();

            $pesanan_detail->jumlah = $pesanan_detail->jumlah+$defaultJumlah;

            //harga sekarang
            $harga_pesanan_detail_baru = $barang->harga*$defaultJumlah;
            $pesanan_detail->jumlah_harga = $pesanan_detail->jumlah_harga+$harga_pesanan_detail_baru;
            $pesanan_detail->update();
        }

        //jumlah total
        $pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
        $pesanan->jumlah_harga = $pesanan->jumlah_harga+$barang->harga*$defaultJumlah;
        $pesanan->update();

        Alert::success('Pesanan Sukses Masuk Keranjang', 'Success');
        return redirect('check-out');
    }

    public function check_out()
    {

        
      //  dd(Auth::user()->id);
        $pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
        //dd($pesanan);
        if(!empty($pesanan))
        {
           $pesanan_details = PesananDetail::where('pesanan_id', $pesanan->id)->get();
           return view('pesan.check_out', compact('pesanan', 'pesanan_details'));
        }
        $pesanan_details='';
        return view('pesan.check_out', compact('pesanan', 'pesanan_details'));        
      
       

    }
    
    public function delete($id)
    {
        $pesanan_detail = PesananDetail::where('id', $id)->first();

        $pesanan = Pesanan::where('id', $pesanan_detail->pesanan_id)->first();
        $pesanan->jumlah_harga = $pesanan->jumlah_harga-$pesanan_detail->jumlah_harga;
        $pesanan->update();


        $pesanan_detail->delete();

        Alert::error('Pesanan Sukses Dihapus', 'Hapus');
        return redirect('check-out');
    }

    public function konfirmasi()
    {
        $user = User::where('id', Auth::user()->id)->first();
        if($user->alamat==null || $user->nohp==null){
             Alert::error('Pesanan Sukses Dihapus', 'Hapus');
            return redirect('profile');
        }
        
        $pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
        $pesanan_id = $pesanan->id;
        $pesanan->status = 1;
        $pesanan->update();

        $pesanan_details = PesananDetail::where('pesanan_id', $pesanan_id)->get();
        foreach ($pesanan_details as $pesanan_detail) {
            $barang = Barang::where('id', $pesanan_detail->barang_id)->first();
            $barang->stok = $barang->stok-$pesanan_detail->jumlah;
            $barang->update();
        }



        Alert::success('Pesanan Sukses Check Out Silahkan Lanjutkan Proses Pembayaran', 'Success');
        return redirect('history/'.$pesanan_id);

    }


}