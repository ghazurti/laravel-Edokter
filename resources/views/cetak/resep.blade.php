@extends('cetak.layout')
@section('title', 'Resep')

@section('content')
    <div class="judul">Resep Obat</div>

    <table class="data" style="width:100%; margin-bottom:8px;">
        <tr>
            <td style="width:18%">Nama</td><td style="width:42%">: {{ $pasien->nm_pasien }}</td>
            <td style="width:15%">No. RM</td><td>: {{ $pasien->no_rkm_medis }}</td>
        </tr>
        <tr>
            <td>Umur / JK</td><td>: {{ $pasien->umurdaftar }} {{ $pasien->sttsumur }} / {{ $pasien->jk }}</td>
            <td>Tanggal</td><td>: {{ \Carbon\Carbon::parse($header->tgl_peresepan ?? $pasien->tgl_registrasi)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td>Poli</td><td>: {{ $pasien->nm_poli ?? '-' }}</td>
            <td>Dokter</td><td>: {{ $pasien->nm_dokter ?? '-' }}</td>
        </tr>
    </table>

    <hr style="border:none; border-top:1px solid #000;">

    @if(!$header)
        <p><i>Belum ada resep untuk kunjungan ini.</i></p>
    @else
        <div style="min-height: 260px;">
            <div style="font-size:20px; font-weight:bold; float:left; margin-right:8px;">R/</div>
            <table class="data" style="width:92%; margin-left:30px;">
                @forelse($items as $it)
                <tr>
                    <td style="width:60%">{{ $it->nama_brng }}</td>
                    <td style="width:15%">No. {{ (int) $it->jml }}</td>
                    <td style="width:25%">{{ $it->kode_sat }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-bottom:8px;"><i>S {{ $it->aturan_pakai }}</i></td>
                </tr>
                @empty
                @endforelse

                @foreach($racikan as $rc)
                <tr>
                    <td colspan="3"><b>Racikan: {{ $rc->nama_racik }} ({{ $rc->nm_racik ?? '' }}) — {{ (int) $rc->jml_dr }} bks</b></td>
                </tr>
                @foreach($rc->detail as $d)
                <tr>
                    <td style="padding-left:16px;">- {{ $d->nama_brng }}</td>
                    <td colspan="2">{{ $d->jml }}</td>
                </tr>
                @endforeach
                <tr><td colspan="3" style="padding-bottom:8px;"><i>S {{ $rc->aturan_pakai }}</i></td></tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="ttd">
        <table>
            <tr>
                <td style="width:55%"></td>
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
