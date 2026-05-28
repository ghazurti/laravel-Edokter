<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input-datetime label="Tanggal" id="tanggal_kulitdankelamin" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_kulitdankelamin" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_kulitdankelamin" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_kulitdankelamin" model="keluhan_utama" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps_kulitdankelamin" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd_kulitdankelamin" model="rpd" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penggunaan Obat" id="rpo_kulitdankelamin" model="rpo" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Keluarga" id="rpk_kulitdankelamin" model="rpk" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Riwayat Alergi" id="alergi_kulitdankelamin" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Kesadaran" id="kesadaran_kulitdankelamin" model="kesadaran">
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Delirum">Delirum</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.input label="Status / Keadaan Umum" id="status_kulitdankelamin" model="status" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="GCS" id="gcs_kulitdankelamin" model="gcs" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.input label="TD (mmHg)" id="td_kulitdankelamin" model="td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Nadi (x/mnt)" id="nadi_kulitdankelamin" model="nadi" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Suhu (°C)" id="suhu_kulitdankelamin" model="suhu" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="RR (x/mnt)" id="rr_kulitdankelamin" model="rr" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="BB (Kg)" id="bb_kulitdankelamin" model="bb" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Nyeri (NRS 0-10)" id="nyeri_kulitdankelamin" model="nyeri" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">III. STATUS DERMATOLOGIS</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Status Dermatologis" id="statusderma_kulitdankelamin" model="statusderma" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Pemeriksaan Penunjang" id="pemeriksaan_kulitdankelamin" model="pemeriksaan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">V. DIAGNOSIS / ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Kerja" id="diagnosis_kulitdankelamin" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Banding" id="diagnosis2_kulitdankelamin" model="diagnosis2" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Permasalahan" id="permasalahan_kulitdankelamin" model="permasalahan" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Terapi / Pengobatan" id="terapi_kulitdankelamin" model="terapi" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Tindakan / Rencana Tindakan" id="tindakan_kulitdankelamin" model="tindakan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_kulitdankelamin" model="edukasi" />
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
    $(".btn-awal-kulitdankelamin").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-kulitdankelamin").modal('show');
    });
</script>
@endpush
