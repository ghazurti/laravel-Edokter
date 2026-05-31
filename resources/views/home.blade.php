@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="welcome-banner">
        <div class="welcome-banner-text">
            <small>Selamat datang kembali,</small>
            <h2 class="mb-0">{{$nm_dokter}}</h2>
            <span class="badge badge-warning mt-2"><i class="fas fa-hospital mr-1"></i>{{ ucwords(strtolower($poliklinik)) }}</span>
        </div>
        <div class="welcome-banner-icon">
            <i class="fas fa-user-md"></i>
        </div>
    </div>
@stop

@section('content')
        <div class="row">
            <div class="col-sm-6 col-md-3">
                <x-adminlte-info-box title="TOTAL PASIEN" text="{{$totalPasien}}" icon="fas fa-lg fa-users" theme="success"/>
            </div>
            <div class="col-sm-6 col-md-3">
                <x-adminlte-info-box title="PASIEN BULAN INI" text="{{$pasienBulanIni}}" icon="fas fa-lg fa-clipboard" theme="success"/>
            </div>
            <div class="col-sm-6 col-md-3">
                <x-adminlte-info-box title="PASIEN POLI BULAN INI" text="{{$pasienPoliBulanIni}}" icon="fas fa-lg fa-hospital" theme="warning"/>
            </div>
            <div class="col-sm-6 col-md-3">
                <x-adminlte-info-box title="PASIEN POLI HARI INI" text="{{$pasienPoliHariIni}}" icon="fas fa-lg fa-stethoscope" theme="success"/>
            </div>
        </div>

        <x-adminlte-card title="Statistik Kunjungan {{ ucwords(strtolower($poliklinik))}}" theme="success" icon="fas fa-chart-bar" >
            @php 
                $bulan = [];
                $jumlah = [];
                foreach ($statistikKunjungan as $key => $value) {
                    $bulan[] = $value->bulan;
                    $jumlah[] = intval($value->jumlah);
                }
            @endphp
            <canvas id="chartKunjungan" height="100px"></canvas>
        </x-adminlte-card>
        
    @php
        $config = [
            'order' => [[2, 'asc']],
            'columns' => [null, null, null, ['orderable' => true]],
        ];
    @endphp
    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card theme="success" icon="fas fa-trophy" title="Pasien {{ ucwords(strtolower($poliklinik))}} Paling Aktif" >
                <x-adminlte-datatable id="table5" :heads="$headPasienAktif" theme="light" striped hoverable>
                    @foreach($pasienAktif as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{!! $cell !!}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-callout>
        </div>
        <div class="col-md-6">
            <x-adminlte-card theme="success" icon="fas fa-clock" title="Antrian 10 Pasien Terakhir {{ ucwords(strtolower($poliklinik))}}" >
                <x-adminlte-datatable id="table6" :heads="$headPasienTerakhir" theme="light" striped hoverable>
                    @foreach($pasienTerakhir as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{!! $cell !!}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-callout>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)

@section('css')
    <style>
        .welcome-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            background: linear-gradient(135deg, #2f7da3 0%, #256683 100%);
            color: #ffffff;
            padding: 20px 28px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(47,125,163,0.25);
            margin-bottom: 4px;
            position: relative;
            overflow: hidden;
        }
        @media (max-width: 575px) {
            .welcome-banner { padding: 16px 18px; }
            .welcome-banner-text h2 { font-size: 18px; }
            .welcome-banner-icon { display: none; }
        }
        @media (max-width: 991px) {
            .welcome-banner-icon { font-size: 48px; }
        }
        .welcome-banner::after {
            content: "";
            position: absolute;
            top: -40px; right: -40px;
            width: 180px; height: 180px;
            background: rgba(108,196,224,0.18);
            border-radius: 50%;
        }
        .welcome-banner-text small {
            opacity: 0.85;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        .welcome-banner-text h2 {
            font-weight: 700;
            margin: 4px 0 0;
            font-size: 22px;
        }
        .welcome-banner-text .badge-warning {
            background: #6cc4e0;
            color: #143a4a;
            font-size: 11px;
            padding: 5px 10px;
            font-weight: 600;
        }
        .welcome-banner-icon {
            font-size: 64px;
            opacity: 0.25;
            position: relative;
            z-index: 1;
        }

        /* Info boxes accent */
        .info-box .info-box-icon.bg-success { background: #2f7da3 !important; }
        .info-box .info-box-icon.bg-warning { background: #6cc4e0 !important; color: #143a4a !important; }

        /* Cards with success theme — use logo green, white title */
        .card.card-success.card-outline { border-top-color: #2f7da3; }
        .card.card-success > .card-header,
        .card.bg-success > .card-header {
            background: #2f7da3 !important;
            color: #ffffff !important;
            border-bottom: 0;
        }
        .card.card-success > .card-header .card-title,
        .card.card-success > .card-header .card-tools .btn-tool,
        .card.card-success > .card-header i {
            color: #ffffff !important;
        }

        /* Info-box title white */
        .info-box .info-box-text {
            color: #ffffff !important;
            font-weight: 600;
        }
        .info-box .info-box-number {
            color: #ffffff !important;
        }
        .info-box { background: #2f7da3; }
        .info-box .info-box-icon { background: rgba(255,255,255,0.18) !important; color: #ffffff !important; }
    </style>
@stop

@section('js')
    <script>
        const ctx = document.getElementById('chartKunjungan').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($bulan) !!},
                datasets: [{
                    label: 'Jumlah Kunjungan ' + "{{ ucwords(strtolower($poliklinik))}}",
                    data: {!! json_encode($jumlah) !!},
                    backgroundColor: 'rgba(47,125,163, 0.75)',
                    borderColor: '#256683',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@stop
