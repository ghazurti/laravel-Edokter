@extends('cetak.layout')
@section('title', 'Surat Rujukan')

@section('content')
    <div class="judul">Surat Rujukan</div>
    <div class="sub-judul">Nomor: ............/RJK/{{ date('Y') }}</div>

    <table class="data" style="width:100%; margin-bottom:8px;">
        <tr><td style="width:18%">Kepada Yth.</td><td>: {{ $tujuanRs ?: '....................................' }}</td></tr>
        <tr><td>Bagian / Poli</td><td>: {{ $tujuanPoli ?: '....................................' }}</td></tr>
        <tr><td>Di</td><td>: Tempat</td></tr>
    </table>

    <p>Dengan hormat, mohon pemeriksaan dan penanganan lebih lanjut terhadap pasien:</p>

    <table class="data" style="width:100%; margin: 6px 0 6px 20px;">
        <tr><td style="width:22%">Nama</td><td>: {{ $pasien->nm_pasien }}</td></tr>
        <tr><td>Umur / JK</td><td>: {{ $pasien->umurdaftar }} {{ $pasien->sttsumur }} / {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $pasien->alamat ?: '-' }}</td></tr>
        <tr><td>No. Rekam Medis</td><td>: {{ $pasien->no_rkm_medis }}</td></tr>
    </table>

    <table class="box" style="margin-bottom:8px;">
        <tr><th style="width:25%; text-align:left;">Diagnosa Kerja</th>
            <td>
                @forelse($diagnosa as $d){{ $d->nm_penyakit }} ({{ $d->kd_penyakit }})@if(!$loop->last); @endif @empty - @endforelse
            </td>
        </tr>
        <tr><th style="text-align:left;">Alasan / Anamnesa</th>
            <td><pre>{{ $alasan ?: '-' }}</pre></td>
        </tr>
    </table>

    <p>Atas bantuan dan kerjasamanya, kami ucapkan terima kasih.</p>

    <div class="ttd">
        <table>
            <tr>
                <td style="width:55%"></td>
                <td class="kolom">
                    {{ env('KOTA_INSTANSI') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Dokter Perujuk,
                    <div class="space"></div>
                    <b><u>{{ $pasien->nm_dokter ?? '-' }}</u></b>
                </td>
            </tr>
        </table>
    </div>
@endsection
