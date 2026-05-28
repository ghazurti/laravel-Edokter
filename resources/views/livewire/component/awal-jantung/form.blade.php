<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input-datetime label="Tanggal" id="tanggal_jantung" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_jantung" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_jantung" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_jantung" model="keluhan_utama" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps_jantung" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Keluarga" id="rpk_jantung" model="rpk" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd_jantung" model="rpd" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penggunaan Obat" id="rpo_jantung" model="rpo" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Riwayat Alergi" id="alergi_jantung" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-md-3">
                <x-ui.input label="TD (mmHg)" id="td_jantung" model="td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="BB (Kg)" id="bb_jantung" model="bb" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="TB (cm)" id="tb_jantung" model="tb" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Suhu (°C)" id="suhu_jantung" model="suhu" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="Nadi (x/mnt)" id="nadi_jantung" model="nadi" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="RR (x/mnt)" id="rr_jantung" model="rr" />
            </div>
            <div class="col-md-4">
                <x-ui.select label="Keadaan Umum" id="keadaan_umum_jantung" model="keadaan_umum">
                    <option value="Sehat">Sehat</option>
                    <option value="Sakit Ringan">Sakit Ringan</option>
                    <option value="Sakit Sedang">Sakit Sedang</option>
                    <option value="Sakit Berat">Sakit Berat</option>
                </x-ui.select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="Nyeri" id="nyeri_jantung" model="nyeri" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Status Nutrisi" id="status_nutrisi_jantung" model="status_nutrisi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">III. PEMERIKSAAN SISTEM</h6>
        @php
            $statusOptions = ['Normal', 'Abnormal', 'Tidak Diperiksa'];
            $sistemFields = [
                ['id' => 'jantung',    'label' => 'Jantung',    'ket' => 'keterangan_jantung'],
                ['id' => 'paru',       'label' => 'Paru',       'ket' => 'keterangan_paru'],
                ['id' => 'ekstrimitas','label' => 'Ekstrimitas', 'ket' => 'keterangan_ekstrimitas'],
            ];
        @endphp
        @foreach($sistemFields as $field)
        <div class="row">
            <div class="col-md-4 col-5">
                <x-ui.select :label="$field['label']" :id="$field['id'].'_jantung'" :model="$field['id']">
                    @foreach($statusOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="col-md-8 col-7">
                <x-ui.input label="Keterangan" :id="$field['ket'].'_jantung'" :model="$field['ket']" />
            </div>
        </div>
        @endforeach
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Keterangan Pemeriksaan Fisik Lainnya" id="lainnya_jantung" model="lainnya" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Laboratorium" id="lab_jantung" model="lab" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="EKG" id="ekg_jantung" model="ekg" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Penunjang Lain" id="penunjang_lain_jantung" model="penunjang_lain" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">V. DIAGNOSIS / ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Kerja" id="diagnosis_jantung" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Banding" id="diagnosis2_jantung" model="diagnosis2" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Permasalahan" id="permasalahan_jantung" model="permasalahan" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Terapi / Pengobatan" id="terapi_jantung" model="terapi" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Tindakan / Rencana Tindakan" id="tindakan_jantung" model="tindakan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_jantung" model="edukasi" />
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
    $(".btn-awal-jantung").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-jantung").modal('show');
    });
</script>
@endpush
