{{-- Caller wajib sediakan: $noRawat, $isRanap (bool) --}}
@php
    $cppt = \App\Helpers\Cppt::timeline($noRawat, $isRanap ?? false);
@endphp
<div class="card card-info collapsed-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-lg fa-stream mr-1"></i>
            CPPT Terintegrasi
            <span class="badge badge-light ml-2">{{ count($cppt) }} catatan</span>
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(count($cppt) === 0)
            <p class="text-muted mb-0">Belum ada catatan terintegrasi untuk kunjungan ini.</p>
        @else
        {{-- legenda --}}
        <div class="mb-3" style="font-size:12px;">
            <span class="badge bg-info text-white">Dokter</span>
            <span class="badge bg-success text-white">Perawat / Bidan</span>
            <span class="badge bg-warning">Ahli Gizi</span>
            <span class="badge bg-purple text-white">Farmasi</span>
        </div>

        <div class="timeline">
            @php $tglTerakhir = null; @endphp
            @foreach($cppt as $e)
                @if($tglTerakhir !== $e->tgl)
                    @php $tglTerakhir = $e->tgl; @endphp
                    <div class="time-label">
                        <span class="bg-secondary">{{ $e->tgl ? \Carbon\Carbon::parse($e->tgl)->translatedFormat('d M Y') : '-' }}</span>
                    </div>
                @endif

                <div>
                    <i class="fas
                        @if($e->tipe === 'soap') fa-user-md
                        @elseif($e->tipe === 'adime') fa-utensils
                        @else fa-notes-medical @endif
                        bg-{{ $e->warna }}"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{ $e->jam ?: '—' }}</span>
                        <h3 class="timeline-header">
                            <span class="badge bg-{{ $e->warna }} text-white">{{ $e->profesi }}</span>
                            <b class="ml-1">{{ $e->penulis }}</b>
                        </h3>
                        <div class="timeline-body">
                            @if($e->tipe === 'soap')
                                @php $v = $e->data->vital; @endphp
                                @if(array_filter($v))
                                <div class="mb-2" style="font-size:12px;">
                                    @foreach(['TD'=>'TD','N'=>'Nadi','S'=>'Suhu','RR'=>'RR','SpO2'=>'SpO2','GCS'=>'GCS','Kes'=>'Kesadaran'] as $k=>$lbl)
                                        @if(!empty($v[$k]))
                                        <span class="badge badge-light border">{{ $lbl }}: {{ $v[$k] }}</span>
                                        @endif
                                    @endforeach
                                </div>
                                @endif
                                <table class="table table-sm table-bordered mb-0">
                                    <tr><th style="width:90px">S</th><td><pre class="mb-0">{{ $e->data->S ?: '-' }}</pre></td></tr>
                                    <tr><th>O</th><td><pre class="mb-0">{{ $e->data->O ?: '-' }}</pre></td></tr>
                                    <tr><th>A</th><td><pre class="mb-0">{{ $e->data->A ?: '-' }}</pre></td></tr>
                                    <tr><th>P</th><td><pre class="mb-0">{{ $e->data->P ?: '-' }}</pre></td></tr>
                                    @if($e->data->instruksi)
                                    <tr><th>Instruksi</th><td><pre class="mb-0">{{ $e->data->instruksi }}</pre></td></tr>
                                    @endif
                                    @if($e->data->evaluasi)
                                    <tr><th>Evaluasi</th><td><pre class="mb-0">{{ $e->data->evaluasi }}</pre></td></tr>
                                    @endif
                                    @if($e->data->alergi)
                                    <tr><th>Alergi</th><td>{{ $e->data->alergi }}</td></tr>
                                    @endif
                                </table>
                            @elseif($e->tipe === 'adime')
                                <table class="table table-sm table-bordered mb-0">
                                    <tr><th style="width:90px">Antropometri</th><td>{{ $e->data->A ?: '-' }}</td></tr>
                                    <tr><th>Diagnosis</th><td><pre class="mb-0">{{ $e->data->D ?: '-' }}</pre></td></tr>
                                    <tr><th>Intervensi</th><td><pre class="mb-0">{{ $e->data->I ?: '-' }}</pre></td></tr>
                                    <tr><th>Monitoring</th><td><pre class="mb-0">{{ $e->data->ME ?: '-' }}</pre></td></tr>
                                </table>
                            @else
                                <pre class="mb-0">{{ $e->data->uraian ?: '-' }}</pre>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            <div><i class="fas fa-clock bg-gray"></i></div>
        </div>
        @endif
    </div>
</div>
