@extends('cetak.layout')
@section('title', 'Persetujuan/Penolakan Tindakan')

@section('content')
    <div class="judul">
        @if($data->pernyataan === 'Penolakan') Penolakan @else Persetujuan @endif Tindakan Kedokteran
    </div>
    <div class="sub-judul">No. {{ $data->no_pernyataan }}</div>

    <p style="margin-bottom:4px;">Telah dijelaskan kepada saya / keluarga saya hal-hal berikut beserta pemahamannya:</p>

    <table class="box" style="margin-bottom:8px;">
        <tr><th style="width:24%; text-align:left;">Hal</th><th>Penjelasan</th><th style="width:60px;">Paham</th></tr>
        @php
            $rows = [
                ['Diagnosa', $data->diagnosa, $data->diagnosa_konfirmasi],
                ['Dasar Diagnosis/Indikasi', $data->indikasi_tindakan, $data->indikasi_tindakan_konfirmasi],
                ['Tindakan Kedokteran', $data->tindakan, $data->tindakan_konfirmasi],
                ['Tata Cara', $data->tata_cara, $data->tata_cara_konfirmasi],
                ['Tujuan', $data->tujuan, $data->tujuan_konfirmasi],
                ['Risiko', $data->risiko, $data->risiko_konfirmasi],
                ['Komplikasi', $data->komplikasi, $data->komplikasi_konfirmasi],
                ['Prognosis', $data->prognosis, $data->prognosis_konfirmasi],
                ['Alternatif & Risikonya', $data->alternatif_dan_risikonya, $data->alternatif_konfirmasi],
                ['Perkiraan Biaya', $data->biaya ? 'Rp '.number_format((float)$data->biaya,0,',','.') : '-', $data->biaya_konfirmasi],
                ['Lain-lain', $data->lain_lain, $data->lain_lain_konfirmasi],
            ];
        @endphp
        @foreach($rows as [$label, $isi, $konf])
        <tr>
            <td>{{ $label }}</td>
            <td>{{ $isi ?: '-' }}</td>
            <td style="text-align:center;">{{ $konf === 'true' ? '✓' : '—' }}</td>
        </tr>
        @endforeach
    </table>

    <p style="text-align: justify; margin-bottom:6px;">
        Dengan ini saya yang bertanda tangan di bawah ini menyatakan
        <b>@if($data->pernyataan === 'Penolakan') MENOLAK @else MENYETUJUI @endif</b>
        untuk dilakukannya tindakan kedokteran tersebut terhadap:
    </p>

    <table class="data" style="width:100%; margin: 0 0 6px 16px;">
        <tr><td style="width:22%">Nama Pasien</td><td>: {{ $pasien->nm_pasien ?? '-' }}</td></tr>
        <tr><td>No. Rekam Medis</td><td>: {{ $pasien->no_rkm_medis ?? '-' }}</td></tr>
    </table>

    <p style="margin-bottom:2px;">Yang membuat pernyataan (Penerima Informasi):</p>
    <table class="data" style="width:100%; margin: 0 0 6px 16px;">
        <tr>
            <td style="width:22%">Nama</td><td style="width:45%">: {{ $data->penerima_informasi }}</td>
            <td style="width:12%">L/P</td><td>: {{ $data->jk_penerima_informasi }}</td>
        </tr>
        <tr>
            <td>Umur / Tgl Lahir</td><td>: {{ $data->umur_penerima_informasi }}
                @if($data->tanggal_lahir_penerima_informasi && $data->tanggal_lahir_penerima_informasi !== '0000-00-00')
                    ({{ \Carbon\Carbon::parse($data->tanggal_lahir_penerima_informasi)->format('d-m-Y') }})
                @endif
            </td>
            <td>No. HP</td><td>: {{ $data->no_hp ?: '-' }}</td>
        </tr>
        <tr><td>Alamat</td><td colspan="3">: {{ $data->alamat_penerima_informasi ?: '-' }}</td></tr>
        <tr>
            <td>Hubungan</td><td>: {{ $data->hubungan_penerima_informasi }}</td>
            <td>Alasan diwakilkan</td><td>: {{ $data->alasan_diwakilkan_penerima_informasi ?: '-' }}</td>
        </tr>
    </table>

    <table style="width:100%; margin-top:18px;">
        <tr>
            <td style="width:33%; text-align:center; vertical-align:top;">
                Dokter / Pemberi Informasi
                <div style="height:55px;"></div>
                <b><u>{{ $data->nm_dokter ?? $data->kd_dokter }}</u></b>
            </td>
            <td style="width:33%; text-align:center; vertical-align:top;">
                {{ env('KOTA_INSTANSI') }}, {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}<br>
                Yang Menyatakan
                <div style="height:40px;"></div>
                <b><u>{{ $data->penerima_informasi }}</u></b>
            </td>
            <td style="width:33%; text-align:center; vertical-align:top;">
                Saksi Keluarga
                <div style="height:55px;"></div>
                <b><u>{{ $data->saksi_keluarga ?: '(.................)' }}</u></b>
            </td>
        </tr>
    </table>
@endsection
