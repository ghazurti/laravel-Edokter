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
                <x-ui.input-datetime label="Tanggal" id="tanggal_orthopedi" model="tanggal" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label>Anamnesis</label>
                    <div class="row">
                        <div class="col-6">
                            <x-ui.select id="anamnesis_orthopedi" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6">
                            <x-ui.input id="hubungan_orthopedi" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama_orthopedi" model="keluhan_utama" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps_orthopedi" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd_orthopedi" model="rpd" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Riwayat Penggunaan Obat" id="rpo_orthopedi" model="rpo" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.input label="Riwayat Alergi" id="alergi_orthopedi" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.select label="Kesadaran" id="kesadaran_orthopedi" model="kesadaran">
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Delirum">Delirum</option>
                </x-ui.select>
            </div>
            <div class="col-md-4">
                <x-ui.input label="Status / Keadaan Umum" id="status_orthopedi" model="status" />
            </div>
            <div class="col-md-4">
                <x-ui.input label="GCS" id="gcs_orthopedi" model="gcs" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <x-ui.input label="TD (mmHg)" id="td_orthopedi" model="td" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Nadi (x/mnt)" id="nadi_orthopedi" model="nadi" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="Suhu (°C)" id="suhu_orthopedi" model="suhu" />
            </div>
            <div class="col-md-3">
                <x-ui.input label="RR (x/mnt)" id="rr_orthopedi" model="rr" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="BB (Kg)" id="bb_orthopedi" model="bb" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Nyeri (NRS 0-10)" id="nyeri_orthopedi" model="nyeri" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">III. PEMERIKSAAN SISTEM</h6>
        @php
            $statusOptions = ['Normal', 'Abnormal', 'Tidak Diperiksa'];
            $sistemFields = [
                ['id' => 'kepala',     'label' => 'Kepala'],
                ['id' => 'thoraks',    'label' => 'Thoraks'],
                ['id' => 'abdomen',    'label' => 'Abdomen'],
                ['id' => 'ekstremitas','label' => 'Ekstremitas'],
                ['id' => 'genetalia',  'label' => 'Genetalia'],
                ['id' => 'columna',    'label' => 'Columna Vertebra'],
                ['id' => 'muskulos',   'label' => 'Muskuloskeletal'],
            ];
        @endphp
        <div class="row">
            @foreach($sistemFields as $field)
            <div class="col-md-4 col-6">
                <x-ui.select :label="$field['label']" :id="$field['id'].'_orthopedi'" :model="$field['id']">
                    @foreach($statusOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Keterangan Pemeriksaan Fisik Lainnya" id="lainnya_orthopedi" model="lainnya" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">IV. STATUS LOKALIS</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea label="Keterangan Status Lokalis" id="ket_lokalis_orthopedi" model="ket_lokalis" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">V. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Laboratorium" id="lab_orthopedi" model="lab" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Radiologi" id="rad_orthopedi" model="rad" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Pemeriksaan Lain" id="pemeriksaan_orthopedi" model="pemeriksaan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VI. DIAGNOSIS / ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Kerja" id="diagnosis_orthopedi" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Diagnosis Banding" id="diagnosis2_orthopedi" model="diagnosis2" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VII. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Permasalahan" id="permasalahan_orthopedi" model="permasalahan" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Terapi / Pengobatan" id="terapi_orthopedi" model="terapi" />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Tindakan / Rencana Tindakan" id="tindakan_orthopedi" model="tindakan" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">VIII. EDUKASI</h6>
        <div class="row">
            <div class="col-12">
                <x-ui.textarea id="edukasi_orthopedi" model="edukasi" />
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
    $(".btn-awal-orthopedi").on('click', function () {
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-orthopedi").modal('show');
    });
</script>
@endpush
