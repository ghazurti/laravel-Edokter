<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input-datetime label="Tanggal" id="tanggal_penyakitmulut" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_penyakitmulut" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_penyakitmulut" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_penyakitmulut" model="keluhan_utama" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps_penyakitmulut" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Keluarga" id="rpk_penyakitmulut" model="rpk" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Riwayat Alergi" id="alergi_penyakitmulut" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Keadaan Umum" id="keadaan_penyakitmulut" model="keadaan">
                    <option value="Baik">Baik</option>
                    <option value="Sedang">Sedang</option>
                    <option value="Lemah">Lemah</option>
                    <option value="Buruk">Buruk</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Kesadaran" id="kesadaran_penyakitmulut" model="kesadaran">
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Somnolen">Somnolen</option>
                    <option value="Sopor">Sopor</option>
                    <option value="Koma">Koma</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Nyeri" id="nyeri_penyakitmulut" model="nyeri">
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
                <x-ui.input label="TD (mmHg)" id="td_penyakitmulut" model="td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Nadi (x/mnt)" id="nadi_penyakitmulut" model="nadi" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Suhu (°C)" id="suhu_penyakitmulut" model="suhu" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="RR (x/mnt)" id="rr_penyakitmulut" model="rr" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="BB (Kg)" id="bb_penyakitmulut" model="bb" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="TB (cm)" id="tb_penyakitmulut" model="tb" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Status Nutrisi" id="status_nutrisi_penyakitmulut" model="status_nutrisi" />
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
                <x-ui.select :label="$field['label']" :id="$field['id'].'_penyakitmulut'" :model="$field['id']">
                    <option value="Ya">Ya</option>
                    <option value="Tidak">Tidak</option>
                </x-ui.select>
            </div>
            <div class="col-md-9 col-8">
                <x-ui.input label="Keterangan" :id="$field['ket'].'_penyakitmulut'" :model="$field['ket']" />
            </div>
        </div>
        @endforeach

        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN EKSTRA & INTRA ORAL</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Pemeriksaan Ekstra Oral - Wajah" id="wajah_penyakitmulut" model="wajah" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Pemeriksaan Intra Oral" id="intra_penyakitmulut" model="intra" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Gigi Geligi" id="gigigeligi_penyakitmulut" model="gigigeligi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">V. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Laboratorium" id="lab_penyakitmulut" model="lab" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Radiologi" id="rad_penyakitmulut" model="rad" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Penunjang Lain" id="penunjang_penyakitmulut" model="penunjang" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. DIAGNOSIS / ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Kerja" id="diagnosis_penyakitmulut" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Banding" id="diagnosis2_penyakitmulut" model="diagnosis2" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Permasalahan" id="permasalahan_penyakitmulut" model="permasalahan" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Terapi / Pengobatan" id="terapi_penyakitmulut" model="terapi" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Tindakan / Rencana Tindakan" id="tindakan_penyakitmulut" model="tindakan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VIII. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_penyakitmulut" model="edukasi" />
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
    $(".btn-awal-penyakit-mulut").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-penyakit-mulut").modal('show');
    });
</script>
@endpush
