<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input-datetime label="Tanggal" id="tanggal_urologi" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_urologi" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_urologi" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_urologi" model="keluhan_utama" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps_urologi" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Keluarga" id="rpk_urologi" model="rpk" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd_urologi" model="rpd" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penggunaan Obat" id="rpo_urologi" model="rpo" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Kebiasaan" id="riwayat_kebiasaan_urologi" model="riwayat_kebiasaan" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Operasi Urologi" id="riwayat_operasi_urologi_urologi" model="riwayat_operasi_urologi" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Riwayat Alergi" id="alergi_urologi" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-md-3">
                <x-ui.input label="TD (mmHg)" id="td_urologi" model="td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="BB (Kg)" id="bb_urologi" model="bb" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="TB (cm)" id="tb_urologi" model="tb" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Suhu (°C)" id="suhu_urologi" model="suhu" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="Nadi (x/mnt)" id="nadi_urologi" model="nadi" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="RR (x/mnt)" id="rr_urologi" model="rr" />
            </div>
            <div class="col-md-4">
                <x-ui.select label="Keadaan Umum" id="keadaan_umum_urologi" model="keadaan_umum">
                    <option value="Sehat">Sehat</option>
                    <option value="Sakit Ringan">Sakit Ringan</option>
                    <option value="Sakit Sedang">Sakit Sedang</option>
                    <option value="Sakit Berat">Sakit Berat</option>
                </x-ui.select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="Nyeri" id="nyeri_urologi" model="nyeri" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Status Nutrisi" id="status_nutrisi_urologi" model="status_nutrisi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">III. PEMERIKSAAN SISTEM</h6>
        @php
            $statusOptions = ['Normal', 'Abnormal', 'Tidak Diperiksa'];
            $sistemFields = [
                ['id' => 'thoraks',    'label' => 'Thoraks',    'ket' => 'keterangan_thoraks'],
                ['id' => 'abdomen',    'label' => 'Abdomen',    'ket' => 'keterangan_abdomen'],
                ['id' => 'ekstrimitas','label' => 'Ekstrimitas', 'ket' => 'keterangan_ekstrimitas'],
            ];
        @endphp
        @foreach($sistemFields as $field)
        <div class="row">
            <div class="col-md-4 col-5">
                <x-ui.select :label="$field['label']" :id="$field['id'].'_urologi'" :model="$field['id']">
                    @foreach($statusOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="col-md-8 col-7">
                <x-ui.input label="Keterangan" :id="$field['ket'].'_urologi'" :model="$field['ket']" />
            </div>
        </div>
        @endforeach

        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN KHUSUS UROLOGI</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="Nyeri Ketok CVA" id="nyeri_ketok_cva_urologi" model="nyeri_ketok_cva" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Genitalia Eksternal" id="genitalia_eksternal_urologi" model="genitalia_eksternal" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Colok Dubur" id="colok_dubur_urologi" model="colok_dubur" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Keterangan Pemeriksaan Fisik Lainnya" id="lainnya_urologi" model="lainnya" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">V. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Urinalisis" id="urinalisis_urologi" model="urinalisis" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Darah" id="darah_urologi" model="darah" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="USG Urologi" id="usg_urologi_urologi" model="usg_urologi" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Radiologi" id="radiologi_urologi" model="radiologi" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Penunjang Lain" id="penunjang_lain_urologi" model="penunjang_lain" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. DIAGNOSIS / ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Kerja" id="diagnosis_urologi" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Banding" id="diagnosis2_urologi" model="diagnosis2" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Permasalahan" id="permasalahan_urologi" model="permasalahan" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Terapi / Pengobatan" id="terapi_urologi" model="terapi" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Tindakan / Rencana Tindakan" id="tindakan_urologi" model="tindakan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VIII. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_urologi" model="edukasi" />
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
    $(".btn-awal-urologi").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-urologi").modal('show');
    });
</script>
@endpush
