<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-6">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 mb-5">
                <img src="{{ url('images/logo.png') }}" class="rounded mx-auto d-block" width="700" alt="">
            </div>
            @foreach($barangs as $barang)
            <div class="col-md-4">
                <div class="card">
                  <img src="{{('uploads')}}/{{$barang->foto}}" class="card-img-top" alt="...">
                  <div class="card-body">
                    <h5 class="card-title"><strong>{{ $barang->nama_prt }} </strong></h5>
                    <p class="card-text">
                        {{ $barang->jenis_kelamin}} <br>
                        {{ $barang->umur}} Tahun <br>
                        Rp. {{ number_format ($barang->harga)}} <br>
                        {{ $barang->kategori}} <br>
                    </p>
                    <a href="{{ url('pesan') }}/{{ $barang->id }}" class="btn btn-primary">Lihat detail</a>
                  </div>
                </div>
                </div>
            @endforeach
        </div>
    </div>
    </div>
</x-app-layout>
