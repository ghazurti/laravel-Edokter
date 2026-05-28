@extends('adminlte::page')

@section('title', 'Pemeriksaan IGD')

@section('content_header')
<div class="d-flex flex-row justify-content-between">
    <h1>Pemeriksaan IGD</h1>
    <a class="btn btn-danger" href="{{ url('igd/pasien') }}" role="button">
        <i class="fas fa-arrow-left mr-1"></i> Daftar Pasien
    </a>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <x-ralan.pasien :no-rawat="request()->get('no_rawat')" />
    </div>
    <div class="col-md-8">

        {{-- Triase --}}
        <x-adminlte-card title="Triase" theme="danger" icon="fas fa-ambulance" collapsible maximizable>
            <livewire:igd.triase :noRawat="request()->get('no_rawat')" />
        </x-adminlte-card>

        {{-- Penilaian Medis IGD (CPPT) --}}
        <x-adminlte-card title="Penilaian Medis IGD" theme="danger" icon="fas fa-stethoscope" collapsible="collapsed" maximizable>
            <livewire:igd.penilaian-medis :noRawat="request()->get('no_rawat')" />
        </x-adminlte-card>

        {{-- Catatan Observasi --}}
        <x-adminlte-card title="Catatan Observasi" theme="warning" icon="fas fa-heartbeat" collapsible="collapsed" maximizable>
            <livewire:igd.observasi :noRawat="request()->get('no_rawat')" />
        </x-adminlte-card>

        {{-- Resep Obat --}}
        <x-adminlte-card title="Resep" id="resepCard" theme="danger" icon="fas fa-pills" collapsible="collapsed" maximizable>
            <x-ralan.resep />
        </x-adminlte-card>

        {{-- Permintaan Lab --}}
        <livewire:ralan.permintaan-lab :no-rawat="request()->get('no_rawat')" />

        {{-- Permintaan Radiologi --}}
        <livewire:ralan.permintaan-radiologi :no-rawat="request()->get('no_rawat')" />

        {{-- Permintaan Konsultasi --}}
        <x-adminlte-card title="Permintaan Konsultasi Medik" theme="danger" icon="fas fa-user-md" collapsible="collapsed" maximizable>
            <livewire:ralan.permintaan-konsultasi :noRawat="request()->get('no_rawat')" />
        </x-adminlte-card>

    </div>
</div>

<livewire:ralan.riwayat-pemeriksaan :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
@stop

@section('plugins.TempusDominusBs4', true)
