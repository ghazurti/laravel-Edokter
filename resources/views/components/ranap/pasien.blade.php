<div>
    @php $imgUrl = !empty($data->gambar) ? 'http://127.0.0.1/webapps/photopasien/'.$data->gambar : null; @endphp
    <x-adminlte-profile-widget name="{{$data->nm_pasien ?? '-'}}" desc="{{$data->no_rkm_medis ?? '-'}}"
        theme="lightblue" layout-type="classic"
        :img="$imgUrl">
        <x-adminlte-profile-row-item icon="fas fa-fw fa-book-medical" title="No Rawat"
            text="{{$data->no_rawat ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-id-card" title="No KTP"
            text="{{$data->no_ktp ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-user" title="Jns Kelamin"
            text="{{$data->jk == 'L' ? 'Laki - Laki' : 'Perempuan' }}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-calendar" title="Tempat, Tgl Lahir"
            text="{{$data->tmp_lahir ?? '-'}}, {{\Carbon\Carbon::parse($data->tgl_lahir)->isoFormat('LL')  ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-school" title="Pendidikan" text="{{$data->pnd ?? '-'}}" />
        <x-adminlte-profile-row-item title="Nama Ibu" icon="fas fa-fw fa-user"  text="{{$data->nm_ibu  ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-map" title="Alamat" text="{{$data->alamat ?? '-'}}" />
        <x-adminlte-profile-row-item title="Nama Keluarga" icon="fas fa-fw fa-user"  text="{{$data->namakeluarga  ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-briefcase" title="Pekerjaan PJ" text="{{$data->pekerjaanpj ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-map" title="Alamat PJ" text="{{$data->alamatpj ?? '-'}}" />
        <x-adminlte-profile-row-item title="Gol Darah" icon="fas fa-fw fa-droplet" text="{{$data->gol_darah  ?? '-'}}" />
        <x-adminlte-profile-row-item title="Stts Nikah" icon="fas fa-fw fa-ring" text="{{$data->stts_nikah  ?? '-'}}" />
        <x-adminlte-profile-row-item title="Agama" icon="fas fa-fw fa-book" text="{{$data->agama  ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-clock" title="Umur" text="{{$data->umur ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-wallet" title="Cara Bayar" text="{{$data->png_jawab ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-phone" title="No Telp" text="{{$data->no_tlp ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-building" title="Pekerjaan"
            text="{{$data->pekerjaan ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-id-card" title="No Peserta"
            text="{{$data->no_peserta ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="Catatan" text="{{$data->catatan ?? '-'}}" />
        <span class="nav-link">
            <x-adminlte-button label="Riwayat Pemeriksaan" data-toggle="modal"
                data-target="#modalRiwayatPemeriksaanRanap" class="bg-primary justify-content-end" />
        </span>
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

@push('css')
<style>
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
</script>
@endpush