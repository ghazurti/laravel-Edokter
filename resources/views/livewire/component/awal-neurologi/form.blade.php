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
                <x-ui.input-datetime label="Tanggal" id="tanggal_neurologi" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_neurologi" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_neurologi" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_neurologi" model="keluhan_utama" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps_neurologi" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd_neurologi" model="rpd" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penggunaan Obat" id="rpo_neurologi" model="rpo" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.input label="Riwayat Alergi" id="alergi_neurologi" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Kesadaran" id="kesadaran_neurologi" model="kesadaran">
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Delirum">Delirum</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.select label="Status" id="status_neurologi" model="status">
                    <option value="Skor < 2">Skor &lt; 2</option>
                    <option value="Skor >= 2">Skor &gt;= 2</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.input label="GCS" id="gcs_neurologi" model="gcs" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.input label="TD (mmHg)" id="td_neurologi" model="td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Nadi (x/mnt)" id="nadi_neurologi" model="nadi" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Suhu (°C)" id="suhu_neurologi" model="suhu" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="RR (x/mnt)" id="rr_neurologi" model="rr" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="BB (Kg)" id="bb_neurologi" model="bb" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Nyeri (NRS 0-10)" id="nyeri_neurologi" model="nyeri" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">III. PEMERIKSAAN SISTEM</h6>
        @php
            $statusOptions = ['Normal', 'Abnormal', 'Tidak Diperiksa'];
            $sistemFields = [
                ['id' => 'kepala',     'label' => 'Kepala',            'ket' => 'keterangan_kepala'],
                ['id' => 'thoraks',    'label' => 'Thoraks',           'ket' => 'keterangan_thoraks'],
                ['id' => 'abdomen',    'label' => 'Abdomen',           'ket' => 'keterangan_abdomen'],
                ['id' => 'ekstremitas','label' => 'Ekstremitas',       'ket' => 'keterangan_ekstremitas'],
                ['id' => 'columna',    'label' => 'Columna Vertebra',  'ket' => 'keterangan_columna'],
                ['id' => 'muskulos',   'label' => 'Muskuloskeletal',   'ket' => 'keterangan_muskulos'],
            ];
        @endphp
        @foreach($sistemFields as $field)
        <div class="row">
            <div class="col-md-4 col-6">
                <x-ui.select :label="$field['label']" :id="$field['id'].'_neurologi'" :model="$field['id']">
                    @foreach($statusOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="col-md-8 col-6">
                <x-ui.input label="Keterangan" :id="$field['ket'].'_neurologi'" :model="$field['ket']" />
            </div>
        </div>
        @endforeach
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Keterangan Pemeriksaan Fisik Lainnya" id="lainnya_neurologi" model="lainnya" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Laboratorium" id="lab_neurologi" model="lab" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Radiologi" id="rad_neurologi" model="rad" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Penunjang Lain" id="penunjanglain_neurologi" model="penunjanglain" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">V. DIAGNOSIS / ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Kerja" id="diagnosis_neurologi" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Banding" id="diagnosis2_neurologi" model="diagnosis2" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Permasalahan" id="permasalahan_neurologi" model="permasalahan" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Terapi / Pengobatan" id="terapi_neurologi" model="terapi" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Tindakan / Rencana Tindakan" id="tindakan_neurologi" model="tindakan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_neurologi" model="edukasi" />
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
    $(".btn-awal-neurologi").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-neurologi").modal('show');
    });
</script>
@endpush
