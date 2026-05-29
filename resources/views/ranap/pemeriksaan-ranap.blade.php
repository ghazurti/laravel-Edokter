@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ranap')

@section('content_header')
    <h1>Pemeriksaan Ranap</h1>
@stop

@section('content')
@php
    $dpjpUtama = \Illuminate\Support\Facades\DB::table('reg_periksa')
        ->where('no_rawat', request()->get('no_rawat'))
        ->value('kd_dokter');
    $isDokterKonsul = $dpjpUtama
        && $dpjpUtama !== session('username')
        && \Illuminate\Support\Facades\DB::table('rujukan_internal_poli')
            ->where('no_rawat', request()->get('no_rawat'))
            ->where('kd_dokter', session('username'))
            ->exists();
@endphp
    <x-ranap.riwayat-ranap :no-rawat="request()->get('no_rawat')" />
    <div class="row">
        <div class="col-md-4">
            <x-ranap.pasien :no-rawat="request()->get('no_rawat')" />
        </div>
        <div class="col-md-8">
            @if($isDokterKonsul)
            <div class="alert alert-info py-2 mb-2">
                <i class="fas fa-user-md mr-1"></i>
                <strong>Mode Konsul</strong> &mdash; Anda dokter penerima rujukan internal untuk pasien ini.
            </div>
            <x-adminlte-card title="Jawaban Konsul" theme="info" icon="fas fa-lg fa-comment-medical" collapsible maximizable>
                <livewire:ralan.jawaban-konsul :noRawat="request()->get('no_rawat')" />
            </x-adminlte-card>
            @endif

            <x-ranap.pemeriksaan-ranap :no-rawat="request()->get('no_rawat')" />
            <x-ranap.resep-ranap />
            @unless($isDokterKonsul)
            <x-adminlte-card title="Diagnosa" theme="info" icon="fas fa-lg fa-diagnoses" collapsible="collapsed" maximizable>
                <livewire:ralan.diagnosa :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
            </x-adminlte-card>
            <livewire:ranap.resep-pulang :no-rawat="request()->get('no_rawat')" />
            <livewire:ranap.resume-pasien :no-rawat="request()->get('no_rawat')" />
            @endunless
            <livewire:ranap.catatan-pasien :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
            <livewire:ranap.permintaan-lab :no-rawat="request()->get('no_rawat')" />
            <livewire:ranap.permintaan-radiologi :no-rawat="request()->get('no_rawat')" />
            <x-adminlte-card title="Laporan Operasi" icon='fas fa-stethoscope' theme="info" maximizable collapsible="collapsed">
                <livewire:ranap.lap-operasi :no-rawat="request()->get('no_rawat')" />
                <livewire:ranap.template-lap-operasi />
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
    <script> console.log('Hi!'); </script>
@stop