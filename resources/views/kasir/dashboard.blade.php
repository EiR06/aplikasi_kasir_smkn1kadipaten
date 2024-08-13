@extends('layouts.master')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection

@section('content')
<!-- Welcome Box -->
<div class="row">
    <div class="col-lg-12">
        <div class="box" style="border-radius: 15px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); background-color: #ffffff; overflow: hidden;">
            <div class="box-body text-center" style="padding: 100px; position: relative;">
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.05); z-index: -1; border-radius: 15px; transform: scale(1.05);"></div>
                <h1 style="font-size: 3rem; font-weight: 700; color: #333; margin-bottom: 10px; transition: color 0.3s;">Selamat Datang</h1>
                <h2 style="font-size: 2rem; font-weight: 300; color: #666; margin-bottom: 30px;">Anda login sebagai KASIR</h2>

                <a href="{{ route('transaksi.baru') }}" class="btn btn-success btn-lg animated bounceIn" style="border-radius: 50px; padding: 15px 30px; font-size: 1.2rem; box-shadow: 0 6px 12px rgba(0, 128, 0, 0.3); background-color: #28a745; border: none; transition: background-color 0.3s, box-shadow 0.3s;">Transaksi Baru</a>
            </div>
        </div>
    </div>
</div>
<!-- /.row (main row) -->
@endsection

@push('styles')
<style>
    .animated {
        animation-duration: 1s;
        animation-fill-mode: both;
    }
    .bounceIn {
        animation-name: bounceIn;
    }
    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
@endpush
