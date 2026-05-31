@php
    $badge = function ($h) {
        if (!$h) return 'secondary';
        if (stripos($h, 'tinggi') !== false) return 'danger';
        if (stripos($h, 'sedang') !== false || stripos($h, 'menengah') !== false) return 'warning';
        return 'success';
    };
    $live = $def['total_col'] ? '' : '.defer';
    $latest = $riwayat->first();
@endphp
<div @if($isCollapsed) class="card card-info card-outline collapsed-card mb-2" @else class="card card-info card-outline mb-2" @endif>
    <div class="card-header py-2">
        <h3 class="card-title">
            <i class="{{ $def['icon'] }} mr-1"></i> {{ $def['judul'] }}
            @if($readonly)
                <span class="badge badge-light border ml-1"><i class="fas fa-eye"></i> review</span>
                @if($latest && $def['hasil_col'])
                    <span class="badge badge-{{ $badge($latest->hasil ?? null) }} ml-1">{{ $latest->hasil }}@if($def['total_col']) ({{ $latest->total }})@endif</span>
                @endif
            @elseif($def['total_col'] && !$isCollapsed)
                <span class="badge badge-{{ $badge($hasil) }} ml-2">{{ $hasil }} ({{ $total }})</span>
            @endif
        </h3>
        <div class="card-tools">
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas {{ $isCollapsed ? 'fa-plus' : 'fa-minus' }}"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($readonly)
            {{-- ===== MODE REVIEW (diisi perawat di Khanza) ===== --}}
            <div class="alert alert-secondary py-2 mb-2">
                <i class="fas fa-info-circle mr-1"></i>
                Diisi oleh <strong>perawat</strong> pada asesmen keperawatan (Khanza). Tampilan ini untuk <strong>review dokter</strong>.
            </div>
        @else
            {{-- ===== MODE INPUT (dokter mengisi, mis. nyeri) ===== --}}
            <div class="form-group row mb-3 position-relative">
                <label class="col-sm-3 col-form-label">Petugas Pencatat <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm"
                               wire:model.debounce.300ms="searchPetugas"
                               placeholder="Cari nama/NIP petugas pencatat..."
                               @if($nip) readonly @endif>
                        @if($nip)
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" wire:click="batalPetugas"><i class="fas fa-times"></i></button>
                        </div>
                        @endif
                    </div>
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

            <div class="table-responsive">
                <table class="table table-sm mb-2">
                    <tbody>
                        @foreach($def['baris'] as $b)
                        @php $tipe = $b['tipe'] ?? 'pilih'; @endphp
                        <tr>
                            <td style="width:45%; vertical-align:middle;">{{ $b['label'] }}</td>
                            <td>
                                @if($tipe === 'teks')
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm" wire:model.defer="jawab.{{ $b['col'] }}">
                                        @if(!empty($b['suffix']))
                                        <div class="input-group-append"><span class="input-group-text">{{ $b['suffix'] }}</span></div>
                                        @endif
                                    </div>
                                @else
                                    <select class="form-control form-control-sm" wire:model{{ $live }}="jawab.{{ $b['col'] }}">
                                        @foreach($opsiBaris[$b['col']] ?? [] as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </td>
                            @if(!empty($b['skor_col']))
                                @php
                                    $idx = array_search($jawab[$b['col']] ?? null, $opsiBaris[$b['col']] ?? [], true);
                                    $sc = ($idx !== false && isset($b['skor'][$idx])) ? $b['skor'][$idx] : 0;
                                @endphp
                                <td class="text-center" style="width:60px; vertical-align:middle;">
                                    <span class="badge badge-light border">{{ $sc }}</span>
                                </td>
                            @elseif(!collect($def['baris'])->every(fn($x) => empty($x['skor_col'])))
                                <td style="width:60px;"></td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($def['total_col'])
            <div class="alert alert-{{ $badge($hasil) }} py-2 mb-2">
                <strong>Total skor: {{ $total }}</strong> &mdash; {{ $hasil }}
                @if($saran)<br><small>{{ $saran }}</small>@endif
            </div>
            @endif

            <div class="d-flex flex-row-reverse">
                <button type="button" class="btn btn-primary btn-sm" wire:click="simpan" wire:loading.attr="disabled">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
            <hr class="my-2">
        @endif

        {{-- Riwayat / hasil penilaian --}}
        <h6 class="mb-1"><i class="fas fa-history mr-1"></i> {{ $readonly ? 'Hasil Penilaian' : 'Riwayat Penilaian' }}</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Tanggal</th>
                        @if($def['total_col'])<th class="text-center">Total</th>@endif
                        @if($def['hasil_col'])<th>Hasil</th>@endif
                        <th>Petugas</th>
                        @unless($readonly)<th class="text-center" style="width:60px;">Aksi</th>@endunless
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y H:i') }}</td>
                        @if($def['total_col'])<td class="text-center">{{ $r->total }}</td>@endif
                        @if($def['hasil_col'])
                            <td><span class="badge badge-{{ $badge($r->hasil ?? null) }}">{{ \Illuminate\Support\Str::limit($r->hasil, 60) }}</span></td>
                        @endif
                        <td><small>{{ $r->petugas ?? '-' }}</small></td>
                        @unless($readonly)
                        <td class="text-center">
                            <button type="button" class="btn btn-xs btn-danger"
                                    onclick="hapusRisiko(@this, '{{ $r->tanggal }}')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                        @endunless
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-2"><small>{{ $readonly ? 'Belum diisi perawat.' : 'Belum ada penilaian.' }}</small></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
