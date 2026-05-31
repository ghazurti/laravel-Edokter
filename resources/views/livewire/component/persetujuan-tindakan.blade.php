<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-lg fa-file-signature mr-1"></i>
            Persetujuan / Penolakan Tindakan
        </h3>
        <div class="card-tools">
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg {{ $isCollapsed ? 'fa-plus' : 'fa-minus' }}"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="simpan">
            {{-- Template --}}
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Template</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <select class="form-control" wire:model="kodeTemplate">
                            <option value="">— pilih template (opsional) —</option>
                            @foreach($templates as $t)
                            <option value="{{ $t->kode_template }}">{{ $t->kode_template }} — {{ \Illuminate\Support\Str::limit($t->tindakan, 50) }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" wire:click="pilihTemplate">Isi dari template</button>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-muted mb-2"><small>Centang tiap item yang sudah dijelaskan & dipahami.</small></p>

            @php
                $items = [
                    ['Diagnosa','diagnosa','diagnosaK'],
                    ['Dasar Diagnosis / Indikasi','indikasi','indikasiK'],
                    ['Tindakan Kedokteran','tindakan','tindakanK'],
                    ['Tata Cara','tataCara','tataCaraK'],
                    ['Tujuan','tujuan','tujuanK'],
                    ['Risiko','risiko','risikoK'],
                    ['Komplikasi','komplikasi','komplikasiK'],
                    ['Prognosis','prognosis','prognosisK'],
                    ['Alternatif & Risikonya','alternatif','alternatifK'],
                    ['Lain-lain','lainLain','lainLainK'],
                ];
            @endphp

            @foreach($items as [$label, $field, $konf])
            <div class="form-group row mb-2">
                <label class="col-sm-3 col-form-label">{{ $label }}</label>
                <div class="col-sm-8">
                    <textarea class="form-control form-control-sm" rows="1" wire:model.defer="{{ $field }}"></textarea>
                    @error($field) <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="col-sm-1 text-center pt-1">
                    <input type="checkbox" wire:model.defer="{{ $konf }}" title="Sudah dijelaskan">
                </div>
            </div>
            @endforeach

            <div class="form-group row mb-2">
                <label class="col-sm-3 col-form-label">Perkiraan Biaya</label>
                <div class="col-sm-8">
                    <input type="number" min="0" class="form-control form-control-sm" wire:model.defer="biaya">
                </div>
                <div class="col-sm-1 text-center pt-1">
                    <input type="checkbox" wire:model.defer="biayaK" title="Sudah dijelaskan">
                </div>
            </div>

            <hr>
            <h6><i class="fas fa-user mr-1"></i> Penerima Informasi</h6>
            <div class="form-group row mb-2">
                <label class="col-sm-3 col-form-label">Hubungan</label>
                <div class="col-sm-4">
                    <select class="form-control form-control-sm" wire:model="hubungan">
                        @foreach(['Diri Sendiri','Orang Tua','Anak','Saudara Kandung','Teman','Lain-lain'] as $h)
                        <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="form-control form-control-sm" placeholder="Alasan diwakilkan (bila bukan diri sendiri)" wire:model.defer="alasanDiwakilkan">
                </div>
            </div>
            <div class="form-group row mb-2">
                <label class="col-sm-3 col-form-label">Nama Penerima</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control form-control-sm" wire:model.defer="penerima">
                    @error('penerima') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="col-sm-2">
                    <select class="form-control form-control-sm" wire:model.defer="jkPenerima">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Umur" wire:model.defer="umurPenerima">
                </div>
            </div>
            <div class="form-group row mb-2">
                <label class="col-sm-3 col-form-label">Tgl Lahir / No. HP</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control form-control-sm" wire:model.defer="tglLahirPenerima">
                </div>
                <div class="col-sm-5">
                    <input type="text" class="form-control form-control-sm" placeholder="No. HP" wire:model.defer="noHp">
                </div>
            </div>
            <div class="form-group row mb-2">
                <label class="col-sm-3 col-form-label">Alamat</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" wire:model.defer="alamatPenerima">
                </div>
            </div>

            <hr>
            <div class="form-group row mb-2 position-relative">
                <label class="col-sm-3 col-form-label">Petugas Pencatat</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm"
                               wire:model.debounce.300ms="searchPetugas"
                               placeholder="Cari nama/NIP perawat pencatat..."
                               @if($nip) readonly @endif>
                        @if($nip)
                        <div class="input-group-append">
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="batalPetugas"><i class="fas fa-times"></i></button>
                        </div>
                        @endif
                    </div>
                    @error('nip') <span class="text-danger small">{{ $message }}</span> @enderror
                    @if(count($hasilPetugas) > 0 && !$nip)
                    <div class="list-group position-absolute w-75" style="z-index:9; max-height:200px; overflow-y:auto;">
                        @foreach($hasilPetugas as $pg)
                        <button type="button" class="list-group-item list-group-item-action py-1"
                                wire:click="pilihPetugas('{{ $pg->nip }}', '{{ addslashes($pg->nama) }}')">
                            <small class="text-muted">{{ $pg->nip }}</small> — {{ $pg->nama }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <div class="form-group row mb-2">
                <label class="col-sm-3 col-form-label">Pernyataan</label>
                <div class="col-sm-5">
                    <div class="btn-group btn-group-toggle w-100">
                        <label class="btn btn-outline-success {{ $pernyataan === 'Persetujuan' ? 'active' : '' }}">
                            <input type="radio" wire:model="pernyataan" value="Persetujuan"> Setuju
                        </label>
                        <label class="btn btn-outline-danger {{ $pernyataan === 'Penolakan' ? 'active' : '' }}">
                            <input type="radio" wire:model="pernyataan" value="Penolakan"> Menolak
                        </label>
                    </div>
                </div>
                <label class="col-sm-2 col-form-label text-right">Saksi Kel.</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control form-control-sm" wire:model.defer="saksiKeluarga">
                </div>
            </div>

            <div class="d-flex flex-row-reverse">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>

        <hr>
        <h6>Riwayat Persetujuan/Penolakan</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>No. Pernyataan</th>
                        <th>Tanggal</th>
                        <th>Tindakan</th>
                        <th>Penerima</th>
                        <th class="text-center">Pernyataan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                    <tr>
                        <td><code>{{ $r->no_pernyataan }}</code></td>
                        <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($r->tindakan, 40) }}</td>
                        <td>{{ $r->penerima_informasi }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $r->pernyataan === 'Persetujuan' ? 'success' : ($r->pernyataan === 'Penolakan' ? 'danger' : 'secondary') }}">
                                {{ $r->pernyataan }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ url('cetak/persetujuan-tindakan/'.$r->no_pernyataan) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Cetak">
                                <i class="fas fa-print"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDeletePersetujuan('{{ $r->no_pernyataan }}')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
    function confirmDeletePersetujuan(no) {
        Swal.fire({
            title: 'Hapus pernyataan ini?', icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Hapus', cancelButtonText: 'Batal',
        }).then((r) => { if (r.isConfirmed) Livewire.emit('deletePersetujuan', no); });
    }
    window.addEventListener('swal', function (e) { Swal.fire(e.detail); });
</script>
@endpush
