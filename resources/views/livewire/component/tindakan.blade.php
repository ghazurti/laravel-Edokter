<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-lg fa-hand-holding-medical mr-1"></i>
            Tindakan / Prosedur
            <span class="badge badge-light ml-2">{{ $isRanap ? 'Ranap' : 'Ralan' }}</span>
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
                <label class="col-sm-3 col-form-label">Cari Tindakan</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="text"
                               class="form-control"
                               wire:model.debounce.300ms="search"
                               placeholder="Ketik minimal 2 huruf nama / kode tindakan..."
                               @if($kdJenisPrw) readonly @endif>
                        <div class="input-group-append">
                            @if($kdJenisPrw)
                            <button type="button" class="btn btn-outline-secondary" wire:click="batalPilih">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                            <button type="submit" class="btn btn-primary" @if(!$kdJenisPrw) disabled @endif>
                                <i class="fas fa-plus mr-1"></i> Tambah
                            </button>
                        </div>
                    </div>
                    @error('kdJenisPrw') <span class="text-danger">{{ $message }}</span> @enderror

                    @if(count($hasilCari) > 0 && !$kdJenisPrw)
                    <div class="list-group mt-1 position-absolute w-100" style="z-index:9; max-height:260px; overflow-y:auto;">
                        @foreach($hasilCari as $t)
                        <button type="button"
                                class="list-group-item list-group-item-action py-1 d-flex justify-content-between"
                                wire:click="pilihTindakan('{{ $t->kd_jenis_prw }}', '{{ addslashes($t->nm_perawatan) }}')">
                            <span><small class="text-muted">{{ $t->kd_jenis_prw }}</small> &mdash; {{ $t->nm_perawatan }}</span>
                            <span class="badge badge-success">Rp {{ number_format((float) $t->total_byrdr, 0, ',', '.') }}</span>
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </form>

        <hr>

        <h6>Daftar Tindakan</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th style="width:35%">Tindakan</th>
                        <th class="text-center" style="width:15%">Tanggal</th>
                        <th style="width:25%">Dokter</th>
                        <th class="text-right" style="width:15%">Biaya</th>
                        <th class="text-center" style="width:10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                    <tr>
                        <td>{{ $row->nm_perawatan ?? $row->kd_jenis_prw }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($row->tgl_perawatan)->format('d-m-Y') }} {{ substr($row->jam_rawat ?? '', 0, 5) }}</td>
                        <td>{{ $row->nm_dokter ?? '-' }}</td>
                        <td class="text-right">{{ number_format((float) $row->biaya_rawat, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($row->tgl_perawatan == date('Y-m-d'))
                            <button type="button" class="btn btn-sm btn-danger"
                                    onclick="confirmDeleteTindakan('{{ $row->kd_jenis_prw }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                            @else
                            <span class="text-muted">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">Belum ada tindakan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
    function confirmDeleteTindakan(kode) {
        Swal.fire({
            title: 'Hapus tindakan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((r) => {
            if (r.isConfirmed) {
                Livewire.emit('deleteTindakan', kode);
            }
        });
    }

    window.addEventListener('swal', function (e) {
        Swal.fire(e.detail);
    });
</script>
@endpush
