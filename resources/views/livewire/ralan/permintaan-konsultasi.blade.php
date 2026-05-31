<div>
    <form wire:submit.prevent="simpan">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="datetime-local" class="form-control form-control-sm"
                        wire:model="tanggal">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Jenis Permintaan</label>
                    <select class="form-control form-control-sm" wire:model="jenisPermintaan">
                        <option value="Konsultasi">Konsultasi</option>
                        <option value="Evaluasi">Evaluasi</option>
                        <option value="Rawat Bersama">Rawat Bersama</option>
                        <option value="Alih Rawat">Alih Rawat</option>
                        <option value="Pre/Post Operasi">Pre/Post Operasi</option>
                    </select>
                </div>
            </div>
            @if($editMode)
            <div class="col-md-4">
                <div class="form-group">
                    <label>No. Permintaan</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $noPermintaan }}" readonly>
                </div>
            </div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Dokter Yang Konsul</label>
                    <select class="form-control form-control-sm" wire:model="kdDokter">
                        <option value="">-- Pilih Dokter --</option>
                        @foreach($dokterList as $dok)
                        <option value="{{ $dok->kd_dokter }}">{{ $dok->nm_dokter }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Dokter Yang Dikonsuli <span class="text-danger">*</span></label>
                    <select class="form-control form-control-sm @error('kdDokterDikonsuli') is-invalid @enderror" wire:model="kdDokterDikonsuli">
                        <option value="">-- Pilih Dokter --</option>
                        @foreach($dokterList as $dok)
                        <option value="{{ $dok->kd_dokter }}">{{ $dok->nm_dokter }}</option>
                        @endforeach
                    </select>
                    @error('kdDokterDikonsuli') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Diagnosa Kerja <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm @error('diagnosaKerja') is-invalid @enderror"
                        wire:model.defer="diagnosaKerja" placeholder="Tulis diagnosa kerja">
                    @error('diagnosaKerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Uraian Konsultasi</label>
                    <textarea class="form-control form-control-sm" rows="3"
                        wire:model.defer="uraianKonsultasi" placeholder="Tulis uraian konsultasi"></textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-3">
            @if($editMode)
            <button type="button" class="btn btn-secondary btn-sm mr-1" wire:click="resetForm">Batal</button>
            @endif
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save"></i> {{ $editMode ? 'Ubah' : 'Simpan' }}
            </button>
        </div>
    </form>

    @if(count($daftarKonsultasi) > 0)
    <div class="table-responsive mt-2">
        <table class="table table-sm table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>No. Permintaan</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Dokter Konsul</th>
                    <th>Dokter Dikonsuli</th>
                    <th>Diagnosa</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daftarKonsultasi as $item)
                <tr>
                    <td>{{ $item->no_permintaan }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i') }}</td>
                    <td>{{ $item->jenis_permintaan }}</td>
                    <td>{{ $item->nm_dokter_konsul ?? '-' }}</td>
                    <td>{{ $item->nm_dokter_dikonsuli ?? '-' }}</td>
                    <td>{{ $item->diagnosa_kerja }}</td>
                    <td>
                        <span class="badge badge-{{ $item->status_jawab == 'Sudah Dijawab' ? 'success' : 'warning' }}">
                            {{ $item->status_jawab }}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-xs btn-info" wire:click="edit('{{ $item->no_permintaan }}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-danger" wire:click="konfirmasiHapus('{{ $item->no_permintaan }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
