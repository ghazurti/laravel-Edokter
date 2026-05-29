<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-lg fa-pills mr-1"></i>
            Permintaan Resep Pulang
            @if($noPermintaan)
                <span class="badge badge-warning ml-2">Draft: {{ $noPermintaan }}</span>
            @endif
        </h3>
        <div class="card-tools">
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg {{ $isCollapsed ? 'fa-plus' : 'fa-minus' }}"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="simpan">
            <div class="form-group row position-relative">
                <label class="col-sm-3 col-form-label">Cari Obat</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="text"
                               class="form-control"
                               wire:model.debounce.300ms="search"
                               placeholder="Ketik minimal 2 huruf nama / kode obat..."
                               @if($kodeBrng) readonly @endif>
                        @if($kodeBrng)
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" wire:click="batalPilih">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                    @error('kodeBrng') <span class="text-danger">{{ $message }}</span> @enderror

                    @if(count($hasilCari) > 0 && !$kodeBrng)
                    <div class="list-group mt-1 position-absolute w-75" style="z-index:9; max-height:240px; overflow-y:auto;">
                        @foreach($hasilCari as $brng)
                        <button type="button"
                                class="list-group-item list-group-item-action py-1"
                                wire:click="pilihObat('{{ $brng->kode_brng }}', '{{ addslashes($brng->nama_brng) }}')">
                            <small class="text-muted">{{ $brng->kode_brng }}</small>
                            &mdash; {{ $brng->nama_brng }} <span class="badge badge-light">{{ $brng->kode_sat }}</span>
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Jumlah</label>
                <div class="col-sm-3">
                    <input type="number" min="1" step="1" class="form-control" wire:model.defer="jml">
                    @error('jml') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <label class="col-sm-3 col-form-label">Aturan Pakai / Dosis</label>
                <div class="col-sm-3">
                    <input type="text" maxlength="150" class="form-control" wire:model.defer="dosis" placeholder="3x1 sehari sesudah makan">
                    @error('dosis') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex flex-row-reverse">
                <button type="submit" class="btn btn-primary" @if(!$kodeBrng) disabled @endif>
                    <i class="fas fa-plus mr-1"></i> Tambah
                </button>
            </div>
        </form>

        <hr>

        <h6>Item Permintaan Aktif</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-2">
                <thead class="bg-light">
                    <tr>
                        <th style="width:40%">Nama Obat</th>
                        <th class="text-center" style="width:15%">Jumlah</th>
                        <th style="width:30%">Aturan Pakai</th>
                        <th class="text-center" style="width:15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                    <tr>
                        <td>{{ $row->nama_brng }}</td>
                        <td class="text-center">{{ (int) $row->jml }} {{ $row->kode_sat }}</td>
                        <td>{{ $row->dosis }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger"
                                    onclick="confirmDeleteResepPulang('{{ $row->kode_brng }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted">Belum ada item. Cari & tambahkan obat di atas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(count($riwayat) > 0)
        <hr>
        <h6>Riwayat Permintaan</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>No. Permintaan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Dokter</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayat as $r)
                    <tr>
                        <td><code>{{ $r->no_permintaan }}</code></td>
                        <td>{{ \Carbon\Carbon::parse($r->tgl_permintaan)->format('d-m-Y') }}</td>
                        <td>{{ substr($r->jam, 0, 5) }}</td>
                        <td>{{ $r->kd_dokter }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $r->status === 'Sudah' ? 'success' : 'warning' }}">{{ $r->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@push('js')
<script>
    function confirmDeleteResepPulang(kode) {
        Swal.fire({
            title: 'Hapus item ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((r) => {
            if (r.isConfirmed) {
                Livewire.emit('deleteResepPulang', kode);
            }
        });
    }

    window.addEventListener('swal:success', e => {
        Swal.fire({ icon: 'success', title: e.detail.title, timer: 1500, showConfirmButton: false });
    });
    window.addEventListener('swal:error', e => {
        Swal.fire({ icon: 'error', title: e.detail.title });
    });
</script>
@endpush
