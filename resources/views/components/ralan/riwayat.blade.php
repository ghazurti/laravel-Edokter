<div>
    <x-adminlte-modal wire:ignore.self id="modalRiwayatPemeriksaanRalan" title="Riwayat Pemeriksaan" size="xl" theme="info" v-centered
        static-backdrop scrollable>

        <x-adminlte-card theme="dark" title="Gambar Radiologi (PACS Orthanc)" collapsible="collapsed" maximizable>
            <div data-orthanc-gallery data-no-rm="{{ $no_rm }}">
                <div class="d-flex align-items-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    <span>Memuat gambar dari PACS Orthanc untuk No.RM {{ $no_rm }} ...</span>
                </div>
            </div>
        </x-adminlte-card>

        <div class="timeline">
            @foreach($data as $row)
            @php
            $pemeriksaan =
            App\Http\Controllers\Ralan\PemeriksaanRalanController::getPemeriksaanRalan($row->no_rawat,$row->status_lanjut);
            $diagnosa = App\Http\Controllers\Ralan\PemeriksaanRalanController::getDiagnosa($row->no_rawat);
            $laboratorium = App\Http\Controllers\Ralan\PemeriksaanRalanController::getPemeriksaanLab($row->no_rawat);
            $resume = App\Http\Controllers\Ralan\PemeriksaanRalanController::getResume($row->no_rawat);
            $radiologi = App\Http\Controllers\Ralan\PemeriksaanRalanController::getRadiologi($row->no_rawat);
            $gambarRadiologi = App\Http\Controllers\Ralan\PemeriksaanRalanController::getFotoRadiologi($row->no_rawat);
            $tgl = date_create($row->tgl_registrasi ?? '0000-00-00');
            $date = date_format($tgl,"d M Y");
            @endphp

            <div class="time-label">
                <span @if($loop->first) class="bg-green" @else class="bg-yellow" @endif >{{ $date ?? '' }}</span>
            </div>
            <div>
                <i class="fas fa-stethoscope bg-blue"></i>
                <div class="timeline-item">
                    <h3 class="timeline-header d-flex justify-content-between"><b>{{$row->no_rawat}}</b>
                        <b>{{$row->nm_dokter}}</b>
                    </h3>
                    <div class="timeline-body">
                        @if(count($pemeriksaan)>0)
                        <x-adminlte-card theme="dark" title="Pemeriksaan" collapsible maximizable>
                            <div class="table-responsive">
                                @foreach($pemeriksaan as $pemeriksaan)
                                @php
                                $tglPemeriksaan = date_create($pemeriksaan->tgl_perawatan ?? '0000-00-00');
                                $datePemeriksaan = date_format($tglPemeriksaan,"d M Y");
                                @endphp
                                <div class="d-flex justify-content-between">
                                    <h5>{{$datePemeriksaan}}</h5>
                                    <h5>{{$pemeriksaan->jam_rawat}}</h5>
                                </div>
                                <table class="table table-bordered" width="100%">
                                    <tr style="font-weight: bold">
                                        <td>Status</td>
                                        <td>Suhu(C)</td>
                                        <td>Tensi(mmHg)</td>
                                        <td>Nadi(/menit)</td>
                                        <td>RR(/menit)</td>
                                        <td>Tinggi(Cm)</td>
                                        <td>Berat(Kg)</td>
                                        <td>SPO2</td>
                                        <td>GCS(E,V,M)</td>
                                        <td>Kesadaran</td>
                                    </tr>
                                    <tr>
                                        <td>{{$row->status_lanjut}}</td>
                                        <td>{{ $pemeriksaan->suhu_tubuh ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->tensi ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->nadi ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->respirasi ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->tinggi ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->berat ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->spo2 ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->gcs ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->kesadaran ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Alergi</b></td>
                                        <td colspan="9">{{ $pemeriksaan->alergi ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Subjek</b></td>
                                        <td colspan="9">
                                            <pre>{{ $pemeriksaan->keluhan ?? '' }}</pre>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Objek</b></td>
                                        <td colspan="9">
                                            <pre>{{ $pemeriksaan->pemeriksaan ?? '' }}</pre>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Asesmen</b></td>
                                        <td colspan="9">{{ $pemeriksaan->penilaian ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Plan</b></td>
                                        <td colspan="9">{{ $pemeriksaan->rtl ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Instruksi</b></td>
                                        <td colspan="9">{{ $pemeriksaan->instruksi ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Evaluasi</b></td>
                                        <td colspan="9">{{ $pemeriksaan->evaluasi ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Resume Medis</b></td>
                                        <td colspan="9">
                                            @if(isset($resume))
                                            <ul>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Keluhan Utama :</div>
                                                        <div>
                                                            {{$resume->keluhan_utama}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Jalannya Penyakit :</div>
                                                        <div>
                                                            {{$resume->jalannya_penyakit ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Pemeriksaan Penunjang :</div>
                                                        <div>
                                                            {{$resume->pemeriksaan_penunjang ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Hasil Laborat :</div>
                                                        <div>
                                                            {{$resume->hasil_laborat ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Diagnosa Utama :</div>
                                                        <div>
                                                            {{$resume->diagnosa_utama ?? ''}} - {{$resume->kd_diagnosa_utama ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Diagnosa Sekunder 1:</div>
                                                        <div>
                                                            {{$resume->diagnosa_sekunder ?? ''}} - {{$resume->kd_diagnosa_sekunder ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Diagnosa Sekunder 2:</div>
                                                        <div>
                                                            {{$resume->diagnosa_sekunder1 ?? ''}} - {{$resume->kd_diagnosa_sekunder1 ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Diagnosa Sekunder 3:</div>
                                                        <div>
                                                            {{$resume->diagnosa_sekunder2 ?? ''}} - {{$resume->kd_diagnosa_sekunder2 ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Diagnosa Sekunder 4:</div>
                                                        <div>
                                                            {{$resume->diagnosa_sekunder3 ?? ''}} - {{$resume->kd_diagnosa_sekunder3 ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Diagnosa Sekunder 5:</div>
                                                        <div>
                                                            {{$resume->diagnosa_sekunder4 ?? ''}} - {{$resume->kd_diagnosa_sekunder4 ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Prosedur Utama :</div>
                                                        <div>
                                                            {{$resume->prosedur_utama ?? ''}} - {{$resume->kd_prosedur_utama ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Prosedur Sekunder 1:</div>
                                                        <div>
                                                            {{$resume->prosedur_sekunder ?? ''}} - {{$resume->kd_prosedur_sekunder ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Prosedur Sekunder 2:</div>
                                                        <div>
                                                            {{$resume->prosedur_sekunder1 ?? ''}} - {{$resume->kd_prosedur_sekunder1 ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Prosedur Sekunder 3:</div>
                                                        <div>
                                                            {{$resume->prosedur_sekunder2 ?? ''}} - {{$resume->kd_prosedur_sekunder2 ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Prosedur Sekunder 4:</div>
                                                        <div>
                                                            {{$resume->prosedur_sekunder3 ?? ''}} - {{$resume->kd_prosedur_sekunder3 ?? ''}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Obat Pulang :</div>
                                                        <div>
                                                            <pre>{{$resume->obat_pulang}}</pre>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            @else
                                            -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Diagnosa</b></td>
                                        <td colspan="9">
                                            <ol>
                                                @forelse($diagnosa as $diag)
                                                <li>{{$diag->nm_penyakit}} ({{$diag->kd_penyakit}})</li>
                                                @empty
                                                <li>-</li>
                                                @endforelse
                                            </ol>
                                        </td>
                                    </tr>
                                </table>
                                @endforeach
                            </div>
                        </x-adminlte-card>
                        @endif

                        @if(count($radiologi)>0)
                        <x-adminlte-card theme="dark" title="Radiologi" collapsible="collapsed" maximizable>
                            <x-adminlte-card theme="dark" title="Gambar Radiologi" collapsible="collapsed">
                                <div class="container">
                                    <div class="row row-cols-auto">
                                        @foreach($gambarRadiologi as $gambar)
                                            @php
                                                $loc = trim((string) $gambar->lokasi_gambar);
                                                $isFullUrl = preg_match('/^https?:\/\//i', $loc) === 1;
                                                $url = $isFullUrl ? $loc : rtrim(env('URL_RADIOLOGI', ''), '/') . '/' . ltrim($loc, '/');
                                                $isImage = (bool) preg_match('/\.(jpe?g|png|gif|webp|bmp)(\?.*)?$/i', $loc);
                                            @endphp
                                            @if($isImage)
                                            <a href="{{ $url }}" data-toggle="lightbox" data-gallery="example-gallery" class="col-sm-4">
                                                <img src="{{ $url }}" class="img-fluid" style="width: 200px;height:250px">
                                            </a>
                                            @else
                                            <a href="{{ $url }}" target="_blank" rel="noopener" class="col-sm-4 d-inline-block m-1">
                                                <div class="btn btn-outline-info">
                                                    <i class="fas fa-external-link-alt mr-1"></i> Buka Viewer Radiologi
                                                </div>
                                            </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </x-adminlte-card>
                            @foreach($radiologi as $radiologi)
                            <x-adminlte-card title="{{$radiologi->jam}}" theme="dark"
                                footer-class="bg-dark border-top rounded border-light">
                                <pre>{{$radiologi->hasil}}</pre>
                                {{-- <x-slot name="footerSlot">
                                    <x-adminlte-button class="d-flex ml-auto" theme="light" label="Foto"
                                        icon="fas fa-sign-in" />
                                </x-slot> --}}
                            </x-adminlte-card>
                            @endforeach
                        </x-adminlte-card>
                        @endif

                        @if(count($laboratorium)>0)
                        <x-adminlte-card theme="dark" title="Laboratorium" collapsible="collapsed" maximizable>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemeriksaan</th>
                                            <th>Tgl Periksa</th>
                                            <th>Jam</th>
                                            <th>Hasil</th>
                                            <th>Satuan</th>
                                            <th>Nilai Rujukan</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($laboratorium as $lab)
                                        @php
                                            $ket = strtoupper(trim((string) $lab->keterangan));
                                            if (!in_array($ket, ['H','T','L','R','N'])) {
                                                $ket = \App\Helpers\LabKet::auto($lab->nilai, $lab->nilai_rujukan);
                                            }
                                            $rowClass = in_array($ket, ['H','T']) ? 'bg-danger text-white'
                                                : (in_array($ket, ['L','R']) ? 'bg-info text-white' : '');
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$lab->Pemeriksaan}}</td>
                                            <td>{{$lab->tgl_periksa}}</td>
                                            <td>{{$lab->jam}}</td>
                                            <td>{{$lab->nilai}}</td>
                                            <td>{{$lab->satuan}}</td>
                                            <td>{{$lab->nilai_rujukan}}</td>
                                            <td>{{ $ket !== '' ? $ket : ($lab->keterangan ?? '') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </x-adminlte-card>
                        @endif

                        <!-- {{-- <x-adminlte-card theme="dark" title="Laporan Operasi" collapsible="collapsed" maximizable>
                            <livewire:component.riwayat-operasi :noRawat='$row->no_rawat' />
                        </x-adminlte-card> --}} -->

                        @include('partials.riwayat-tambahan', [
                            'ctrl'     => \App\Http\Controllers\Ralan\PemeriksaanRalanController::class,
                            'noRawat'  => $row->no_rawat,
                            'isRanap'  => $row->status_lanjut === 'Ranap',
                        ])
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
</div>

@push('js')
<script>
    $(document).ready(function () {
            $('#example').DataTable();
        });

        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox();
        });

        function formatStudyDate(d) {
            if (!d || d.length < 8) return d || '-';
            return d.slice(6,8) + '-' + d.slice(4,6) + '-' + d.slice(0,4);
        }

        function loadOrthancGallery($container) {
            if ($container.data('loaded')) return;
            $container.data('loaded', true);
            var noRm = $container.data('no-rm');
            if (!noRm) { $container.html('<em>No.RM tidak tersedia.</em>'); return; }
            $.getJSON('/api/orthanc/studies/' + encodeURIComponent(noRm))
                .done(function(res) {
                    if (!res.configured) {
                        $container.html('<em class="text-muted">PACS Orthanc belum dikonfigurasi (ORTHANC_URL di .env).</em>');
                        return;
                    }
                    if (!res.studies || res.studies.length === 0) {
                        $container.html('<em class="text-muted">Belum ada study di Orthanc untuk No.RM ' + noRm + '.</em>');
                        return;
                    }
                    var html = '';
                    res.studies.forEach(function(study) {
                        html += '<div class="mb-3 p-2 border rounded">';
                        html += '<div class="d-flex justify-content-between align-items-center">';
                        html += '<div><b>' + formatStudyDate(study.study_date) + '</b> <span class="text-muted">' + (study.accession || '') + '</span></div>';
                        html += '<button type="button" class="btn btn-sm btn-outline-success" data-orthanc-archive="' + study.id + '"><i class="fas fa-save mr-1"></i>Arsipkan Study</button>';
                        html += '</div>';
                        html += '<div class="text-muted small mb-2">' + (study.description || '-') + '</div>';
                        study.series.forEach(function(s) {
                            html += '<div class="mb-2"><span class="badge badge-secondary mr-1">' + (s.modality || '-') + '</span>';
                            html += '<span class="small">' + (s.description || '') + '</span></div>';
                            html += '<div class="d-flex flex-wrap" style="gap:6px">';
                            s.instances.forEach(function(iid) {
                                var preview = '/api/orthanc/preview/' + iid;
                                var dcm = '/api/orthanc/dicom/' + iid;
                                html += '<div class="text-center">';
                                html += '<a href="' + preview + '" data-toggle="lightbox" data-gallery="orthanc-' + study.id + '">';
                                html += '<img src="' + preview + '" loading="lazy" style="width:140px;height:140px;object-fit:cover;background:#000" />';
                                html += '</a>';
                                html += '<div><a class="small" href="' + dcm + '" download><i class="fas fa-download"></i> DICOM</a></div>';
                                html += '</div>';
                            });
                            html += '</div>';
                        });
                        html += '</div>';
                    });
                    $container.html(html);
                })
                .fail(function() {
                    $container.html('<em class="text-danger">Gagal memuat data dari Orthanc.</em>');
                });
        }

        $(document).on('shown.bs.modal', '#modalRiwayatPemeriksaanRalan', function() {
            $(this).find('[data-orthanc-gallery]').each(function() {
                loadOrthancGallery($(this));
            });
        });

        $(document).on('click', '[data-orthanc-archive]', function() {
            var $btn = $(this);
            var studyId = $btn.data('orthanc-archive');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Mengarsipkan...');
            $.ajax({
                url: '/api/orthanc/archive/' + studyId,
                type: 'POST',
                headers: { 'Accept': 'application/json' }
            }).done(function(res) {
                if (res.ok) {
                    $btn.removeClass('btn-outline-success').addClass('btn-success')
                        .html('<i class="fas fa-check mr-1"></i>Terarsip (' + res.instances + ' instance)');
                } else {
                    $btn.prop('disabled', false).removeClass('btn-outline-success').addClass('btn-warning')
                        .html('<i class="fas fa-exclamation mr-1"></i>Gagal, ulangi');
                }
            }).fail(function() {
                $btn.prop('disabled', false).removeClass('btn-outline-success').addClass('btn-danger')
                    .html('<i class="fas fa-times mr-1"></i>Gagal');
            });
        });
</script>
@endpush