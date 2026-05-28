<div>
    {{-- Anamnesis --}}
    <h6 class="font-weight-bold text-danger">Anamnesis</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Cara Anamnesis</label>
                <select wire:model="anamnesis" class="form-control form-control-sm">
                    <option>Autoanamnesis</option>
                    <option>Alloanamnesis</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Hubungan (jika Alloanamnesis)</label>
                <input wire:model="hubungan" type="text" class="form-control form-control-sm" placeholder="Orang tua, suami, dll">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Alergi</label>
                <input wire:model="alergi" type="text" class="form-control form-control-sm" placeholder="Tidak Ada">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>Keluhan Utama</label>
                <textarea wire:model="keluhan_utama" class="form-control form-control-sm" rows="2"
                    placeholder="Keluhan utama pasien..."></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Riwayat Penyakit Sekarang (RPS)</label>
                <textarea wire:model="rps" class="form-control form-control-sm" rows="3"></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Riwayat Penyakit Dahulu (RPD)</label>
                <textarea wire:model="rpd" class="form-control form-control-sm" rows="3"></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Riwayat Penyakit Keluarga (RPK)</label>
                <textarea wire:model="rpk" class="form-control form-control-sm" rows="2"></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Riwayat Penggunaan Obat (RPO)</label>
                <textarea wire:model="rpo" class="form-control form-control-sm" rows="2"></textarea>
            </div>
        </div>
    </div>

    {{-- Keadaan Umum & Vital Sign --}}
    <hr>
    <h6 class="font-weight-bold text-danger">Keadaan Umum & Vital Sign</h6>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label>Keadaan Umum</label>
                <select wire:model="keadaan" class="form-control form-control-sm">
                    <option>Sehat</option>
                    <option>Sakit Ringan</option>
                    <option>Sakit Sedang</option>
                    <option>Sakit Berat</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>GCS</label>
                <input wire:model="gcs" type="text" class="form-control form-control-sm" placeholder="E4V5M6">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Kesadaran</label>
                <select wire:model="kesadaran" class="form-control form-control-sm">
                    <option>Compos Mentis</option>
                    <option>Apatis</option>
                    <option>Somnolen</option>
                    <option>Sopor</option>
                    <option>Koma</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label>TD (mmHg)</label>
                <input wire:model="td" type="text" class="form-control form-control-sm" placeholder="120/80">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Nadi (/mnt)</label>
                <input wire:model="nadi" type="text" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>RR (/mnt)</label>
                <input wire:model="rr" type="text" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Suhu (°C)</label>
                <input wire:model="suhu" type="text" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>SpO2 (%)</label>
                <input wire:model="spo" type="text" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label>BB (kg)</label>
                <input wire:model="bb" type="text" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label>TB (cm)</label>
                <input wire:model="tb" type="text" class="form-control form-control-sm">
            </div>
        </div>
    </div>

    {{-- Pemeriksaan Fisik --}}
    <hr>
    <h6 class="font-weight-bold text-danger">Pemeriksaan Fisik</h6>
    @php
        $organs = [
            'kepala'      => 'Kepala',
            'mata'        => 'Mata',
            'gigi'        => 'Gigi & Mulut',
            'leher'       => 'Leher',
            'thoraks'     => 'Thoraks',
            'abdomen'     => 'Abdomen',
            'genital'     => 'Genital',
            'ekstremitas' => 'Ekstremitas',
        ];
    @endphp
    <div class="row">
        @foreach($organs as $field => $label)
        <div class="col-md-3">
            <div class="form-group">
                <label>{{ $label }}</label>
                <select wire:model="{{ $field }}" class="form-control form-control-sm">
                    <option>Normal</option>
                    <option>Abnormal</option>
                    <option>Tidak Diperiksa</option>
                </select>
            </div>
        </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Keterangan Pemeriksaan Fisik</label>
                <textarea wire:model="ket_fisik" class="form-control form-control-sm" rows="3"></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Status Lokalis</label>
                <textarea wire:model="ket_lokalis" class="form-control form-control-sm" rows="3"></textarea>
            </div>
        </div>
    </div>

    {{-- Penunjang --}}
    <hr>
    <h6 class="font-weight-bold text-danger">Pemeriksaan Penunjang</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>EKG</label>
                <textarea wire:model="ekg" class="form-control form-control-sm" rows="2"></textarea>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Radiologi</label>
                <textarea wire:model="rad" class="form-control form-control-sm" rows="2"></textarea>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Laboratorium</label>
                <textarea wire:model="lab" class="form-control form-control-sm" rows="2"></textarea>
            </div>
        </div>
    </div>

    {{-- Diagnosis & Tatalaksana --}}
    <hr>
    <h6 class="font-weight-bold text-danger">Diagnosis & Tatalaksana</h6>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Diagnosis</label>
                <textarea wire:model="diagnosis" class="form-control form-control-sm" rows="3"></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Tatalaksana / Instruksi</label>
                <textarea wire:model="tata" class="form-control form-control-sm" rows="3"></textarea>
            </div>
        </div>
    </div>

    <button wire:click="simpan" class="btn btn-danger btn-sm">
        <i class="fas fa-save mr-1"></i> Simpan Penilaian Medis
    </button>

    {{-- Riwayat penilaian medis --}}
    <hr class="mt-4">
    <div wire:ignore>
        <x-adminlte-callout theme="info" title="Riwayat Penilaian Medis IGD">
            @php
                $headsPenilaian = ['Tanggal', 'Dokter', 'Keluhan Utama', 'Diagnosis', 'Tatalaksana'];
                $configPenilaian = ['responsive' => true, 'order' => [[0, 'desc']]];
            @endphp
            <x-adminlte-datatable id="tableRiwayatPenilaianIgd" :heads="$headsPenilaian"
                :config="$configPenilaian" head-theme="dark" striped hoverable bordered compressed>
                @foreach($listPenilaian as $item)
                <tr>
                    <td class="align-middle">{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('DD/MM/YYYY HH:mm') }}</td>
                    <td class="align-middle">{{ $item->nm_dokter ?? '-' }}</td>
                    <td class="align-middle">{{ $item->keluhan_utama ?? '-' }}</td>
                    <td class="align-middle">{{ $item->diagnosis ?? '-' }}</td>
                    <td class="align-middle">{{ $item->tata ?? '-' }}</td>
                </tr>
                @endforeach
            </x-adminlte-datatable>
        </x-adminlte-callout>
    </div>
</div>
