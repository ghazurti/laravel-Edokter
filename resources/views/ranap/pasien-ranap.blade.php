@extends('adminlte::page')

@section('title', 'Pasien Ranap')

@section('content_header')
    <h1>Pasien Ranap</h1>
@stop

@section('content')
    <x-adminlte-callout theme="info" >
        @php
            $config["responsive"] = true;
        @endphp
        <div class="row justify-content-between align-items-center mb-2 pr-2 pl-2">
            <div class="small text-muted">
                @if($cariTanggal)
                    Pasien masuk <strong>{{ \Carbon\Carbon::parse($tglAwal)->isoFormat('LL') }}</strong>
                    s/d <strong>{{ \Carbon\Carbon::parse($tglAkhir)->isoFormat('LL') }}</strong>
                @else
                    Menampilkan semua pasien yang masih dirawat
                @endif
            </div>
            <form action="{{route('ranap.pasien')}}" method="GET" class="form-inline justify-content-end" style="gap:.5rem">
                <div class="form-group">
                    <label class="mr-2 mb-0 small text-muted">Masuk dari</label>
                    <input type="date" name="tgl_awal" value="{{$tglAwal}}" class="form-control form-control-sm" max="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label class="mr-2 mb-0 small text-muted">s/d</label>
                    <input type="date" name="tgl_akhir" value="{{$tglAkhir}}" class="form-control form-control-sm" max="{{ date('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search mr-1"></i>Cari</button>
                @if($cariTanggal)
                <a href="{{route('ranap.pasien')}}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-undo mr-1"></i>Semua</a>
                @endif
            </form>
        </div>
        {{-- Minimal example / fill data using the component slot --}}
        <x-adminlte-datatable id="tablePasienRanap" :heads="$heads" :config="$config" head-theme="dark" striped hoverable bordered compressed>
            @foreach($data as $row)
                @php
                    $noRawat = App\Http\Controllers\Ranap\PasienRanapController::encryptData($row->no_rawat);
                    $noRM = App\Http\Controllers\Ranap\PasienRanapController::encryptData($row->no_rkm_medis);
                @endphp
                <tr>
                    <td> 
                        <a class="text-primary" href="{{route('ranap.pemeriksaan', ['no_rawat' => $noRawat, 'no_rm' => $noRM, 'bangsal' => $row->kd_bangsal])}}">
                            {{$row->nm_pasien}}
                        </a>
                    </td>
                    <td>{{$row->no_rkm_medis}}</td>
                    <td>{{$row->nm_bangsal}}</td>
                    <td>{{$row->kd_kamar}}</td>
                    <td>{{$row->tgl_masuk}}</td>
                    <td>{{$row->png_jawab}}</td>
                </tr>
            @endforeach
        </x-adminlte-datatable>

    </x-adminlte-callout>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
@stop
