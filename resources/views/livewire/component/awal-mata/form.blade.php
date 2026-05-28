<div>
    <form wire:submit.prevent='simpan'>
        <div class="row mb-2">
            <div class="col-12">
                <button type="button" wire:click="ambilDariCppt" class="btn btn-info btn-sm">
                    <i class="fas fa-sync-alt"></i> Ambil dari CPPT
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input-datetime label="Tanggal" id="tanggal_mata" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_mata" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_mata" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_mata" model="keluhan_utama" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps_mata" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd_mata" model="rpd" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penggunaan Obat" id="rpo_mata" model="rpo" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.input label="Riwayat Alergi" id="alergi_mata" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-md-3">
                <x-ui.input label="Status / Keadaan Umum" id="status_mata" model="status" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="TD (mmHg)" id="td_mata" model="td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Nadi (x/mnt)" id="nadi_mata" model="nadi" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="RR (x/mnt)" id="rr_mata" model="rr" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="Suhu (°C)" id="suhu_mata" model="suhu" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Nyeri (NRS 0-10)" id="nyeri_mata" model="nyeri" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="BB (Kg)" id="bb_mata" model="bb" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">III. PEMERIKSAAN MATA</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Pemeriksaan</th>
                        <th class="text-center">Kanan (OD)</th>
                        <th class="text-center">Kiri (OS)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $eyeFields = [
                            ['key' => 'visus',  'label' => 'Visus'],
                            ['key' => 'cc',     'label' => 'Koreksi'],
                            ['key' => 'pal',    'label' => 'Palpebra'],
                            ['key' => 'con',    'label' => 'Konjungtiva'],
                            ['key' => 'cornea', 'label' => 'Kornea'],
                            ['key' => 'coa',    'label' => 'COA'],
                            ['key' => 'pupil',  'label' => 'Pupil'],
                            ['key' => 'lensa',  'label' => 'Lensa'],
                            ['key' => 'fundus', 'label' => 'Fundus'],
                            ['key' => 'papil',  'label' => 'Papil'],
                            ['key' => 'retina', 'label' => 'Retina'],
                            ['key' => 'makula', 'label' => 'Makula'],
                            ['key' => 'tio',    'label' => 'TIO'],
                            ['key' => 'mbo',    'label' => 'MBO'],
                        ];
                    @endphp
                    @foreach($eyeFields as $ef)
                    <tr>
                        <td>{{ $ef['label'] }}</td>
                        <td><x-ui.input :id="$ef['key'].'_kanan_mata'" :model="$ef['key'].'_kanan'" /></td>
                        <td><x-ui.input :id="$ef['key'].'_kiri_mata'" :model="$ef['key'].'_kiri'" /></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Laboratorium" id="lab_mata" model="lab" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Radiologi" id="rad_mata" model="rad" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Penunjang" id="penunjang_mata" model="penunjang" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Tes Khusus" id="tes_mata" model="tes" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Pemeriksaan Lain" id="pemeriksaan_mata" model="pemeriksaan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">V. DIAGNOSIS / ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Kerja" id="diagnosis_mata" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Banding" id="diagnosisbdg_mata" model="diagnosisbdg" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Permasalahan" id="permasalahan_mata" model="permasalahan" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Terapi / Pengobatan" id="terapi_mata" model="terapi" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Tindakan / Rencana Tindakan" id="tindakan_mata" model="tindakan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_mata" model="edukasi" />
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-6">
                <button type="button" wire:click='confirmHapus' class="btn btn-danger btn-block">Hapus</button>
            </div>
            <div class="col-6">
                <button type="submit" class="btn btn-primary btn-block">{{ $editMode ? 'Ubah' : 'Simpan' }}</button>
            </div>
        </div>
    </form>
</div>

@push('js')
<script>
    $(".btn-awal-mata").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-mata").modal('show');
    });
</script>
@endpush
