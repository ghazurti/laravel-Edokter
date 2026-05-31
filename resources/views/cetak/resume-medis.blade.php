@extends('cetak.layout')
@section('title', 'Resume Medis')

@section('content')
    <div class="judul">Resume Medis</div>

    <table class="data" style="width:100%; margin-bottom:8px;">
        <tr>
            <td style="width:18%">Nama</td><td style="width:42%">: {{ $pasien->nm_pasien }}</td>
            <td style="width:15%">No. RM</td><td>: {{ $pasien->no_rkm_medis }}</td>
        </tr>
        <tr>
            <td>Umur / JK</td><td>: {{ $pasien->umurdaftar }} {{ $pasien->sttsumur }} / {{ $pasien->jk }}</td>
            <td>Tanggal</td><td>: {{ \Carbon\Carbon::parse($pasien->tgl_registrasi)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td>Poli</td><td>: {{ $pasien->nm_poli ?? '-' }}</td>
            <td>Dokter</td><td>: {{ $pasien->nm_dokter ?? '-' }}</td>
        </tr>
    </table>

    @if(!$resume)
        <p><i>Resume medis belum diisi untuk kunjungan ini.</i></p>
    @else
    <table class="box">
        <tr><th style="width:28%; text-align:left;">Keluhan Utama</th><td><pre>{{ $resume->keluhan_utama }}</pre></td></tr>
        <tr><th style="text-align:left;">Jalannya Penyakit</th><td><pre>{{ $resume->jalannya_penyakit }}</pre></td></tr>
        <tr><th style="text-align:left;">Pemeriksaan Penunjang</th><td><pre>{{ $resume->pemeriksaan_penunjang }}</pre></td></tr>
        <tr><th style="text-align:left;">Hasil Laborat</th><td><pre>{{ $resume->hasil_laborat }}</pre></td></tr>
        <tr><th style="text-align:left;">Diagnosa Utama</th><td>{{ $resume->diagnosa_utama }} @if($resume->kd_diagnosa_utama)({{ $resume->kd_diagnosa_utama }})@endif</td></tr>
        <tr><th style="text-align:left;">Diagnosa Sekunder</th>
            <td>
                @php
                    $sek = array_filter([
                        $resume->diagnosa_sekunder, $resume->diagnosa_sekunder2,
                        $resume->diagnosa_sekunder3, $resume->diagnosa_sekunder4,
                    ]);
                @endphp
                @forelse($sek as $s){{ $s }}@if(!$loop->last); @endif @empty - @endforelse
            </td>
        </tr>
        <tr><th style="text-align:left;">Prosedur / Tindakan</th>
            <td>
                @php
                    $pros = array_filter([
                        $resume->prosedur_utama, $resume->prosedur_sekunder,
                        $resume->prosedur_sekunder2, $resume->prosedur_sekunder3,
                    ]);
                @endphp
                @forelse($pros as $p){{ $p }}@if(!$loop->last); @endif @empty - @endforelse
            </td>
        </tr>
        <tr><th style="text-align:left;">Kondisi Pulang</th><td>{{ $resume->kondisi_pulang ?: '-' }}</td></tr>
        <tr><th style="text-align:left;">Obat Pulang</th><td><pre>{{ $resume->obat_pulang }}</pre></td></tr>
    </table>

    <div style="margin-top:6px;">
        <b>Diagnosa (ICD-10):</b>
        <ol style="margin:2px 0;">
            @forelse($diagnosa as $d)
            <li>{{ $d->nm_penyakit }} ({{ $d->kd_penyakit }})</li>
            @empty
            <li>-</li>
            @endforelse
        </ol>
    </div>
    @endif

    <div class="ttd">
        <table>
            <tr>
                <td style="width:55%"></td>
                <td class="kolom">
                    {{ env('KOTA_INSTANSI') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Dokter Penanggung Jawab,
                    <div class="space"></div>
                    <b><u>{{ $pasien->nm_dokter ?? '-' }}</u></b>
                </td>
            </tr>
        </table>
    </div>
@endsection
