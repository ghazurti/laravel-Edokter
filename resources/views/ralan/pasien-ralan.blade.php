@extends('adminlte::page')

@section('title', 'Pasien Ralan')

@section('content_header')
    <h1>Pasien Ralan</h1>
@stop

@section('content')
    <x-adminlte-callout theme="info" title="{{$nm_poli}}">
        <ul class="nav nav-tabs" id="tabRalan" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pasien-tab" data-toggle="tab" data-target="#pasien" type="button" role="tab" aria-controls="pasien" aria-selected="true">Pasien Ralan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rujuk-tab" data-toggle="tab" data-target="#rujuk" type="button" role="tab" aria-controls="rujuk" aria-selected="false">Rujuk Internal</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="pasien" role="tabpanel" aria-labelledby="pasien-tab">
                <x-adminlte-card theme="info">
                    @php
                        $config["responsive"] = true;
                    @endphp
                    {{-- Minimal example / fill data using the component slot --}}
                    @php
                        $poliAsesmen = [
                            'U0025' => ['class' => 'btn-awal-medis',                  'label' => 'Penilaian Awal Medis Umum'],
                            'U0011' => ['class' => 'btn-awal-tht',                    'label' => 'Penilaian Awal Medis THT'],
                            'U0002' => ['class' => 'btn-awal-anak',                   'label' => 'Penilaian Awal Medis Bayi/Anak'],
                            'U0001' => ['class' => 'btn-awal-kandungan',              'label' => 'Penilaian Awal Medis Kandungan'],
                            'U0003' => ['class' => 'btn-awal-dalam',                  'label' => 'Penilaian Awal Medis Penyakit Dalam'],
                            'U0027' => ['class' => 'btn-awal-psikiatri',              'label' => 'Penilaian Awal Medis Psikiatri'],
                            'U0004' => ['class' => 'btn-awal-bedah',                  'label' => 'Penilaian Awal Medis Bedah'],
                            'U0026' => ['class' => 'btn-awal-bedah',                  'label' => 'Penilaian Awal Medis Bedah'],
                            'U0030' => ['class' => 'btn-awal-orthopedi',              'label' => 'Penilaian Awal Medis Bedah Ortopedi'],
                            'U0007' => ['class' => 'btn-awal-neurologi',              'label' => 'Penilaian Awal Medis Neurologi'],
                            'U0008' => ['class' => 'btn-awal-paru',                   'label' => 'Penilaian Awal Medis Paru'],
                            'U0012' => ['class' => 'btn-awal-jantung',                'label' => 'Penilaian Awal Medis Jantung'],
                            'U0006' => ['class' => 'btn-awal-kulitdankelamin',        'label' => 'Penilaian Awal Medis Kulit & Kelamin'],
                            'U0005' => ['class' => 'btn-awal-mata',                   'label' => 'Penilaian Awal Medis Mata'],
                            'U0009' => ['class' => 'btn-awal-urologi',                'label' => 'Penilaian Awal Medis Urologi'],
                            'U0029' => ['class' => 'btn-awal-geriatri',               'label' => 'Penilaian Awal Medis Geriatri'],
                            'U0018' => ['class' => 'btn-awal-rehab-medik',            'label' => 'Penilaian Awal Medis Rehab Medik'],
                            'U0022' => ['class' => 'btn-awal-bedah-mulut',            'label' => 'Penilaian Awal Medis Bedah Mulut'],
                            'U0023' => ['class' => 'btn-awal-penyakit-mulut',         'label' => 'Penilaian Awal Medis Penyakit Mulut'],
                            'IGDK'  => ['class' => 'btn-awal-gawat-darurat-psikiatri','label' => 'Penilaian Awal Medis Gawat Darurat Psikiatri'],
                        ];
                        $asesmen = $poliAsesmen[$kd_poli] ?? ['class' => 'btn-awal-medis', 'label' => 'Penilaian Awal Medis Umum'];
                    @endphp
                    <x-adminlte-datatable id="tablePasienRalan" :heads="$heads" :config="$config" head-theme="dark" striped hoverable bordered compressed>
                        @foreach($data as $row)
                            <tr @if(!empty($row->diagnosa_utama)) class="bg-success" @endif >
                                <td>{{$row->no_reg}}</td>
                                <td>
                                    @php
                                    $noRawat = App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rawat);
                                    $noRM = App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rkm_medis);
                                    @endphp
                                    <a @if(!empty($row->diagnosa_utama)) class="text-white" @else class="text-primary" @endif href="{{route('ralan.pemeriksaan', ['no_rawat' => $noRawat, 'no_rm' => $noRM])}} ">
                                        {{$row->nm_pasien}}
                                    </a>
                                </td>
                                <td>
                                    <button id="{{$row->no_rawat}}" class="btn btn-sm btn-secondary {{ $asesmen['class'] }}">
                                        {{ $row->no_rawat }}
                                    </button>
                                </td>
                                <td>{{$row->no_tlp}}</td>
                                <td>{{$row->nm_dokter}}</td>
                                <td>{{$row->stts}}</td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
            <div class="tab-pane fade" id="rujuk" role="tabpanel" aria-labelledby="rujuk-tab">
                <x-adminlte-card theme="info">
                    @php
                        $config["responsive"] = true;
                    @endphp
                    {{-- Minimal example / fill data using the component slot --}}
                    <x-adminlte-datatable id="tableRujuk" :heads="$headsInternal" :config="$config" head-theme="dark" striped hoverable bordered compressed>
                        @foreach($dataInternal as $row)
                            <tr @if($row->stts == 'Sudah') class="bg-success" @endif >
                                <td>{{$row->no_reg}}</td>
                                <td>
                                    @php
                                    $noRawat = App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rawat);
                                    $noRM = App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rkm_medis);
                                    @endphp
                                    <a @if($row->stts == 'Sudah') class="text-white" @else class="text-primary" @endif href="{{route('ralan.rujuk-internal', ['no_rawat' => $noRawat, 'no_rm' => $noRM])}} ">{{$row->nm_pasien}}
                                    </a>
                                </td>
                                <td>{{$row->no_rkm_medis}}</td>
                                <td>{{$row->nm_dokter}}</td>
                                <td>{{$row->stts}}</td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
        </div>
        <div class="row justify-content-end pr-2">
            <div class="md:col-3 sm:col-auto">
                @php
                $config = ['format' => 'YYYY-MM-DD'];
                @endphp
                <form action="{{route('ralan.pasien')}}" method="GET">
                <x-adminlte-input-date name="tanggal" value="{{$tanggal}}" :config="$config" placeholder="Pilih Tanggal...">
                    <x-slot name="appendSlot">
                        <x-adminlte-button class="btn-flat" type="submit" theme="primary" icon="fas fa-lg fa-search"/>
                    </x-slot>
                </x-adminlte-input-date>
                </form>
            </div>
        </div>
    </x-adminlte-callout>
    
    <x-adminlte-modal wire:ignore.self id="modal-awal-keperawatan" title="Penilaian Awal Medis Umum" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-ralan.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-tht" title="Penilaian Awal Medis THT" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-tht.form  />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-anak" title="Penilaian Awal Medis Anak" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-anak.form-anak  />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-kandungan" title="Penilaian Awal Medis Kandungan" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-kandungan.form-kandungan  />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-dalam" title="Penilaian Awal Medis Penyakit Dalam" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-dalam.form-dalam  />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-psikiatri" title="Penilaian Awal Medis Psikiatri" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-psikiatri.form-psikiatri  />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-bedah" title="Penilaian Awal Medis Bedah" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-bedah.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-neurologi" title="Penilaian Awal Medis Neurologi" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-neurologi.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-orthopedi" title="Penilaian Awal Medis Bedah Ortopedi" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-orthopedi.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-paru" title="Penilaian Awal Medis Paru" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-paru.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-jantung" title="Penilaian Awal Medis Jantung" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-jantung.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-kulitdankelamin" title="Penilaian Awal Medis Kulit &amp; Kelamin" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-kulitdankelamin.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-mata" title="Penilaian Awal Medis Mata" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-mata.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-urologi" title="Penilaian Awal Medis Urologi" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-urologi.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-geriatri" title="Penilaian Awal Medis Geriatri" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-geriatri.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-rehab-medik" title="Penilaian Awal Medis Rehab Medik" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-rehab-medik.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-bedah-mulut" title="Penilaian Awal Medis Bedah Mulut" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-bedah-mulut.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-penyakit-mulut" title="Penilaian Awal Medis Penyakit Mulut" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-penyakit-mulut.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-gawat-darurat-psikiatri" title="Penilaian Awal Medis Gawat Darurat Psikiatri" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-gawat-darurat-psikiatri.form />
    </x-adminlte-modal>
@stop

@section('plugins.TempusDominusBs4', true)
@push('js')
<script>
</script>
@endpush
