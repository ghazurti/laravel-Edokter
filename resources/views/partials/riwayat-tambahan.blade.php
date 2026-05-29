{{-- Variabel yang harus disediakan caller: $ctrl (FQCN controller pemilik trait RiwayatPasien), $noRawat, $isRanap (bool) --}}
@php
    $resep            = $ctrl::riwayatResep($noRawat);
    $operasiList      = $ctrl::riwayatOperasi($noRawat);
    $tindakanDokter   = $ctrl::riwayatTindakanDokter($noRawat, $isRanap);
    $tindakanPerawat  = $ctrl::riwayatTindakanPerawat($noRawat, $isRanap);
    $rujukanInternal  = $ctrl::riwayatRujukanInternal($noRawat);
    $resepPulangList  = $ctrl::riwayatResepPulang($noRawat);
    $berkas           = $ctrl::riwayatBerkasDigital($noRawat);
@endphp

@if(count($resep) > 0)
<x-adminlte-card theme="dark" title="Resep / Obat ({{ count($resep) }})" collapsible="collapsed" maximizable>
    @foreach($resep as $r)
        <div class="border-bottom pb-2 mb-2">
            <div class="d-flex justify-content-between">
                <span><b>{{ $r->no_resep }}</b> &mdash; {{ $r->nm_dokter ?? '-' }}</span>
                <span>{{ $r->tgl_peresepan }} {{ substr($r->jam_peresepan ?? '', 0, 5) }}
                    <span class="badge badge-{{ ($r->status ?? '') === 'Sudah' ? 'success' : 'secondary' }}">{{ $r->status ?? '-' }}</span>
                </span>
            </div>
            @if(count($r->items) > 0)
            <table class="table table-sm table-bordered mt-1 mb-0">
                <thead class="bg-light"><tr><th>Nama Obat</th><th class="text-center">Jumlah</th><th>Aturan Pakai</th></tr></thead>
                <tbody>
                    @foreach($r->items as $it)
                    <tr>
                        <td>{{ $it->nama_brng }}</td>
                        <td class="text-center">{{ (int) $it->jml }} {{ $it->kode_sat }}</td>
                        <td>{{ $it->aturan_pakai }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    @endforeach
</x-adminlte-card>
@endif

@if(count($resepPulangList) > 0)
<x-adminlte-card theme="dark" title="Permintaan Resep Pulang ({{ count($resepPulangList) }})" collapsible="collapsed" maximizable>
    @foreach($resepPulangList as $rp)
        <div class="border-bottom pb-2 mb-2">
            <div class="d-flex justify-content-between">
                <span><b><code>{{ $rp->no_permintaan }}</code></b> &mdash; {{ $rp->nm_dokter ?? '-' }}</span>
                <span>{{ \Carbon\Carbon::parse($rp->tgl_permintaan)->format('d-m-Y') }} {{ substr($rp->jam, 0, 5) }}
                    <span class="badge badge-{{ $rp->status === 'Sudah' ? 'success' : 'warning' }}">{{ $rp->status }}</span>
                </span>
            </div>
            @if(count($rp->items) > 0)
            <table class="table table-sm table-bordered mt-1 mb-0">
                <thead class="bg-light"><tr><th>Nama Obat</th><th class="text-center">Jumlah</th><th>Aturan Pakai</th></tr></thead>
                <tbody>
                    @foreach($rp->items as $it)
                    <tr>
                        <td>{{ $it->nama_brng }}</td>
                        <td class="text-center">{{ (int) $it->jml }} {{ $it->kode_sat }}</td>
                        <td>{{ $it->dosis }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    @endforeach
</x-adminlte-card>
@endif

@if(count($tindakanDokter) > 0)
<x-adminlte-card theme="dark" title="Tindakan Dokter ({{ count($tindakanDokter) }})" collapsible="collapsed" maximizable>
    <table class="table table-sm table-bordered">
        <thead class="bg-light"><tr><th>Tanggal</th><th>Jam</th><th>Tindakan</th><th>Dokter</th><th class="text-right">Biaya</th></tr></thead>
        <tbody>
            @foreach($tindakanDokter as $t)
            <tr>
                <td>{{ $t->tgl_perawatan }}</td>
                <td>{{ substr($t->jam_rawat ?? '', 0, 5) }}</td>
                <td>{{ $t->nm_perawatan ?? '-' }}</td>
                <td>{{ $t->nm_dokter ?? '-' }}</td>
                <td class="text-right">{{ number_format((float) $t->biaya_rawat, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-adminlte-card>
@endif

@if(count($tindakanPerawat) > 0)
<x-adminlte-card theme="dark" title="Tindakan Perawat ({{ count($tindakanPerawat) }})" collapsible="collapsed" maximizable>
    <table class="table table-sm table-bordered">
        <thead class="bg-light"><tr><th>Tanggal</th><th>Jam</th><th>Tindakan</th><th>Petugas</th><th class="text-right">Biaya</th></tr></thead>
        <tbody>
            @foreach($tindakanPerawat as $t)
            <tr>
                <td>{{ $t->tgl_perawatan }}</td>
                <td>{{ substr($t->jam_rawat ?? '', 0, 5) }}</td>
                <td>{{ $t->nm_perawatan ?? '-' }}</td>
                <td>{{ $t->nama ?? '-' }}</td>
                <td class="text-right">{{ number_format((float) $t->biaya_rawat, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-adminlte-card>
@endif

@if(count($operasiList) > 0)
<x-adminlte-card theme="dark" title="Laporan Operasi ({{ count($operasiList) }})" collapsible="collapsed" maximizable>
    <table class="table table-sm table-bordered">
        <thead class="bg-light"><tr><th>Tgl Operasi</th><th>Paket</th><th>Kategori</th><th>Anestesi</th><th>Operator</th><th>Dr. Anestesi</th></tr></thead>
        <tbody>
            @foreach($operasiList as $o)
            <tr>
                <td>{{ $o->tgl_operasi }}</td>
                <td>{{ $o->nm_perawatan ?? '-' }}</td>
                <td>{{ $o->kategori }}</td>
                <td>{{ $o->jenis_anasthesi }}</td>
                <td>{{ $o->operator1 }}</td>
                <td>{{ $o->dokter_anestesi }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-adminlte-card>
@endif

@if(count($rujukanInternal) > 0)
<x-adminlte-card theme="dark" title="Rujukan Internal ({{ count($rujukanInternal) }})" collapsible="collapsed" maximizable>
    <table class="table table-sm table-bordered">
        <thead class="bg-light"><tr><th>Dirujuk ke Poli</th><th>Dokter Tujuan</th></tr></thead>
        <tbody>
            @foreach($rujukanInternal as $rj)
            <tr>
                <td>{{ $rj->nm_poli ?? $rj->kd_poli }}</td>
                <td>{{ $rj->nm_dokter ?? $rj->kd_dokter }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-adminlte-card>
@endif

@if(count($berkas) > 0)
<x-adminlte-card theme="dark" title="Berkas Digital ({{ count($berkas) }})" collapsible="collapsed" maximizable>
    <ul class="mb-0">
        @foreach($berkas as $b)
        <li>
            <span class="badge badge-info">{{ $b->kode }}</span>
            <a href="{{ env('BERKAS_BASE_URL', '') }}{{ $b->lokasi_file }}" target="_blank" rel="noopener">
                {{ $b->lokasi_file }}
            </a>
        </li>
        @endforeach
    </ul>
</x-adminlte-card>
@endif
