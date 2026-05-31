@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ralan')

@section('content_header')
<div class="d-flex flex-row justify-content-between">
    <h1>Pemeriksaan Ralan</h1>
    <a name="" id="" class="btn btn-primary" href="{{ url('ralan/pasien') }}" role="button">Daftar Pasien</a>
</div>
@stop

@section('content')
@php
    $noRawat = request()->get('no_rawat');
    $noRm = request()->get('no_rm');
    $kdPoli = session()->get('kd_poli');
    $dpjpUtama = \Illuminate\Support\Facades\DB::table('reg_periksa')
        ->where('no_rawat', $noRawat)
        ->value('kd_dokter');
    $isDokterKonsul = $dpjpUtama
        && $dpjpUtama !== session('username')
        && \Illuminate\Support\Facades\DB::table('rujukan_internal_poli')
            ->where('no_rawat', $noRawat)
            ->where('kd_dokter', session('username'))
            ->exists();
@endphp
@include('partials.tab-style')
<x-ralan.riwayat :no-rawat="$noRawat" />
<div class="row">
    <div class="col-md-4">
        <x-ralan.pasien :no-rawat="$noRawat" />
    </div>
    <div class="col-md-8">
        @if($isDokterKonsul)
        <div class="alert alert-info py-2 mb-2">
            <i class="fas fa-user-md mr-1"></i>
            <strong>Mode Konsul</strong> &mdash; Anda adalah dokter penerima rujukan internal untuk pasien ini.
        </div>
        <x-adminlte-card title="Jawaban Konsul" theme="info" icon="fas fa-lg fa-comment-medical" collapsible maximizable>
            <livewire:ralan.jawaban-konsul :noRawat="$noRawat" />
        </x-adminlte-card>
        @endif

        <div class="card card-info card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="tab-ralan" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="r-asesmen-tab" data-toggle="pill" href="#r-asesmen" role="tab">
                            <i class="fas fa-clipboard-check mr-1"></i> Asesmen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="r-order-tab" data-toggle="pill" href="#r-order" role="tab">
                            <i class="fas fa-flask mr-1"></i> Order
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="r-terapi-tab" data-toggle="pill" href="#r-terapi" role="tab">
                            <i class="fas fa-prescription-bottle-alt mr-1"></i> Terapi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="r-rujukan-tab" data-toggle="pill" href="#r-rujukan" role="tab">
                            <i class="fas fa-share mr-1"></i> Rujukan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="r-cppt-tab" data-toggle="pill" href="#r-cppt" role="tab">
                            <i class="fas fa-stream mr-1"></i> CPPT
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="r-cetak-tab" data-toggle="pill" href="#r-cetak" role="tab">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="tab-ralan-content">
                    {{-- ASESMEN --}}
                    <div class="tab-pane fade show active" id="r-asesmen" role="tabpanel">
                        @if($kdPoli == 'U017')
                        <x-adminlte-card title="Uji Fungsi KFR" theme="info" collapsible="collapsed" maximizable>
                            <livewire:ralan.uji-fungsi-kfr :noRawat="$noRawat" />
                        </x-adminlte-card>
                        @endif

                        <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-clipboard-check" collapsible maximizable>
                            <livewire:ralan.pemeriksaan :noRawat="$noRawat" :noRm="$noRm" />
                            <livewire:ralan.modal.edit-pemeriksaan />
                        </x-adminlte-card>

                        @if($kdPoli == 'U002' || $kdPoli == 'U003')
                        <livewire:ralan.odontogram :noRawat="$noRawat" :noRm="$noRm" />
                        @endif

                        @unless($isDokterKonsul)
                        <x-adminlte-card title="Diagnosa" theme="info" icon="fas fa-lg fa-diagnoses" collapsible="collapsed" maximizable>
                            <livewire:ralan.diagnosa :noRawat="$noRawat" :noRm="$noRm" />
                        </x-adminlte-card>
                        @endunless

                        <livewire:ralan.catatan :noRawat="$noRawat" :noRm="$noRm" />

                        @unless($isDokterKonsul)
                        <livewire:ralan.resume :no-rawat="$noRawat" :noRm="$noRm" />
                        @endunless
                    </div>

                    {{-- ORDER PENUNJANG --}}
                    <div class="tab-pane fade" id="r-order" role="tabpanel">
                        <livewire:ralan.permintaan-lab :no-rawat="$noRawat" />
                        <livewire:ralan.permintaan-radiologi :no-rawat="$noRawat" />
                    </div>

                    {{-- TERAPI & TINDAKAN --}}
                    <div class="tab-pane fade" id="r-terapi" role="tabpanel">
                        <x-adminlte-card title="Resep" id="resepCard" theme="info" icon="fas fa-lg fa-prescription-bottle-alt" collapsible maximizable>
                            <x-ralan.resep />
                        </x-adminlte-card>
                        <livewire:component.tindakan :no-rawat="$noRawat" />
                        <livewire:component.persetujuan-tindakan :no-rawat="$noRawat" />
                        <x-adminlte-card title="Laporan Operasi" icon="fas fa-lg fa-notes-medical" theme="info" maximizable collapsible="collapsed">
                            <livewire:ranap.lap-operasi :no-rawat="$noRawat" />
                            <livewire:ranap.template-lap-operasi />
                        </x-adminlte-card>
                    </div>

                    {{-- RUJUKAN & KONSUL --}}
                    <div class="tab-pane fade" id="r-rujukan" role="tabpanel">
                        @unless($isDokterKonsul)
                        <x-ralan.rujuk-internal :no-rawat="$noRawat" />
                        @endunless
                        <x-adminlte-card title="Permintaan Konsultasi Medik" theme="info" icon="fas fa-lg fa-comment-medical" collapsible maximizable>
                            <livewire:ralan.permintaan-konsultasi :noRawat="$noRawat" />
                        </x-adminlte-card>
                    </div>

                    {{-- CPPT --}}
                    <div class="tab-pane fade" id="r-cppt" role="tabpanel">
                        @include('partials.cppt', ['noRawat' => $noRawat, 'isRanap' => false])
                    </div>

                    {{-- CETAK --}}
                    <div class="tab-pane fade" id="r-cetak" role="tabpanel">
                        @include('partials.cetak-dokumen', ['noRawat' => $noRawat])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('plugins.TempusDominusBs4', true)
@push('js')
<script>
    // Saat pindah tab, paksa plugin (select2/datatables) hitung ulang lebar kolom
    $(document).on('shown.bs.tab', 'a[data-toggle="pill"]', function () {
        window.dispatchEvent(new Event('resize'));
    });
</script>
@endpush
