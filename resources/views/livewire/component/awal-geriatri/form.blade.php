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
                <x-ui.input-datetime label="Tanggal" id="tanggal_geriatri" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_geriatri" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_geriatri" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_geriatri" model="keluhan_utama" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps_geriatri" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd_geriatri" model="rpd" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penggunaan Obat" id="rpo_geriatri" model="rpo" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.input label="Riwayat Alergi" id="alergi_geriatri" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Tulang Belakang" id="tulang_belakang_geriatri" model="tulang_belakang">
                    <option value="Tegap">Tegap</option>
                    <option value="Membungkuk">Membungkuk</option>
                    <option value="Kifosis">Kifosis</option>
                    <option value="Skoliosis">Skoliosis</option>
                    <option value="Lordosis">Lordosis</option>
                </x-ui.select>
            </div>
            <div class="col-md-3">
                <x-ui.input label="TD (mmHg)" id="td_geriatri" model="td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Nadi (x/mnt)" id="nadi_geriatri" model="nadi" />
            </div>
            <div class="col-md-2">
                <x-ui.input label="Suhu (°C)" id="suhu_geriatri" model="suhu" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.input label="RR (x/mnt)" id="rr_geriatri" model="rr" />
            </div>
            <div class="col-md-9">
                <x-ui.textarea label="Kondisi Umum" id="kondisi_umum_geriatri" model="kondisi_umum" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">III. STATUS PSIKOLOGIS & KOGNITIF</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.select label="Status Psikologis (GDS)" id="status_psikologis_gds_geriatri" model="status_psikologis_gds">
                    <option value="Skor 1-4 Tidak Ada Depresi">Skor 1-4 Tidak Ada Depresi</option>
                    <option value="Skor Antara 5-9 Menunjukkan Kemungkinan Besar Depresi">Skor Antara 5-9 Menunjukkan Kemungkinan Besar Depresi</option>
                    <option value="Skor 10 Atau Lebih Menunjukkan Depresi">Skor 10 Atau Lebih Menunjukkan Depresi</option>
                </x-ui.select>
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Kondisi Sosial" id="kondisi_sosial_geriatri" model="kondisi_sosial" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.select label="Status Kognitif (MMSE)" id="status_kognitif_mmse_geriatri" model="status_kognitif_mmse">
                    <option value="24-30 : Tidak Ada Gangguan Kognitif">24-30 : Tidak Ada Gangguan Kognitif</option>
                    <option value="18-23 : Gangguan Kognitif Sedang">18-23 : Gangguan Kognitif Sedang</option>
                    <option value="0-17 : Gangguan Kognitif Berat">0-17 : Gangguan Kognitif Berat</option>
                </x-ui.select>
            </div>
        </div>

        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN SISTEM</h6>
        @php
            $statusOptions = ['Normal', 'Abnormal', 'Tidak Diperiksa'];
            $sistemFields = [
                ['id' => 'kepala',     'label' => 'Kepala',     'ket' => 'keterangan_kepala'],
                ['id' => 'thoraks',    'label' => 'Thoraks',    'ket' => 'keterangan_thoraks'],
                ['id' => 'abdomen',    'label' => 'Abdomen',    'ket' => 'keterangan_abdomen'],
                ['id' => 'ekstremitas','label' => 'Ekstremitas','ket' => 'keterangan_ekstremitas'],
            ];
        @endphp
        @foreach($sistemFields as $field)
        <div class="row">
            <div class="col-md-4 col-5">
                <x-ui.select :label="$field['label']" :id="$field['id'].'_geriatri'" :model="$field['id']">
                    @foreach($statusOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="col-md-8 col-7">
                <x-ui.input label="Keterangan" :id="$field['ket'].'_geriatri'" :model="$field['ket']" />
            </div>
        </div>
        @endforeach

        <h6 class="mt-3 text-bold">V. INTEGUMENT</h6>
        <div class="row">
            <div class="col-md-3">
                <x-ui.select label="Kebersihan" id="Integument_kebersihan_geriatri" model="Integument_kebersihan">
                    <option value="Normal">Normal</option>
                    <option value="Abnormal">Abnormal</option>
                </x-ui.select>
            </div>
            <div class="col-md-3">
                <x-ui.select label="Warna" id="Integument_warna_geriatri" model="Integument_warna">
                    <option value="Normal">Normal</option>
                    <option value="Pucat">Pucat</option>
                    <option value="Sianosis">Sianosis</option>
                    <option value="Lain-lain">Lain-lain</option>
                </x-ui.select>
            </div>
            <div class="col-md-3">
                <x-ui.select label="Kelembaban" id="Integument_kelembaban_geriatri" model="Integument_kelembaban">
                    <option value="Kering">Kering</option>
                    <option value="Lembab">Lembab</option>
                </x-ui.select>
            </div>
            <div class="col-md-3">
                <x-ui.select label="Gangguan Kulit" id="Integument_gangguan_kulit_geriatri" model="Integument_gangguan_kulit">
                    <option value="Normal">Normal</option>
                    <option value="Rash">Rash</option>
                    <option value="Luka">Luka</option>
                    <option value="Memar">Memar</option>
                    <option value="Ptekie">Ptekie</option>
                    <option value="Bula">Bula</option>
                </x-ui.select>
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. ASESMEN GERIATRI</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Status Fungsional (Barthel)" id="status_fungsional_geriatri" model="status_fungsional">
                    <option value="20 : Mandiri (A)">20 : Mandiri (A)</option>
                    <option value="12-19 : Ketergantungan Ringan (B)">12-19 : Ketergantungan Ringan (B)</option>
                    <option value="9-11 : Ketergantungan Sedang (B)">9-11 : Ketergantungan Sedang (B)</option>
                    <option value="5-8 : Ketergantungan Berat (C)">5-8 : Ketergantungan Berat (C)</option>
                    <option value="0-4 : Ketergantungan Total (C)">0-4 : Ketergantungan Total (C)</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Skrining Jatuh" id="skrining_jatuh_geriatri" model="skrining_jatuh">
                    <option value="Risiko Rendah Skor 0-5">Risiko Rendah Skor 0-5</option>
                    <option value="Risiko Sedang Skor 6-16">Risiko Sedang Skor 6-16</option>
                    <option value="Risiko Tinggi Skor 17-30">Risiko Tinggi Skor 17-30</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Status Nutrisi (MNA)" id="status_nutrisi_geriatri" model="status_nutrisi">
                    <option value="Skor 12-14 : Status Gizi Normal">Skor 12-14 : Status Gizi Normal</option>
                    <option value="Skor 8-11 : Berisiko Malnutrisi">Skor 8-11 : Berisiko Malnutrisi</option>
                    <option value="Skor 0-7 : Malnutrisi">Skor 0-7 : Malnutrisi</option>
                </x-ui.select>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Keterangan Lainnya" id="lainnya_geriatri" model="lainnya" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Laboratorium" id="lab_geriatri" model="lab" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Radiologi" id="rad_geriatri" model="rad" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Pemeriksaan Lain" id="pemeriksaan_geriatri" model="pemeriksaan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VIII. DIAGNOSIS / ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Kerja" id="diagnosis_geriatri" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Banding" id="diagnosis2_geriatri" model="diagnosis2" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">IX. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Permasalahan" id="permasalahan_geriatri" model="permasalahan" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Terapi / Pengobatan" id="terapi_geriatri" model="terapi" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Tindakan / Rencana Tindakan" id="tindakan_geriatri" model="tindakan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">X. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_geriatri" model="edukasi" />
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
    $(".btn-awal-geriatri").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-geriatri").modal('show');
    });
</script>
@endpush
