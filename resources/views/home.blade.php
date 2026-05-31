@extends('adminlte::page')

@section('title', 'Dashboard')

@php
    $dtConfig = [
        'order' => [[2, 'desc']],
        'pageLength' => 5,
        'lengthChange' => false,
        'searching' => false,
    ];
@endphp

@section('content_header')
    <div class="welcome-banner">
        <div class="welcome-banner-text">
            <small>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</small>
            <h2 class="mb-0">{{ $nm_dokter }}</h2>
            <span class="badge wb-badge mt-2"><i class="fas fa-hospital mr-1"></i>{{ ucwords(strtolower($poliklinik)) }}</span>
        </div>
        <div class="welcome-banner-icon">
            <i class="fas fa-user-md"></i>
        </div>
    </div>
@stop

@section('content')
    {{-- ===== Stat cards ===== --}}
    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <div class="small-box sb-antrian">
                <div class="inner">
                    <h3>{{ $antrianBelum }}</h3>
                    <p>Antrian Belum Dilayani</p>
                </div>
                <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                <a href="{{ url('ralan/pasien') }}" class="small-box-footer">Buka daftar pasien <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="small-box sb-sudah">
                <div class="inner">
                    <h3>{{ $sudahDilayani }}</h3>
                    <p>Sudah Dilayani Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-user-check"></i></div>
                <span class="small-box-footer">dari {{ $pasienPoliHariIni }} kunjungan hari ini</span>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="small-box sb-bulan">
                <div class="inner">
                    <h3>{{ $pasienPoliBulanIni }}</h3>
                    <p>Kunjungan Poli Bulan Ini</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
                <span class="small-box-footer">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="small-box sb-total">
                <div class="inner">
                    <h3>{{ $totalPasienSaya }}</h3>
                    <p>Total Pasien Saya</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <span class="small-box-footer">pasien unik pernah dilayani</span>
            </div>
        </div>
    </div>

    {{-- ===== Chart ===== --}}
    <x-adminlte-card title="Statistik Kunjungan {{ ucwords(strtolower($poliklinik)) }} — {{ date('Y') }}" theme="success" icon="fas fa-chart-column">
        @php
            $bulan = [];
            $jumlah = [];
            foreach ($statistikKunjungan as $value) {
                $bulan[] = $value->bulan;
                $jumlah[] = intval($value->jumlah);
            }
        @endphp
        <div style="position:relative; height:260px;">
            <canvas id="chartKunjungan"></canvas>
        </div>
    </x-adminlte-card>

    {{-- ===== Antrian hari ini + Pasien aktif ===== --}}
    <div class="row">
        <div class="col-lg-7">
            <x-adminlte-card theme="success" icon="fas fa-list-ol" title="Antrian Hari Ini">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width:42px;">#</th>
                                <th>Nama Pasien</th>
                                <th style="width:62px;">Jam</th>
                                <th class="text-center" style="width:90px;">Status</th>
                                <th class="text-center" style="width:80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($antrianHariIni as $row)
                                @php
                                    $nr = \App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rawat);
                                    $rm = \App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rkm_medis);
                                    $bdg = $row->stts === 'Sudah' ? 'success' : ($row->stts === 'Batal' ? 'danger' : 'warning');
                                @endphp
                                <tr>
                                    <td><span class="text-muted">{{ $row->no_reg }}</span></td>
                                    <td class="text-truncate" style="max-width:220px;">{{ $row->nm_pasien }}</td>
                                    <td><small>{{ \Illuminate\Support\Str::limit($row->jam_reg, 5, '') }}</small></td>
                                    <td class="text-center"><span class="badge badge-{{ $bdg }}">{{ $row->stts }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('ralan.pemeriksaan', ['no_rawat' => $nr, 'no_rm' => $rm]) }}"
                                           class="btn btn-xs btn-primary"><i class="fas fa-stethoscope"></i> Periksa</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada antrian hari ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>
        <div class="col-lg-5">
            <x-adminlte-card theme="success" icon="fas fa-trophy" title="Pasien Paling Aktif">
                <x-adminlte-datatable id="tablePasienAktif" :heads="$headPasienAktif" theme="light" striped hoverable :config="$dtConfig">
                    @foreach($pasienAktif as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{!! $cell !!}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)

@section('css')
    <style>
        /* ===== Welcome banner ===== */
        .welcome-banner {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
            background: linear-gradient(135deg, #2f7da3 0%, #1c4f66 100%);
            color: #fff; padding: 20px 28px; border-radius: 12px;
            box-shadow: 0 6px 20px rgba(28,79,102,0.25);
            margin-bottom: 6px; position: relative; overflow: hidden;
        }
        .welcome-banner::after {
            content: ""; position: absolute; top: -50px; right: -30px;
            width: 200px; height: 200px; background: rgba(108,196,224,0.16); border-radius: 50%;
        }
        .welcome-banner-text small { opacity: .85; font-size: 13px; letter-spacing: .4px; }
        .welcome-banner-text h2 { font-weight: 700; margin: 4px 0 0; font-size: 22px; }
        .welcome-banner .wb-badge { background: #6cc4e0; color: #143a4a; font-size: 11px; padding: 5px 10px; font-weight: 600; }
        .welcome-banner-icon { font-size: 64px; opacity: .22; position: relative; z-index: 1; }
        @media (max-width: 575px) { .welcome-banner { padding: 16px 18px; } .welcome-banner-text h2 { font-size: 18px; } .welcome-banner-icon { display: none; } }

        /* ===== Small-box stat cards (multi-tone tapi kalem) ===== */
        .small-box { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 14px rgba(0,0,0,.08); }
        .small-box > .inner { padding: 16px; }
        .small-box h3 { font-size: 2.1rem; font-weight: 700; }
        .small-box .icon { top: 12px; }
        .small-box .icon > i { font-size: 64px; opacity: .28; }
        .small-box .small-box-footer { background: rgba(0,0,0,.12); font-size: 12px; }
        .sb-antrian { background: linear-gradient(135deg,#e0922f,#c9781f); color:#fff; }
        .sb-sudah   { background: linear-gradient(135deg,#2f7da3,#256683); color:#fff; }
        .sb-bulan   { background: linear-gradient(135deg,#2c9bb5,#1f7f96); color:#fff; }
        .sb-total   { background: linear-gradient(135deg,#3a5a72,#2a4253); color:#fff; }
        .small-box h3, .small-box p, .small-box .icon > i, .small-box .small-box-footer { color: #fff !important; }

        /* ===== Cards theme ===== */
        .card.card-success.card-outline { border-top-color: #2f7da3; }
        .card.card-success > .card-header {
            background: #2f7da3 !important; color: #fff !important; border-bottom: 0;
        }
        .card.card-success > .card-header .card-title,
        .card.card-success > .card-header .card-tools .btn-tool,
        .card.card-success > .card-header i { color: #fff !important; }
        .card { border-radius: 10px; }
    </style>
@stop

@section('js')
    <script>
        (function () {
            var ctx = document.getElementById('chartKunjungan');
            if (!ctx) return;
            var g = ctx.getContext('2d').createLinearGradient(0, 0, 0, 260);
            g.addColorStop(0, 'rgba(47,125,163,0.95)');
            g.addColorStop(1, 'rgba(108,196,224,0.55)');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($bulan) !!},
                    datasets: [{
                        label: 'Kunjungan',
                        data: {!! json_encode($jumlah) !!},
                        backgroundColor: g,
                        borderColor: '#256683',
                        borderWidth: 0,
                        borderRadius: 6,
                        maxBarThickness: 46
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1c4f66',
                            padding: 10, cornerRadius: 6,
                            callbacks: { label: function (c) { return ' ' + c.parsed.y + ' kunjungan'; } }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        })();
    </script>
@stop
