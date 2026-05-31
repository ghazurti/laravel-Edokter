{{-- Caller wajib sediakan: $noRawat --}}
@php $rw = rawurlencode($noRawat); @endphp
<div class="card card-secondary collapsed-card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lg fa-print mr-1"></i> Cetak Dokumen</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        {{-- Dokumen tanpa input: langsung cetak --}}
        <div class="mb-3">
            <a href="{{ url('cetak/resep/'.$rw) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-1">
                <i class="fas fa-prescription mr-1"></i> Resep
            </a>
            <a href="{{ url('cetak/resume-medis/'.$rw) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-1">
                <i class="fas fa-file-medical mr-1"></i> Resume Medis
            </a>
        </div>

        <div class="row">
            {{-- Surat Keterangan Sakit --}}
            <div class="col-md-4 mb-2">
                <div class="border rounded p-2 h-100">
                    <b><i class="fas fa-bed mr-1"></i> Surat Keterangan Sakit</b>
                    <div class="form-group mb-1 mt-2">
                        <label class="mb-0 small">Lama istirahat (hari)</label>
                        <input type="number" min="1" value="1" class="form-control form-control-sm" id="sks_lama_{{ $rw }}">
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0 small">Mulai tanggal</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" id="sks_mulai_{{ $rw }}">
                    </div>
                    <button class="btn btn-primary btn-sm btn-block"
                        onclick="cetakSuratSakit('{{ $rw }}')">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </button>
                </div>
            </div>

            {{-- Surat Rujukan --}}
            <div class="col-md-4 mb-2">
                <div class="border rounded p-2 h-100">
                    <b><i class="fas fa-share mr-1"></i> Surat Rujukan</b>
                    <div class="form-group mb-1 mt-2">
                        <label class="mb-0 small">RS / Faskes tujuan</label>
                        <input type="text" class="form-control form-control-sm" id="rjk_rs_{{ $rw }}" placeholder="RSUD ...">
                    </div>
                    <div class="form-group mb-1">
                        <label class="mb-0 small">Bagian / Poli</label>
                        <input type="text" class="form-control form-control-sm" id="rjk_poli_{{ $rw }}" placeholder="Poli ...">
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0 small">Alasan</label>
                        <textarea class="form-control form-control-sm" rows="2" id="rjk_alasan_{{ $rw }}"></textarea>
                    </div>
                    <button class="btn btn-primary btn-sm btn-block"
                        onclick="cetakSuratRujukan('{{ $rw }}')">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </button>
                </div>
            </div>

            {{-- Surat Kontrol --}}
            <div class="col-md-4 mb-2">
                <div class="border rounded p-2 h-100">
                    <b><i class="fas fa-calendar-check mr-1"></i> Surat Kontrol</b>
                    <div class="form-group mb-1 mt-2">
                        <label class="mb-0 small">Tanggal kontrol</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" id="ktr_tgl_{{ $rw }}">
                    </div>
                    <div class="form-group mb-1">
                        <label class="mb-0 small">Poli / Dokter tujuan</label>
                        <input type="text" class="form-control form-control-sm" id="ktr_poli_{{ $rw }}">
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0 small">Catatan</label>
                        <textarea class="form-control form-control-sm" rows="2" id="ktr_catatan_{{ $rw }}"></textarea>
                    </div>
                    <button class="btn btn-primary btn-sm btn-block"
                        onclick="cetakSuratKontrol('{{ $rw }}')">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </button>
                </div>
            </div>

            {{-- Surat Keterangan Kematian --}}
            <div class="col-md-4 mb-2">
                <div class="border rounded p-2 h-100">
                    <b><i class="fas fa-cross mr-1"></i> Surat Keterangan Kematian</b>
                    <div class="form-group mb-1 mt-2">
                        <label class="mb-0 small">Nomor surat</label>
                        <input type="text" class="form-control form-control-sm" id="skm_nomor_{{ $rw }}" placeholder="0001/rs/{{ date('Y') }}">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-7 mb-1">
                            <label class="mb-0 small">Tgl meninggal</label>
                            <input type="date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" id="skm_tgl_{{ $rw }}">
                        </div>
                        <div class="form-group col-5 mb-1">
                            <label class="mb-0 small">Pukul</label>
                            <input type="time" value="{{ date('H:i') }}" class="form-control form-control-sm" id="skm_jam_{{ $rw }}">
                        </div>
                    </div>
                    <div class="form-group mb-1">
                        <label class="mb-0 small">Tempat meninggal</label>
                        <input type="text" value="Rumah Sakit" class="form-control form-control-sm" id="skm_tempat_{{ $rw }}">
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0 small">Diagnosa (ICD)</label>
                        <input type="text" class="form-control form-control-sm" id="skm_icd_{{ $rw }}" placeholder="otomatis dari diagnosa">
                    </div>
                    <button class="btn btn-primary btn-sm btn-block"
                        onclick="cetakSuratKematian('{{ $rw }}')">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    function cetakSuratSakit(rw) {
        const lama = document.getElementById('sks_lama_' + rw).value || 1;
        const mulai = document.getElementById('sks_mulai_' + rw).value;
        const q = new URLSearchParams({ lama, mulai }).toString();
        window.open('{{ url('cetak/surat-sakit') }}/' + rw + '?' + q, '_blank');
    }
    function cetakSuratRujukan(rw) {
        const q = new URLSearchParams({
            tujuan_rs: document.getElementById('rjk_rs_' + rw).value,
            tujuan_poli: document.getElementById('rjk_poli_' + rw).value,
            alasan: document.getElementById('rjk_alasan_' + rw).value,
        }).toString();
        window.open('{{ url('cetak/surat-rujukan') }}/' + rw + '?' + q, '_blank');
    }
    function cetakSuratKontrol(rw) {
        const q = new URLSearchParams({
            tgl_kontrol: document.getElementById('ktr_tgl_' + rw).value,
            poli: document.getElementById('ktr_poli_' + rw).value,
            catatan: document.getElementById('ktr_catatan_' + rw).value,
        }).toString();
        window.open('{{ url('cetak/surat-kontrol') }}/' + rw + '?' + q, '_blank');
    }
    function cetakSuratKematian(rw) {
        const q = new URLSearchParams({
            nomor: document.getElementById('skm_nomor_' + rw).value,
            tgl_wafat: document.getElementById('skm_tgl_' + rw).value,
            jam_wafat: document.getElementById('skm_jam_' + rw).value,
            tempat: document.getElementById('skm_tempat_' + rw).value,
            diagnosa_icd: document.getElementById('skm_icd_' + rw).value,
        }).toString();
        window.open('{{ url('cetak/surat-kematian') }}/' + rw + '?' + q, '_blank');
    }
</script>
@endpush
