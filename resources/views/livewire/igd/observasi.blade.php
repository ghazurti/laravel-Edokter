<div>
    {{-- Form input baru --}}
    <h6 class="font-weight-bold">Tambah Catatan Observasi</h6>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label>GCS</label>
                <input wire:model="gcs" type="text" class="form-control form-control-sm" placeholder="E4V5M6">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>TD (mmHg)</label>
                <input wire:model="td" type="text" class="form-control form-control-sm" placeholder="120/80">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>HR (/mnt)</label>
                <input wire:model="hr" type="text" class="form-control form-control-sm" placeholder="80">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>RR (/mnt)</label>
                <input wire:model="rr" type="text" class="form-control form-control-sm" placeholder="20">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Suhu (°C)</label>
                <input wire:model="suhu" type="text" class="form-control form-control-sm" placeholder="36.5">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>SpO2 (%)</label>
                <input wire:model="spo2" type="text" class="form-control form-control-sm" placeholder="98">
            </div>
        </div>
    </div>
    <button wire:click="simpan" class="btn btn-warning btn-sm mb-3">
        <i class="fas fa-plus mr-1"></i> Tambah Observasi
    </button>

    {{-- Tabel riwayat observasi --}}
    @if($listObservasi->count() > 0)
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Waktu</th>
                    <th>Petugas</th>
                    <th>GCS</th>
                    <th>TD</th>
                    <th>HR</th>
                    <th>RR</th>
                    <th>Suhu</th>
                    <th>SpO2</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listObservasi as $obs)
                <tr>
                    <td>{{ $obs->tgl_perawatan }} {{ $obs->jam_rawat }}</td>
                    <td>{{ $obs->nama ?? $obs->nip }}</td>
                    <td>{{ $obs->gcs }}</td>
                    <td>{{ $obs->td }}</td>
                    <td>{{ $obs->hr }}</td>
                    <td>{{ $obs->rr }}</td>
                    <td>{{ $obs->suhu }}</td>
                    <td>{{ $obs->spo2 }}</td>
                    <td>
                        <button wire:click="hapus('{{ $obs->tgl_perawatan }}', '{{ $obs->jam_rawat }}')"
                            class="btn btn-xs btn-danger"
                            onclick="return confirm('Hapus observasi ini?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-muted text-sm">Belum ada catatan observasi.</p>
    @endif
</div>
