{{-- Tab "Risiko" — Asesmen Risiko akreditasi.
     Param: $noRawat, $modul (ralan|igd|ranap).
     Daftar instrumen mengikuti Khanza desktop (dekubitus khusus ranap). --}}
@php
    $modul = $modul ?? 'ranap';
    // Dokter mengisi 'nyeri'; sisanya (jatuh/dekubitus/gizi) tugas perawat → read-only.
    $editable = ['nyeri'];
    $instrumenRisiko = collect(\App\Support\AsesmenRisiko::untukModul($modul))
        ->sortBy(fn ($k) => in_array($k, $editable) ? 0 : 1)
        ->values();
@endphp

<div class="alert alert-info py-2">
    <i class="fas fa-clipboard-list mr-1"></i>
    <strong>Asesmen Risiko</strong> &mdash; <strong>Penilaian Nyeri</strong> diisi dokter. Instrumen lain (jatuh, dekubitus, gizi) adalah asesmen <strong>keperawatan</strong> &mdash; ditampilkan untuk <strong>review</strong> saja.
</div>

@foreach($instrumenRisiko as $key)
    <livewire:component.asesmen-risiko
        :no-rawat="$noRawat"
        :instrumen="$key"
        :modul="$modul"
        :readonly="!in_array($key, $editable)"
        :key="$modul.'-'.$key" />
@endforeach

@push('js')
<script>
    // Daftarkan listener swal sekali saja (cegah toast dobel saat banyak instance)
    if (!window.__swalRisikoBound) {
        window.__swalRisikoBound = true;
        window.addEventListener('swal', function (e) { if (window.Swal) Swal.fire(e.detail); });
    }
    window.hapusRisiko = function (cmp, tgl) {
        if (window.Swal) {
            Swal.fire({
                title: 'Hapus penilaian ini?', icon: 'warning', showCancelButton: true,
                confirmButtonText: 'Hapus', cancelButtonText: 'Batal',
            }).then(function (r) { if (r.isConfirmed) cmp.call('hapus', tgl); });
        } else if (confirm('Hapus penilaian ini?')) {
            cmp.call('hapus', tgl);
        }
    };
</script>
@endpush
