<div>
    @if(!$hasData)
        <div class="alert alert-warning mb-0">
            <i class="fas fa-info-circle mr-1"></i> Belum ada permintaan konsul untuk Anda di pasien ini.
        </div>
    @else
        {{-- Info konsul dari dokter perujuk --}}
        <div class="callout callout-info py-2">
            <div class="row small">
                <div class="col-md-4"><strong>Perujuk:</strong> {{ $perujukNama }}</div>
                <div class="col-md-4"><strong>Poli:</strong> {{ $perujukPoli }}</div>
                <div class="col-md-4"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tglRujukan)->isoFormat('DD/MM/YYYY HH:mm') }}</div>
            </div>
            <hr class="my-2">
            <div><strong>Catatan/Pertanyaan Konsul:</strong></div>
            <div class="text-muted small">{!! nl2br(e($catatanKonsul ?: '-')) !!}</div>
        </div>

        {{-- Form jawaban --}}
        <div class="form-group">
            <label class="small mb-1">Pemeriksaan Konsul</label>
            <textarea wire:model.defer="pemeriksaan" class="form-control form-control-sm" rows="3"
                placeholder="Hasil pemeriksaan saat menjawab konsul..."></textarea>
        </div>
        <div class="form-group">
            <label class="small mb-1">Diagnosa Konsul</label>
            <textarea wire:model.defer="diagnosa" class="form-control form-control-sm" rows="2"
                placeholder="Diagnosa dari perspektif spesialis penerima konsul..."></textarea>
        </div>
        <div class="form-group">
            <label class="small mb-1">
                Saran / Jawaban Konsul <span class="text-danger">*</span>
            </label>
            <textarea wire:model.defer="saran" class="form-control form-control-sm" rows="4"
                placeholder="Saran/rekomendasi/jawaban untuk dokter perujuk..."></textarea>
        </div>
        <div class="d-flex justify-content-end">
            <button wire:click="simpan" class="btn btn-primary btn-sm">
                <i class="fas fa-save mr-1"></i> Simpan Jawaban Konsul
            </button>
        </div>
    @endif
</div>
