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
                <x-ui.input-datetime label="Tanggal" id="tanggal_bedahmulut" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_bedahmulut" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_bedahmulut" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_bedahmulut" model="keluhan_utama" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps_bedahmulut" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Keluarga" id="rpk_bedahmulut" model="rpk" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Riwayat Alergi" id="alergi_bedahmulut" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Keadaan Umum" id="keadaan_bedahmulut" model="keadaan">
                    <option value="Baik">Baik</option>
                    <option value="Sedang">Sedang</option>
                    <option value="Lemah">Lemah</option>
                    <option value="Buruk">Buruk</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Kesadaran" id="kesadaran_bedahmulut" model="kesadaran">
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Somnolen">Somnolen</option>
                    <option value="Sopor">Sopor</option>
                    <option value="Koma">Koma</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Nyeri" id="nyeri_bedahmulut" model="nyeri">
                    <option value="Tidak Nyeri">Tidak Nyeri</option>
                    <option value="Nyeri Ringan">Nyeri Ringan</option>
                    <option value="Nyeri Sedang">Nyeri Sedang</option>
                    <option value="Nyeri Berat">Nyeri Berat</option>
                    <option value="Nyeri Sangat Berat">Nyeri Sangat Berat</option>
                    <option value="Nyeri Tak Tertahankan">Nyeri Tak Tertahankan</option>
                </x-ui.select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.input label="TD (mmHg)" id="td_bedahmulut" model="td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Nadi (x/mnt)" id="nadi_bedahmulut" model="nadi" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Suhu (°C)" id="suhu_bedahmulut" model="suhu" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="RR (x/mnt)" id="rr_bedahmulut" model="rr" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="BB (Kg)" id="bb_bedahmulut" model="bb" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="TB (cm)" id="tb_bedahmulut" model="tb" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Status Nutrisi" id="status_nutrisi_bedahmulut" model="status_nutrisi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">III. PEMERIKSAAN SISTEM</h6>
        @php
            $pemFields = [
                ['id' => 'kulit',      'label' => 'Kulit',      'ket' => 'keterangan_kulit'],
                ['id' => 'kepala',     'label' => 'Kepala',     'ket' => 'keterangan_kepala'],
                ['id' => 'mata',       'label' => 'Mata',       'ket' => 'keterangan_mata'],
                ['id' => 'leher',      'label' => 'Leher',      'ket' => 'keterangan_leher'],
                ['id' => 'kelenjar',   'label' => 'Kelenjar',   'ket' => 'keterangan_kelenjar'],
                ['id' => 'dada',       'label' => 'Dada',       'ket' => 'keterangan_dada'],
                ['id' => 'perut',      'label' => 'Perut',      'ket' => 'keterangan_perut'],
                ['id' => 'ekstremitas','label' => 'Ekstremitas','ket' => 'keterangan_ekstremitas'],
            ];
        @endphp
        @foreach($pemFields as $field)
        <div class="row">
            <div class="col-md-3 col-4">
                <x-ui.select :label="$field['label']" :id="$field['id'].'_bedahmulut'" :model="$field['id']">
                    <option value="Ya">Ya</option>
                    <option value="Tidak">Tidak</option>
                </x-ui.select>
            </div>
            <div class="col-md-9 col-8">
                <x-ui.input label="Keterangan" :id="$field['ket'].'_bedahmulut'" :model="$field['ket']" />
            </div>
        </div>
        @endforeach

        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN EKSTRA & INTRA ORAL</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Pemeriksaan Ekstra Oral - Wajah" id="wajah_bedahmulut" model="wajah" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Pemeriksaan Intra Oral" id="intra_bedahmulut" model="intra" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Gigi Geligi" id="gigigeligi_bedahmulut" model="gigigeligi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">V. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Laboratorium" id="lab_bedahmulut" model="lab" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Radiologi" id="rad_bedahmulut" model="rad" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Penunjang Lain" id="penunjang_bedahmulut" model="penunjang" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. DIAGNOSIS / ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Kerja" id="diagnosis_bedahmulut" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Banding" id="diagnosis2_bedahmulut" model="diagnosis2" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Permasalahan" id="permasalahan_bedahmulut" model="permasalahan" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Terapi / Pengobatan" id="terapi_bedahmulut" model="terapi" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Tindakan / Rencana Tindakan" id="tindakan_bedahmulut" model="tindakan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VIII. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_bedahmulut" model="edukasi" />
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
    $(".btn-awal-bedah-mulut").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-bedah-mulut").modal('show');
    });
</script>
@endpush
