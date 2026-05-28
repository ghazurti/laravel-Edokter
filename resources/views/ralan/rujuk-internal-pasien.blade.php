@extends('adminlte::page')

@section('title', 'Rujukan Internal')

@section('content_header')
<div class="d-flex flex-row justify-content-between">
    <h1>Rujukan Internal</h1>
    <a class="btn btn-primary" href="{{ url('ralan/pasien') }}" role="button">Daftar Pasien</a>
</div>
@stop

@section('content')
<x-ralan.riwayat :no-rawat="request()->get('no_rawat')" />
<div class="row">
    <div class="col-md-4">
        <x-ralan.pasien :no-rawat="request()->get('no_rawat')" />
    </div>
    <div class="col-md-8">
        <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-bell" collapsible="collapsed" maximizable>
            <livewire:ralan.pemeriksaan :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
            <livewire:ralan.modal.edit-pemeriksaan />
        </x-adminlte-card>

        <x-adminlte-card title="Resep" id="resepCard" theme="info" icon="fas fa-lg fa-pills" collapsible="collapsed" maximizable>
            <x-ralan.resep />
        </x-adminlte-card>

        <livewire:ralan.resume :no-rawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />

        <x-adminlte-card title="Diagnosa" theme="info" icon="fas fa-lg fa-file-medical" collapsible="collapsed" maximizable>
            <livewire:ralan.diagnosa :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
        </x-adminlte-card>

        <livewire:ralan.catatan :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />

        <livewire:ralan.permintaan-lab :no-rawat="request()->get('no_rawat')" />

        <livewire:ralan.permintaan-radiologi :no-rawat="request()->get('no_rawat')" />

        <x-adminlte-card title="Permintaan Konsultasi Medik" theme="info" icon="fas fa-user-md" collapsible="collapsed" maximizable>
            <livewire:ralan.permintaan-konsultasi :noRawat="request()->get('no_rawat')" />
        </x-adminlte-card>

        <x-adminlte-card title="Laporan Operasi" icon="fas fa-stethoscope" theme="info" maximizable collapsible="collapsed">
            <livewire:ranap.lap-operasi :no-rawat="request()->get('no_rawat')" />
            <livewire:ranap.template-lap-operasi />
        </x-adminlte-card>
    </div>
</div>
@stop

@section('plugins.TempusDominusBs4', true)
