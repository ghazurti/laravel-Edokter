@extends('cetak.layout')
@section('title', 'Surat Kontrol')

@section('content')
    <div class="judul">Surat Kontrol / Anjuran Kembali</div>

    <p>Pasien berikut dianjurkan untuk kontrol kembali:</p>

    <table class="data" style="width:100%; margin: 6px 0 6px 10px;">
        <tr><td style="width:30%">Nama</td><td>: {{ $pasien->nm_pasien }}</td></tr>
        <tr><td>No. Rekam Medis</td><td>: {{ $pasien->no_rkm_medis }}</td></tr>
        <tr><td>Umur / JK</td><td>: {{ $pasien->umurdaftar }} {{ $pasien->sttsumur }} / {{ $pasien->jk }}</td></tr>
    </table>

    <table class="box" style="margin: 8px 0;">
        <tr>
            <th style="width:35%; text-align:left;">Tanggal Kontrol</th>
            <td><b>{{ $tglKontrol->translatedFormat('l, d F Y') }}</b></td>
        </tr>
        <tr>
            <th style="text-align:left;">Poli / Dokter Tujuan</th>
            <td>{{ $poliTujuan ?: '-' }}</td>
        </tr>
        @if($catatan)
        <tr>
            <th style="text-align:left;">Catatan</th>
            <td><pre>{{ $catatan }}</pre></td>
        </tr>
        @endif
    </table>

    <p style="font-size:11px; color:#555;">Harap membawa surat ini saat kontrol kembali.</p>

    <div class="ttd">
        <table>
            <tr>
                <td style="width:50%"></td>
                <td class="kolom">
                    {{ env('KOTA_INSTANSI') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Dokter,
                    <div class="space"></div>
                    <b><u>{{ $pasien->nm_dokter ?? '-' }}</u></b>
                </td>
            </tr>
        </table>
    </div>
@endsection
