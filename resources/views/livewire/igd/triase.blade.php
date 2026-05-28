<div>
    {{-- Status badges --}}
    @if($primerTanggal || $sekunderTanggal)
    <div class="d-flex gap-2 mb-2">
        @if($primerTanggal)
        <span class="badge badge-danger px-2 py-1">
            <i class="fas fa-check-circle mr-1"></i>
            Primer: {{ \Carbon\Carbon::parse($primerTanggal)->isoFormat('DD/MM HH:mm') }}
            &mdash; {{ $primerPetugas }}
        </span>
        @endif
        @if($sekunderTanggal)
        <span class="badge badge-warning px-2 py-1">
            <i class="fas fa-check-circle mr-1"></i>
            Sekunder: {{ \Carbon\Carbon::parse($sekunderTanggal)->isoFormat('DD/MM HH:mm') }}
            &mdash; {{ $sekunderPetugas }}
        </span>
        @endif
    </div>
    @endif

    {{-- Header: Cara Masuk, Transportasi, Alasan, Kasus --}}
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label class="small mb-1">Cara Masuk</label>
                <select wire:model="cara_masuk" class="form-control form-control-sm">
                    <option>Jalan</option>
                    <option>Brankar</option>
                    <option>Kursi Roda</option>
                    <option>Digendong</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label class="small mb-1">Alat Transportasi</label>
                <select wire:model="alat_transportasi" class="form-control form-control-sm">
                    <option value="-">-</option>
                    <option>AGD</option>
                    <option>Sendiri</option>
                    <option>Swasta</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label class="small mb-1">Alasan Kedatangan</label>
                <select wire:model="alasan_kedatangan" class="form-control form-control-sm">
                    <option value="-">-</option>
                    <option>Datang Sendiri</option>
                    <option>Polisi</option>
                    <option>Rujukan</option>
                    <option>Bidan</option>
                    <option>Puskesmas</option>
                    <option>Rumah Sakit</option>
                    <option>Poliklinik</option>
                    <option>Faskes Lain</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label class="small mb-1">Macam Kasus</label>
                <select wire:model="kode_kasus" class="form-control form-control-sm">
                    <option value="">-- Pilih --</option>
                    @foreach($masterKasus as $k)
                        <option value="{{ $k->kode_kasus }}">{{ $k->macam_kasus }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label class="small mb-1">Keterangan</label>
                <input wire:model="keterangan_kedatangan" type="text" class="form-control form-control-sm"
                    placeholder="Keterangan tambahan...">
            </div>
        </div>
    </div>

    {{-- Vital Signs --}}
    <div class="row">
        <div class="col-6 col-md-2">
            <div class="form-group">
                <label class="small mb-1">Suhu (°C)</label>
                <input wire:model="suhu" type="text" class="form-control form-control-sm" placeholder="36.5">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="form-group">
                <label class="small mb-1">Nyeri (0-10)</label>
                <input wire:model="nyeri" type="text" class="form-control form-control-sm" placeholder="0">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="form-group">
                <label class="small mb-1">Tensi (mmHg)</label>
                <input wire:model="tekanan_darah" type="text" class="form-control form-control-sm" placeholder="120/80">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="form-group">
                <label class="small mb-1">Nadi (/mnt)</label>
                <input wire:model="nadi" type="text" class="form-control form-control-sm" placeholder="80">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="form-group">
                <label class="small mb-1">Saturasi O₂ (%)</label>
                <input wire:model="saturasi_o2" type="text" class="form-control form-control-sm" placeholder="98">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="form-group">
                <label class="small mb-1">Respirasi (/mnt)</label>
                <input wire:model="pernapasan" type="text" class="form-control form-control-sm" placeholder="20">
            </div>
        </div>
    </div>

    {{-- Tabs: Triase Primer / Sekunder --}}
    <ul class="nav nav-tabs nav-sm mt-1" id="triaseTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active text-danger font-weight-bold" id="primer-tab" data-toggle="tab"
               href="#triasePrimer" role="tab">Triase Primer</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-warning font-weight-bold" id="sekunder-tab" data-toggle="tab"
               href="#triaseSekunder" role="tab">Triase Sekunder</a>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-3 bg-white" style="min-height:380px">

        {{-- === TRIASE PRIMER === --}}
        <div class="tab-pane fade show active" id="triasePrimer" role="tabpanel">
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="small mb-1">Keluhan Utama</label>
                        <textarea wire:model="keluhan_utama" class="form-control form-control-sm" rows="2"
                            placeholder="Keluhan utama pasien..."></textarea>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="small mb-1">Kebutuhan Khusus</label>
                        <select wire:model="kebutuhan_khusus" class="form-control form-control-sm">
                            <option value="-">-</option>
                            <option>UPPA</option>
                            <option>Airborne</option>
                            <option>Dekontaminan</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Pemeriksaan + Skala --}}
            <div class="row">
                <div class="col-md-4">
                    <label class="small font-weight-bold mb-1">Pemeriksaan</label>
                    <div class="list-group list-group-flush border">
                        @foreach($masterPemeriksaan as $pem)
                        @php
                            $hasSkala1 = $masterSkala1->where('kode_pemeriksaan', $pem->kode_pemeriksaan)->count() > 0;
                            $s1Count = collect($skala1Selected)->filter(function($k) use ($pem, $masterSkala1) {
                                return $masterSkala1->where('kode_pemeriksaan', $pem->kode_pemeriksaan)
                                    ->pluck('kode_skala1')->contains($k);
                            })->count();
                            $s2Count = collect($skala2Selected)->filter(function($k) use ($pem, $masterSkala2) {
                                return $masterSkala2->where('kode_pemeriksaan', $pem->kode_pemeriksaan)
                                    ->pluck('kode_skala2')->contains($k);
                            })->count();
                        @endphp
                        <button type="button"
                            wire:click="$set('selectedPemeriksaan', '{{ $pem->kode_pemeriksaan }}')"
                            class="list-group-item list-group-item-action py-2 px-3 d-flex justify-content-between align-items-center
                                {{ $selectedPemeriksaan == $pem->kode_pemeriksaan ? 'active' : '' }}">
                            <span class="small">{{ $pem->nama_pemeriksaan }}</span>
                            <span>
                                @if($s1Count > 0)
                                <span class="badge badge-danger badge-pill">{{ $s1Count }}</span>
                                @endif
                                @if($s2Count > 0)
                                <span class="badge badge-warning badge-pill">{{ $s2Count }}</span>
                                @endif
                            </span>
                        </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-outline card-danger mb-0">
                                <div class="card-header py-1 px-2 bg-danger text-white">
                                    <strong class="small">Skala 1 — Immediate/Segera</strong>
                                </div>
                                <div class="card-body p-2" style="min-height:200px">
                                    @forelse($masterSkala1->where('kode_pemeriksaan', $selectedPemeriksaan) as $item)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="skala1Selected"
                                            value="{{ $item->kode_skala1 }}"
                                            id="s1_{{ $item->kode_skala1 }}">
                                        <label class="form-check-label small text-danger" for="s1_{{ $item->kode_skala1 }}">
                                            {{ $item->pengkajian_skala1 }}
                                        </label>
                                    </div>
                                    @empty
                                    <p class="text-muted small mb-0">-</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-outline card-warning mb-0">
                                <div class="card-header py-1 px-2 bg-warning">
                                    <strong class="small">Skala 2 — Emergent/Gawat Darurat</strong>
                                </div>
                                <div class="card-body p-2" style="min-height:200px">
                                    @forelse($masterSkala2->where('kode_pemeriksaan', $selectedPemeriksaan) as $item)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="skala2Selected"
                                            value="{{ $item->kode_skala2 }}"
                                            id="s2_{{ $item->kode_skala2 }}">
                                        <label class="form-check-label small text-warning" for="s2_{{ $item->kode_skala2 }}">
                                            {{ $item->pengkajian_skala2 }}
                                        </label>
                                    </div>
                                    @empty
                                    <p class="text-muted small mb-0">-</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan + Plan + Simpan --}}
            <div class="row mt-2">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="small mb-1">Catatan</label>
                        <input wire:model="catatan" type="text" class="form-control form-control-sm"
                            placeholder="Catatan triase primer...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="small mb-1 d-block">Plan / Keputusan</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" wire:model="plan"
                            id="plan1" value="Ruang Resusitasi">
                        <label class="form-check-label small text-danger" for="plan1">Ruang Resusitasi</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" wire:model="plan"
                            id="plan2" value="Ruang Kritis">
                        <label class="form-check-label small text-warning" for="plan2">Ruang Kritis</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="simpanPrimer" class="btn btn-danger btn-sm w-100">
                        <i class="fas fa-save mr-1"></i> Simpan Triase Primer
                    </button>
                </div>
            </div>
        </div>

        {{-- === TRIASE SEKUNDER === --}}
        <div class="tab-pane fade" id="triaseSekunder" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="small mb-1">Anamnesa Singkat</label>
                        <textarea wire:model="anamnesa_singkat" class="form-control form-control-sm" rows="4"
                            placeholder="Anamnesa singkat pasien..."></textarea>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="small mb-1">Catatan</label>
                        <input wire:model="catatan_sekunder" type="text" class="form-control form-control-sm"
                            placeholder="Catatan triase sekunder...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="small mb-1 d-block">Plan / Keputusan</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" wire:model="plan_sekunder"
                            id="plans1" value="Zona Kuning">
                        <label class="form-check-label small text-warning" for="plans1">Zona Kuning</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" wire:model="plan_sekunder"
                            id="plans2" value="Zona Hijau">
                        <label class="form-check-label small text-success" for="plans2">Zona Hijau</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="simpanSekunder" class="btn btn-warning btn-sm w-100">
                        <i class="fas fa-save mr-1"></i> Simpan Triase Sekunder
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Riwayat triase --}}
    <hr class="mt-4">
    <div wire:ignore>
        <x-adminlte-callout theme="info" title="Riwayat Triase IGD">
            @php
                $headsTriase = ['Tanggal', 'Keluhan Utama', 'TD', 'Nadi', 'Suhu', 'Plan Primer', 'Plan Sekunder'];
                $configTriase = ['responsive' => true, 'order' => [[0, 'desc']]];
            @endphp
            <x-adminlte-datatable id="tableRiwayatTriaseIgd" :heads="$headsTriase"
                :config="$configTriase" head-theme="dark" striped hoverable bordered compressed>
                @foreach($riwayatTriase as $r)
                <tr>
                    <td class="align-middle">{{ \Carbon\Carbon::parse($r->tgl_registrasi)->isoFormat('DD/MM/YYYY') }}</td>
                    <td class="align-middle">{{ $r->keluhan_utama ?? '-' }}</td>
                    <td class="align-middle text-center">{{ $r->tekanan_darah ?? '-' }}</td>
                    <td class="align-middle text-center">{{ $r->nadi ?? '-' }}</td>
                    <td class="align-middle text-center">{{ $r->suhu ?? '-' }}</td>
                    <td class="align-middle">{{ $r->plan_primer ?? '-' }}</td>
                    <td class="align-middle">{{ $r->plan_sekunder ?? '-' }}</td>
                </tr>
                @endforeach
            </x-adminlte-datatable>
        </x-adminlte-callout>
    </div>
</div>
