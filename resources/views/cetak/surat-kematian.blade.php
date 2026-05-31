@extends('cetak.layout')
@section('title', 'Surat Keterangan Kematian')

@section('content')
    <div class="judul">Surat Keterangan Kematian</div>
    <div class="sub-judul">Nomor : {{ $nomor ?: '............/rs/'.date('Y') }}</div>

    <p style="margin-top:14px;">Yang bertanda tangan di bawah ini menerangkan bahwa :</p>

    <table class="data" style="width:100%; margin: 6px 0 6px 20px;">
        <tr><td style="width:26%">Nama</td><td>: {{ $pasien->nm_pasien }}</td></tr>
        <tr><td>Jenis Kelamin</td><td>: {{ $pasien->jk }}</td></tr>
        <tr><td>Umur</td><td>: {{ $umur }}</td></tr>
        <tr><td>Agama</td><td>: {{ $pasien->agama ?: '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $pasien->alamat ?: '-' }}</td></tr>
        <tr><td>Pekerjaan</td><td>: {{ $pasien->pekerjaan ?: '-' }}</td></tr>
        <tr><td>No. BPJS/JKN</td><td>: {{ $pasien->no_peserta ?: '-' }}</td></tr>
    </table>

    <p style="margin-top:12px;">Telah meninggal dunia pada :</p>

    <table class="data" style="width:100%; margin: 6px 0 6px 20px;">
        <tr><td style="width:26%">Hari</td><td>: {{ $wafat->translatedFormat('l') }}</td></tr>
        <tr><td>Tanggal</td><td>: {{ $wafat->format('d-m-Y') }}</td></tr>
        <tr><td>Pukul</td><td>: {{ $wafat->format('H:i') }} WITA</td></tr>
        <tr><td>Tempat Meninggal</td><td>: {{ $tempat }}</td></tr>
        <tr><td>Diagnosa (ICD)</td><td>: {{ $diagnosaIcd ?: '-' }}</td></tr>
    </table>

    <p style="margin-top:14px;">Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

    <div class="ttd">
        <table>
            <tr>
                <td style="width:55%"></td>
                <td class="kolom">
                    {{ env('KOTA_INSTANSI') }}, {{ $wafat->format('d-m-Y') }}<br>
                    Dokter {{ env('NAMA_INSTANSI', env('APP_NAME')) }}
                    <div class="space"></div>
                    <b><u>{{ $pasien->nm_dokter ?? '-' }}</u></b>
                </td>
            </tr>
        </table>
    </div>
@endsection
