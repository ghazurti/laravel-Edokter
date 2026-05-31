<div>
    @php $imgUrl = !empty($data->gambar) ? 'http://127.0.0.1/webapps/photopasien/'.$data->gambar : null; @endphp
    <x-adminlte-profile-widget name="{{$data->nm_pasien ?? '-'}}" desc="{{$data->no_rkm_medis ?? '-'}}"
        theme="lightblue" layout-type="classic"
        :img="$imgUrl">
        <x-adminlte-profile-row-item icon="fas fa-fw fa-book-medical" title="No Rawat"
            text="{{$data->no_rawat ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-id-card" title="No KTP" text="{{$data->no_ktp ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-user" title="Jns Kelamin"
            text="{{$data->jk == 'L' ? 'Laki - Laki' : 'Perempuan' }}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-calendar" title="Tempat, Tgl Lahir"
            text="{{$data->tmp_lahir ?? '-'}}, {{\Carbon\Carbon::parse($data->tgl_lahir)->isoFormat('LL')  ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-school" title="Pendidikan" text="{{$data->pnd ?? '-'}}" />
        <x-adminlte-profile-row-item title="Nama Ibu" icon="fas fa-fw fa-user" text="{{$data->nm_ibu  ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-map" title="Alamat" text="{{$data->alamat ?? '-'}}" />
        <x-adminlte-profile-row-item title="Nama Keluarga" icon="fas fa-fw fa-user"
            text="{{$data->namakeluarga  ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-briefcase" title="Pekerjaan PJ"
            text="{{$data->pekerjaanpj ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-map" title="Alamat PJ" text="{{$data->alamatpj ?? '-'}}" />
        <x-adminlte-profile-row-item title="Gol Darah" icon="fas fa-fw fa-droplet"
            text="{{$data->gol_darah  ?? '-'}}" />
        <x-adminlte-profile-row-item title="Stts Nikah" icon="fas fa-fw fa-ring" text="{{$data->stts_nikah  ?? '-'}}" />
        <x-adminlte-profile-row-item title="Agama" icon="fas fa-fw fa-book" text="{{$data->agama  ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-clock" title="Umur" text="{{$data->umur ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-wallet" title="Cara Bayar" text="{{$data->png_jawab ?? '-'}}" />
        {{--
        <x-adminlte-profile-row-item icon="fas fa-fw fa-phone" title="No Telp" text="{{$data->no_tlp ?? '-'}}"
            button="btn-phone" /> --}}
        <div class="p-0 col-12">
            <span class="nav-link">
                <i class="fas fa-fw fa-phone"></i>No Telp <span class="float-right"><span
                        id="data-phone">{{$data->no_tlp ?? '-'}}</span>
                    <button id="btn-phone" class="btn btn-sm btn-success">
                        <i class="fas fa-edit"></i>
                    </button>
                </span>
            </span>
        </div>
        <x-adminlte-profile-row-item icon="fas fa-fw fa-building" title="Pekerjaan"
            text="{{$data->pekerjaan ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-id-card" title="No Peserta"
            text="{{$data->no_peserta ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="Catatan" text="{{$data->catatan ?? '-'}}" />
        <div class="p-0 col-12">
            <span class="nav-link">
                <div class="d-flex flex-row justify-content-between" style="gap:10px">
                    <x-adminlte-button label="Riwayat Pemeriksaan" data-toggle="modal"
                        data-target="#modalRiwayatPemeriksaanRalan" class="bg-info" />
                    <x-adminlte-button label="I-Care BPJS" id="icare-button" theme="success" />
                </div>
            </span>
        </div>

        {{-- Ringkasan Klinis (pengganti Berkas RM) --}}
        <div class="p-2 col-12">
            @if($alergi)
            <div class="alert alert-danger py-1 px-2 mb-2 small">
                <i class="fas fa-exclamation-triangle mr-1"></i> <strong>ALERGI:</strong> {{ $alergi }}
            </div>
            @else
            <div class="alert alert-success py-1 px-2 mb-2 small">
                <i class="fas fa-check-circle mr-1"></i> Alergi: tidak ada catatan
            </div>
            @endif

            <div class="small text-muted mb-1"><i class="fas fa-stethoscope mr-1"></i> Diagnosa Terakhir</div>
            @forelse($diagnosaTerakhir as $d)
            <div class="small mb-1">
                <span class="badge badge-info">{{ $d->kd_penyakit }}</span>
                {{ \Illuminate\Support\Str::limit($d->nm_penyakit, 42) }}
            </div>
            @empty
            <div class="small text-muted mb-1">Belum ada diagnosa.</div>
            @endforelse

            @if($kunjunganTerakhir)
            <div class="small text-muted mt-2">
                <i class="fas fa-history mr-1"></i> Kunjungan terakhir:
                <strong>{{ \Carbon\Carbon::parse($kunjunganTerakhir->tgl_registrasi)->isoFormat('LL') }}</strong>
                @if($kunjunganTerakhir->nm_poli) &mdash; {{ $kunjunganTerakhir->nm_poli }} @endif
            </div>
            @endif
        </div>
    </x-adminlte-profile-widget>
</div>

<x-adminlte-modal id="icare-modal" title="I-Care BPJS" size="lg" theme="info" icon="fas fa-bell" v-centered
    static-backdrop scrollable>
    <div class="container-fluid container-icare">

    </div>
</x-adminlte-modal>

<livewire:component.change-phone />

@push('css')
<style>
    @media (min-width: 992px) {
        .modal-lg {
            max-width: 100%;
        }
    }
    .widget-user-image .img-circle[src=""] ,
    .widget-user-image .img-circle:not([src]) {
        display: none;
    }
    .profile-avatar-placeholder {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: -45px auto 0;
        border: 3px solid #fff;
    }
    .profile-avatar-placeholder i {
        font-size: 3rem;
        color: #fff;
    }
</style>
@endpush

@push('js')
{{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
    // Fallback avatar icon jika foto tidak ada atau gagal load
    $(document).ready(function() {
        var $img = $('.widget-user-image .img-circle');
        if ($img.length) {
            $img.on('error', function() {
                $(this).replaceWith('<div class="profile-avatar-placeholder"><i class="fas fa-user"></i></div>');
            });
            if ($img[0].complete && $img[0].naturalWidth === 0) {
                $img.trigger('error');
            }
        } else {
            $('.widget-user-image').html('<div class="profile-avatar-placeholder"><i class="fas fa-user"></i></div>');
        }
    });

    $('#btn-phone').on('click', function(event){
        event.preventDefault();
        var phone = $('#data-phone').text();
        Livewire.emit('setRmPhone', "{{$data->no_rkm_medis}}", phone.trim());
        $('#change-phone').modal('show');
    });

    Livewire.on('refreshPhone', function(event){
        $('#change-phone').modal('hide');
        console.log(event);
        $('#data-phone').text(event);
    });

        $('#icare-button').on('click', function(event){
            event.preventDefault();
            let kdDokter = "{{$dokter}}"
            let param = "{{$data->no_peserta}}"
            $.ajax({
                url: '/api/icare',
                type: 'POST',
                data: {
                    kodedokter: kdDokter,
                    param: param
                },
                beforeSend:function() {
                    Swal.fire({
                        title: 'Loading....',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(data){
                    console.log(data);
                    Swal.close();
                    if(data.code == 200){
                        let url = data.data.url;
                        let html = '';
                        $('#icare-modal').modal('show');
                        html += '<iframe src="'+url+'" frameborder="0" height="700px" width="100%"></iframe>';
                        $('.container-icare').html(html);
                    }else{
                        Swal.fire({
                            title: 'Gagal',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        })
                    }
                },
                error: function(data){
                    console.log(data);
                    Swal.fire({
                        title: 'Gagal',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                }
            })
        })
</script>
{{-- <script>

</script> --}}
@endpush