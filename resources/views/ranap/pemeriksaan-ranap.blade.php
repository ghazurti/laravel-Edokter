@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ranap')

@section('content_header')
    <h1>Pemeriksaan Ranap</h1>
@stop

@section('content')
@php
    $noRawat = request()->get('no_rawat');
    $noRm = request()->get('no_rm');
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
    <x-ranap.riwayat-ranap :no-rawat="$noRawat" />
    <div class="row">
        <div class="col-md-4">
            <x-ranap.pasien :no-rawat="$noRawat" />
        </div>
        <div class="col-md-8">
            @if($isDokterKonsul)
            <div class="alert alert-info py-2 mb-2">
                <i class="fas fa-user-md mr-1"></i>
                <strong>Mode Konsul</strong> &mdash; Anda dokter penerima rujukan internal untuk pasien ini.
            </div>
            <x-adminlte-card title="Jawaban Konsul" theme="info" icon="fas fa-lg fa-comment-medical" collapsible maximizable>
                <livewire:ralan.jawaban-konsul :noRawat="$noRawat" />
            </x-adminlte-card>
            @endif

            <div class="card card-info card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="tab-ranap" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="t-asesmen-tab" data-toggle="pill" href="#t-asesmen" role="tab">
                                <i class="fas fa-clipboard-check mr-1"></i> Asesmen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="t-order-tab" data-toggle="pill" href="#t-order" role="tab">
                                <i class="fas fa-flask mr-1"></i> Order
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="t-terapi-tab" data-toggle="pill" href="#t-terapi" role="tab">
                                <i class="fas fa-prescription-bottle-alt mr-1"></i> Terapi
                            </a>
                        </li>
                        @unless($isDokterKonsul)
                        <li class="nav-item">
                            <a class="nav-link" id="t-pulang-tab" data-toggle="pill" href="#t-pulang" role="tab">
                                <i class="fas fa-sign-out-alt mr-1"></i> Pemulangan
                            </a>
                        </li>
                        @endunless
                        <li class="nav-item">
                            <a class="nav-link" id="t-cppt-tab" data-toggle="pill" href="#t-cppt" role="tab">
                                <i class="fas fa-stream mr-1"></i> CPPT
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="t-cetak-tab" data-toggle="pill" href="#t-cetak" role="tab">
                                <i class="fas fa-print mr-1"></i> Cetak
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="tab-ranap-content">
                        {{-- ASESMEN --}}
                        <div class="tab-pane fade show active" id="t-asesmen" role="tabpanel">
                            <x-ranap.pemeriksaan-ranap :no-rawat="$noRawat" />
                            @unless($isDokterKonsul)
                            <x-adminlte-card title="Diagnosa" theme="info" icon="fas fa-lg fa-diagnoses" collapsible maximizable>
                                <livewire:ralan.diagnosa :noRawat="$noRawat" :noRm="$noRm" />
                            </x-adminlte-card>
                            @endunless
                            <livewire:ranap.catatan-pasien :noRawat="$noRawat" :noRm="$noRm" />
                        </div>

                        {{-- ORDER PENUNJANG --}}
                        <div class="tab-pane fade" id="t-order" role="tabpanel">
                            <livewire:ranap.permintaan-lab :no-rawat="$noRawat" />
                            <livewire:ranap.permintaan-radiologi :no-rawat="$noRawat" />
                        </div>

                        {{-- TERAPI & TINDAKAN --}}
                        <div class="tab-pane fade" id="t-terapi" role="tabpanel">
                            <x-ranap.resep-ranap />
                            <livewire:component.tindakan :no-rawat="$noRawat" />
                            <livewire:component.persetujuan-tindakan :no-rawat="$noRawat" />
                            <x-adminlte-card title="Laporan Operasi" icon='fas fa-stethoscope' theme="info" maximizable collapsible="collapsed">
                                <livewire:ranap.lap-operasi :no-rawat="$noRawat" />
                                <livewire:ranap.template-lap-operasi />
                            </x-adminlte-card>
                        </div>

                        {{-- PEMULANGAN --}}
                        @unless($isDokterKonsul)
                        <div class="tab-pane fade" id="t-pulang" role="tabpanel">
                            <livewire:ranap.resume-pasien :no-rawat="$noRawat" />
                            <livewire:ranap.resep-pulang :no-rawat="$noRawat" />
                        </div>
                        @endunless

                        {{-- CPPT --}}
                        <div class="tab-pane fade" id="t-cppt" role="tabpanel">
                            @include('partials.cppt', ['noRawat' => $noRawat, 'isRanap' => true])
                        </div>

                        {{-- CETAK --}}
                        <div class="tab-pane fade" id="t-cetak" role="tabpanel">
                            @include('partials.cetak-dokumen', ['noRawat' => $noRawat])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
    <script>
        // Saat pindah tab, paksa plugin (select2/datatables) hitung ulang lebar kolom
        $(document).on('shown.bs.tab', 'a[data-toggle="pill"]', function () {
            window.dispatchEvent(new Event('resize'));
        });
    </script>
@stop
