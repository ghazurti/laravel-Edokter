<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input-datetime label="Tanggal" id="tanggal_rehabmedik" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_rehabmedik" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_rehabmedik" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_rehabmedik" model="keluhan_utama" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps_rehabmedik" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd_rehabmedik" model="rpd" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Riwayat Alergi" id="alergi_rehabmedik" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Kesadaran" id="kesadaran_rehabmedik" model="kesadaran">
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Delirum">Delirum</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Nyeri" id="nyeri_rehabmedik" model="nyeri">
                    <option value="Tidak Nyeri">Tidak Nyeri</option>
                    <option value="Nyeri Sedang">Nyeri Sedang</option>
                    <option value="Nyeri Sangat Hebat">Nyeri Sangat Hebat</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Skala Nyeri (0-10)" id="skala_nyeri_rehabmedik" model="skala_nyeri">
                    @foreach(range(0, 10) as $i)
                    <option value="{{ $i }}">{{ $i }}</option>
                    @endforeach
                </x-ui.select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.input label="TD (mmHg)" id="td_rehabmedik" model="td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Nadi (x/mnt)" id="nadi_rehabmedik" model="nadi" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Suhu (°C)" id="suhu_rehabmedik" model="suhu" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="RR (x/mnt)" id="rr_rehabmedik" model="rr" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="BB (Kg)" id="bb_rehabmedik" model="bb" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">III. PEMERIKSAAN SISTEM</h6>
        @php
            $statusOptions = ['Normal', 'Abnormal', 'Tidak Diperiksa'];
            $sistemFields = [
                ['id' => 'kepala',     'label' => 'Kepala',           'ket' => 'keterangan_kepala'],
                ['id' => 'thoraks',    'label' => 'Thoraks',          'ket' => 'keterangan_thoraks'],
                ['id' => 'abdomen',    'label' => 'Abdomen',          'ket' => 'keterangan_abdomen'],
                ['id' => 'ekstremitas','label' => 'Ekstremitas',      'ket' => 'keterangan_ekstremitas'],
                ['id' => 'columna',    'label' => 'Columna Vertebra', 'ket' => 'keterangan_columna'],
                ['id' => 'muskulos',   'label' => 'Muskuloskeletal',  'ket' => 'keterangan_muskulos'],
            ];
        @endphp
        @foreach($sistemFields as $field)
        <div class="row">
            <div class="col-md-4 col-5">
                <x-ui.select :label="$field['label']" :id="$field['id'].'_rehabmedik'" :model="$field['id']">
                    @foreach($statusOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="col-md-8 col-7">
                <x-ui.input label="Keterangan" :id="$field['ket'].'_rehabmedik'" :model="$field['ket']" />
            </div>
        </div>
        @endforeach
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Keterangan Pemeriksaan Fisik Lainnya" id="lainnya_rehabmedik" model="lainnya" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">IV. ASESMEN RISIKO</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Risiko Jatuh" id="resiko_jatuh_rehabmedik" model="resiko_jatuh">
                    <option value="Tidak Berisiko">Tidak Berisiko</option>
                    <option value="Berisiko Sedang">Berisiko Sedang</option>
                    <option value="Berisiko Tinggi">Berisiko Tinggi</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Risiko Nutrisional" id="resiko_nutrisional_rehabmedik" model="resiko_nutrisional">
                    <option value="Tidak Berisiko Malnutrisi">Tidak Berisiko Malnutrisi</option>
                    <option value="Berisiko Malnutrisi">Berisiko Malnutrisi</option>
                    <option value="Malnutrisi">Malnutrisi</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Kebutuhan Fungsional" id="kebutuhan_fungsional_rehabmedik" model="kebutuhan_fungsional">
                    <option value="Tidak Perlu Bantuan">Tidak Perlu Bantuan</option>
                    <option value="Perlu Bantuan">Perlu Bantuan</option>
                    <option value="Perlu Bantuan Total">Perlu Bantuan Total</option>
                </x-ui.select>
            </div>
        </div>

        <h6 class="mt-3 text-bold">V. DIAGNOSIS</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosa Medis" id="diagnosa_medis_rehabmedik" model="diagnosa_medis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosa Fungsi" id="diagnosa_fungsi_rehabmedik" model="diagnosa_fungsi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Penunjang Lain" id="penunjang_lain_rehabmedik" model="penunjang_lain" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. PROGRAM TERAPI</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="Fisioterapi" id="fisio_rehabmedik" model="fisio" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Terapi Okupasi" id="okupasi_rehabmedik" model="okupasi" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Terapi Wicara" id="wicara_rehabmedik" model="wicara" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="Akupuntur" id="akupuntur_rehabmedik" model="akupuntur" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Tatalaksana Lain" id="tatalain_rehabmedik" model="tatalain" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Frekuensi Terapi" id="frekuensi_terapi_rehabmedik" model="frekuensi_terapi" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="Tgl. Fisioterapi" id="fisioterapi_rehabmedik" model="fisioterapi" type="date" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Tgl. Terapi Okupasi" id="terapi_okupasi_rehabmedik" model="terapi_okupasi" type="date" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Tgl. Terapi Wicara" id="terapi_wicara_rehabmedik" model="terapi_wicara" type="date" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="Tgl. Terapi Akupuntur" id="terapi_akupuntur_rehabmedik" model="terapi_akupuntur" type="date" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Tgl. Terapi Lainnya" id="terapi_lainnya_rehabmedik" model="terapi_lainnya" type="date" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VIII. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_rehabmedik" model="edukasi" />
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
    $(".btn-awal-rehab-medik").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-rehab-medik").modal('show');
    });
</script>
@endpush
