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
                <x-ui.input-datetime label="Tanggal" id="tanggal_gdpsikiatri" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_gdpsikiatri" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_gdpsikiatri" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. KELUHAN & RIWAYAT</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_gdpsikiatri" model="keluhan_utama" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Gejala Menyertai" id="gejala_menyertai_gdpsikiatri" model="gejala_menyertai" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Faktor Pencetus" id="faktor_pencetus_gdpsikiatri" model="faktor_pencetus" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. RIWAYAT PSIKIATRI</h6>
        <div class="row">
            <div class="col-md-3">
                <x-ui.select label="Riwayat Penyakit Dahulu" id="riwayat_penyakit_dahulu_gdpsikiatri" model="riwayat_penyakit_dahulu">
                    <option value="Tidak Ada">Tidak Ada</option>
                    <option value="Ada">Ada</option>
                </x-ui.select>
            </div>
            <div class="col-md-9">
                <x-ui.input label="Keterangan" id="keterangan_riwayat_penyakit_dahulu_gdpsikiatri" model="keterangan_riwayat_penyakit_dahulu" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Riwayat Kehamilan" id="riwayat_kehamilan_gdpsikiatri" model="riwayat_kehamilan" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.select label="Riwayat Sosial" id="riwayat_sosial_gdpsikiatri" model="riwayat_sosial">
                    <option value="Bergaul">Bergaul</option>
                    <option value="Tidak Bergaul">Tidak Bergaul</option>
                    <option value="Lain-lain">Lain-lain</option>
                </x-ui.select>
            </div>
            <div class="col-md-9">
                <x-ui.input label="Keterangan" id="keterangan_riwayat_sosial_gdpsikiatri" model="keterangan_riwayat_sosial" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.select label="Riwayat Pekerjaan" id="riwayat_pekerjaan_gdpsikiatri" model="riwayat_pekerjaan">
                    <option value="Bekerja">Bekerja</option>
                    <option value="Tidak Bekerja">Tidak Bekerja</option>
                    <option value="Ganti-gantian Pekerjaan">Ganti-gantian Pekerjaan</option>
                </x-ui.select>
            </div>
            <div class="col-md-9">
                <x-ui.input label="Keterangan" id="keterangan_riwayat_pekerjaan_gdpsikiatri" model="keterangan_riwayat_pekerjaan" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Riwayat Obat yang Diminum" id="riwayat_obat_diminum_gdpsikiatri" model="riwayat_obat_diminum" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.input label="Faktor Kepribadian Premorbid" id="faktor_kepribadian_premorbid_gdpsikiatri" model="faktor_kepribadian_premorbid" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.select label="Faktor Keturunan" id="faktor_keturunan_gdpsikiatri" model="faktor_keturunan">
                    <option value="Tidak Ada">Tidak Ada</option>
                    <option value="Ada">Ada</option>
                </x-ui.select>
            </div>
            <div class="col-md-9">
                <x-ui.input label="Keterangan" id="keterangan_faktor_keturunan_gdpsikiatri" model="keterangan_faktor_keturunan" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.select label="Faktor Organik" id="faktor_organik_gdpsikiatri" model="faktor_organik">
                    <option value="Tidak Ada">Tidak Ada</option>
                    <option value="Ada">Ada</option>
                </x-ui.select>
            </div>
            <div class="col-md-9">
                <x-ui.input label="Keterangan" id="keterangan_faktor_organik_gdpsikiatri" model="keterangan_faktor_organik" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.input label="Riwayat Alergi" id="riwayat_alergi_gdpsikiatri" model="riwayat_alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">III. PEMERIKSAAN FISIK MASUK</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Kesadaran" id="fisik_kesadaran_gdpsikiatri" model="fisik_kesadaran">
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Somnolen">Somnolen</option>
                    <option value="Sopor">Sopor</option>
                    <option value="Koma">Koma</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Nyeri" id="fisik_nyeri_gdpsikiatri" model="fisik_nyeri">
                    <option value="Tidak Nyeri">Tidak Nyeri</option>
                    <option value="Nyeri Ringan">Nyeri Ringan</option>
                    <option value="Nyeri Sedang">Nyeri Sedang</option>
                    <option value="Nyeri Berat">Nyeri Berat</option>
                    <option value="Nyeri Sangat Berat">Nyeri Sangat Berat</option>
                    <option value="Nyeri Tak Tertahankan">Nyeri Tak Tertahankan</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.input label="GCS" id="fisik_gcs_gdpsikiatri" model="fisik_gcs" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.input label="TD (mmHg)" id="fisik_td_gdpsikiatri" model="fisik_td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Nadi (x/mnt)" id="fisik_nadi_gdpsikiatri" model="fisik_nadi" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Suhu (°C)" id="fisik_suhu_gdpsikiatri" model="fisik_suhu" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="RR (x/mnt)" id="fisik_rr_gdpsikiatri" model="fisik_rr" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="BB (Kg)" id="fisik_bb_gdpsikiatri" model="fisik_bb" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="TB (cm)" id="fisik_tb_gdpsikiatri" model="fisik_tb" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Status Nutrisi" id="fisik_status_nutrisi_gdpsikiatri" model="fisik_status_nutrisi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">IV. STATUS KELAINAN</h6>
        @php
            $kelainanFields = [
                ['id' => 'status_kelainan_kepala',       'label' => 'Kepala',       'ket' => 'keterangan_status_kelainan_kepala'],
                ['id' => 'status_kelainan_leher',        'label' => 'Leher',        'ket' => 'keterangan_status_kelainan_leher'],
                ['id' => 'status_kelainan_dada',         'label' => 'Dada',         'ket' => 'keterangan_status_kelainan_dada'],
                ['id' => 'status_kelainan_perut',        'label' => 'Perut',        'ket' => 'keterangan_status_kelainan_perut'],
                ['id' => 'status_kelainan_anggota_gerak','label' => 'Anggota Gerak','ket' => 'keterangan_status_kelainan_anggota_gerak'],
            ];
            $kelainanOptions = ['Normal', 'Abnormal', 'Tidak Diperiksa'];
        @endphp
        @foreach($kelainanFields as $field)
        <div class="row">
            <div class="col-md-4 col-5">
                <x-ui.select :label="$field['label']" :id="$field['id'].'_gdpsikiatri'" :model="$field['id']">
                    @foreach($kelainanOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="col-md-8 col-7">
                <x-ui.input label="Keterangan" :id="$field['ket'].'_gdpsikiatri'" :model="$field['ket']" />
            </div>
        </div>
        @endforeach
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Status Lokalisata" id="status_lokalisata_gdpsikiatri" model="status_lokalisata" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">V. STATUS PSIKIATRIK</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="Kesan Umum" id="psikiatrik_kesan_umum_gdpsikiatri" model="psikiatrik_kesan_umum" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Sikap & Perilaku" id="psikiatrik_sikap_prilaku_gdpsikiatri" model="psikiatrik_sikap_prilaku" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="Kesadaran" id="psikiatrik_kesadaran_gdpsikiatri" model="psikiatrik_kesadaran" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Orientasi" id="psikiatrik_orientasi_gdpsikiatri" model="psikiatrik_orientasi" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="Daya Ingat" id="psikiatrik_daya_ingat_gdpsikiatri" model="psikiatrik_daya_ingat" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Persepsi" id="psikiatrik_persepsi_gdpsikiatri" model="psikiatrik_persepsi" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="Pikiran" id="psikiatrik_pikiran_gdpsikiatri" model="psikiatrik_pikiran" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Insight" id="psikiatrik_insight_gdpsikiatri" model="psikiatrik_insight" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Laborat" id="laborat_gdpsikiatri" model="laborat" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Radiologi" id="radiologi_gdpsikiatri" model="radiologi" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="EKG" id="ekg_gdpsikiatri" model="ekg" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. DIAGNOSIS & ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis" id="diagnosis_gdpsikiatri" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Permasalahan" id="permasalahan_gdpsikiatri" model="permasalahan" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Instruksi Medis" id="instruksi_medis_gdpsikiatri" model="instruksi_medis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Rencana & Target" id="rencana_target_gdpsikiatri" model="rencana_target" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VIII. RENCANA PULANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Dipulangkan" id="pulang_dipulangkan_gdpsikiatri" model="pulang_dipulangkan">
                    <option value="-">-</option>
                    <option value="Tidak Perlu Kontrol">Tidak Perlu Kontrol</option>
                    <option value="Kontrol">Kontrol</option>
                    <option value="Berobat Jalan">Berobat Jalan</option>
                    <option value="Rawat Inap">Rawat Inap</option>
                </x-ui.select>
            </div>
            <div class="col-md-8">
                <x-ui.input label="Keterangan" id="keterangan_pulang_dipulangkan_gdpsikiatri" model="keterangan_pulang_dipulangkan" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="Dirawat di Ruang" id="pulang_dirawat_diruang_gdpsikiatri" model="pulang_dirawat_diruang" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Indikasi Rawat Inap" id="pulang_indikasi_ranap_gdpsikiatri" model="pulang_indikasi_ranap" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="Dirujuk Ke" id="pulang_dirujuk_ke_gdpsikiatri" model="pulang_dirujuk_ke" />
            </div>
            <div class="col-md-6">
                <x-ui.select label="Alasan Dirujuk" id="pulang_alasan_dirujuk_gdpsikiatri" model="pulang_alasan_dirujuk">
                    <option value="-">-</option>
                    <option value="Tempat Penuh">Tempat Penuh</option>
                    <option value="Perlu Fasilitas Lebih">Perlu Fasilitas Lebih</option>
                    <option value="Permintaan Pasien">Permintaan Pasien</option>
                    <option value="Keluarga">Keluarga</option>
                </x-ui.select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Pulang Paksa" id="pulang_paksa_gdpsikiatri" model="pulang_paksa">
                    <option value="-">-</option>
                    <option value="Masalah Biaya">Masalah Biaya</option>
                    <option value="Kondisi Pasien">Kondisi Pasien</option>
                    <option value="Masalah Lokasi Rumah">Masalah Lokasi Rumah</option>
                    <option value="Lain-lain">Lain-lain</option>
                </x-ui.select>
            </div>
            <div class="col-md-8">
                <x-ui.input label="Keterangan Pulang Paksa" id="keterangan_pulang_paksa_gdpsikiatri" model="keterangan_pulang_paksa" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Meninggal di IGD" id="pulang_meninggal_igd_gdpsikiatri" model="pulang_meninggal_igd">
                    <option value="-">-</option>
                    <option value="<= 2 Jam">&lt;= 2 Jam</option>
                    <option value="> 2 Jam">&gt; 2 Jam</option>
                </x-ui.select>
            </div>
            <div class="col-md-8">
                <x-ui.input label="Penyebab Kematian" id="pulang_penyebab_kematian_gdpsikiatri" model="pulang_penyebab_kematian" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">IX. PEMERIKSAAN FISIK PULANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Kesadaran Pulang" id="fisik_pulang_kesadaran_gdpsikiatri" model="fisik_pulang_kesadaran">
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Somnolen">Somnolen</option>
                    <option value="Sopor">Sopor</option>
                    <option value="Koma">Koma</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.input label="TD Pulang (mmHg)" id="fisik_pulang_td_gdpsikiatri" model="fisik_pulang_td" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Nadi Pulang (x/mnt)" id="fisik_pulang_nadi_gdpsikiatri" model="fisik_pulang_nadi" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <x-ui.input label="GCS Pulang" id="fisik_pulang_gcs_gdpsikiatri" model="fisik_pulang_gcs" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="Suhu Pulang (°C)" id="fisik_pulang_suhu_gdpsikiatri" model="fisik_pulang_suhu" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="RR Pulang (x/mnt)" id="fisik_pulang_rr_gdpsikiatri" model="fisik_pulang_rr" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">X. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_gdpsikiatri" model="edukasi" />
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
    $(".btn-awal-gawat-darurat-psikiatri").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-gawat-darurat-psikiatri").modal('show');
    });
</script>
@endpush
