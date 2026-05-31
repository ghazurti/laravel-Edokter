@extends('cetak.layout')
@section('title', 'Surat Keterangan Sakit')

@section('content')
    <div class="judul">Surat Keterangan Sakit</div>
    <div class="sub-judul">Nomor: ............/SKS/{{ date('Y') }}</div>

    <p>Yang bertanda tangan di bawah ini, dokter pada {{ env('NAMA_INSTANSI', env('APP_NAME')) }}, menerangkan bahwa:</p>

    <table class="data" style="width:100%; margin: 6px 0 6px 20px;">
        <tr><td style="width:22%">Nama</td><td>: {{ $pasien->nm_pasien }}</td></tr>
        <tr><td>Umur</td><td>: {{ $pasien->umurdaftar }} {{ $pasien->sttsumur }}</td></tr>
        <tr><td>Jenis Kelamin</td><td>: {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
        <tr><td>Pekerjaan</td><td>: {{ $pasien->pekerjaan ?: '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $pasien->alamat ?: '-' }}</td></tr>
        <tr><td>No. Rekam Medis</td><td>: {{ $pasien->no_rkm_medis }}</td></tr>
    </table>

    <p style="text-align: justify;">
        Berdasarkan hasil pemeriksaan, pasien tersebut dinyatakan <b>sakit</b> dan memerlukan
        istirahat selama <b>{{ $lama }} ({{ \App\Helpers\Terbilang::make($lama) }}) hari</b>,
        terhitung mulai tanggal <b>{{ $mulai->translatedFormat('d F Y') }}</b>
        sampai dengan <b>{{ $selesai->translatedFormat('d F Y') }}</b>.
    </p>

    @if(count($diagnosa) > 0)
    <p style="font-size:11px; color:#444;">
        <i>Diagnosa: @foreach($diagnosa as $d){{ $d->nm_penyakit }}@if(!$loop->last); @endif @endforeach</i>
    </p>
    @endif

    <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

    <div class="ttd">
        <table>
            <tr>
                <td style="width:55%"></td>
                <td class="kolom">
                    {{ env('KOTA_INSTANSI') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Dokter Pemeriksa,
                    <div class="space"></div>
                    <b><u>{{ $pasien->nm_dokter ?? '-' }}</u></b>
                </td>
            </tr>
        </table>
    </div>
@endsection
