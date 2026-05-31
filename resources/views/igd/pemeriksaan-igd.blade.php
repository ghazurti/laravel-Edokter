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
@php
    $noRawat = request()->get('no_rawat');
    $noRm = request()->get('no_rm');
    $isRanap = \Illuminate\Support\Facades\DB::table('reg_periksa')
        ->where('no_rawat', $noRawat)->value('status_lanjut') === 'Ranap';
@endphp
@include('partials.tab-style')
<div class="row">
    <div class="col-md-4">
        <x-ralan.pasien :no-rawat="$noRawat" />
    </div>
    <div class="col-md-8">
        <div class="card card-danger card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="tab-igd" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="i-triase-tab" data-toggle="pill" href="#i-triase" role="tab">
                            <i class="fas fa-ambulance mr-1"></i> Triase
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="i-asesmen-tab" data-toggle="pill" href="#i-asesmen" role="tab">
                            <i class="fas fa-stethoscope mr-1"></i> Asesmen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="i-order-tab" data-toggle="pill" href="#i-order" role="tab">
                            <i class="fas fa-flask mr-1"></i> Order
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="i-terapi-tab" data-toggle="pill" href="#i-terapi" role="tab">
                            <i class="fas fa-prescription-bottle-alt mr-1"></i> Terapi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="i-konsul-tab" data-toggle="pill" href="#i-konsul" role="tab">
                            <i class="fas fa-user-md mr-1"></i> Konsul
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="i-cppt-tab" data-toggle="pill" href="#i-cppt" role="tab">
                            <i class="fas fa-stream mr-1"></i> CPPT
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="i-cetak-tab" data-toggle="pill" href="#i-cetak" role="tab">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="tab-igd-content">
                    {{-- TRIASE --}}
                    <div class="tab-pane fade show active" id="i-triase" role="tabpanel">
                        <x-adminlte-card title="Triase" theme="danger" icon="fas fa-ambulance" collapsible maximizable>
                            <livewire:igd.triase :noRawat="$noRawat" />
                        </x-adminlte-card>
                    </div>

                    {{-- ASESMEN --}}
                    <div class="tab-pane fade" id="i-asesmen" role="tabpanel">
                        <x-adminlte-card title="Penilaian Medis IGD" theme="danger" icon="fas fa-stethoscope" collapsible maximizable>
                            <livewire:igd.penilaian-medis :noRawat="$noRawat" />
                        </x-adminlte-card>
                        <x-adminlte-card title="Catatan Observasi" theme="warning" icon="fas fa-heartbeat" collapsible="collapsed" maximizable>
                            <livewire:igd.observasi :noRawat="$noRawat" />
                        </x-adminlte-card>
                    </div>

                    {{-- ORDER PENUNJANG --}}
                    <div class="tab-pane fade" id="i-order" role="tabpanel">
                        <livewire:ralan.permintaan-lab :no-rawat="$noRawat" />
                        <livewire:ralan.permintaan-radiologi :no-rawat="$noRawat" />
                    </div>

                    {{-- TERAPI & TINDAKAN --}}
                    <div class="tab-pane fade" id="i-terapi" role="tabpanel">
                        <x-adminlte-card title="Resep" id="resepCard" theme="danger" icon="fas fa-pills" collapsible maximizable>
                            <x-ralan.resep />
                        </x-adminlte-card>
                        <livewire:component.tindakan :no-rawat="$noRawat" />
                        <livewire:component.persetujuan-tindakan :no-rawat="$noRawat" />
                    </div>

                    {{-- KONSUL --}}
                    <div class="tab-pane fade" id="i-konsul" role="tabpanel">
                        <x-adminlte-card title="Permintaan Konsultasi Medik" theme="danger" icon="fas fa-user-md" collapsible maximizable>
                            <livewire:ralan.permintaan-konsultasi :noRawat="$noRawat" />
                        </x-adminlte-card>
                    </div>

                    {{-- CPPT --}}
                    <div class="tab-pane fade" id="i-cppt" role="tabpanel">
                        @include('partials.cppt', ['noRawat' => $noRawat, 'isRanap' => $isRanap])
                    </div>

                    {{-- CETAK --}}
                    <div class="tab-pane fade" id="i-cetak" role="tabpanel">
                        @include('partials.cetak-dokumen', ['noRawat' => $noRawat])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<livewire:ralan.riwayat-pemeriksaan :noRawat="$noRawat" :noRm="$noRm" />
@stop

@section('plugins.TempusDominusBs4', true)
@push('js')
<script>
    $(document).on('shown.bs.tab', 'a[data-toggle="pill"]', function () {
        window.dispatchEvent(new Event('resize'));
    });
</script>
@endpush
