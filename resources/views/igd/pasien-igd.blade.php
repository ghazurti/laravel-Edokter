@extends('adminlte::page')

@section('title', 'Pasien IGD')

@section('content_header')
    <h1>Unit Gawat Darurat (IGD)</h1>
@stop

@section('content')
<x-adminlte-callout theme="danger" title="Daftar Pasien IGD">
    <form method="GET" action="{{ route('igd.pasien') }}" class="mb-3">
        <div class="d-flex align-items-end" style="gap:8px">
            <div>
                <label class="mb-0">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control">
            </div>
            <button class="btn btn-danger">Cari</button>
        </div>
    </form>

    <x-adminlte-datatable id="tablePasienIgd" :heads="$heads" head-theme="dark" striped hoverable bordered compressed>
        @foreach($data as $row)
            @php
                $noRawat = App\Http\Controllers\Igd\PasienIgdController::encryptData($row->no_rawat);
                $noRM    = App\Http\Controllers\Igd\PasienIgdController::encryptData($row->no_rkm_medis);
            @endphp
            <tr @if(!empty($row->sudah_periksa)) class="bg-success text-white" @endif>
                <td>{{ $row->no_reg }}</td>
                <td>
                    <a @if(!empty($row->sudah_periksa)) class="text-white" @else class="text-primary" @endif
                       href="{{ route('igd.pemeriksaan', ['no_rawat' => $noRawat, 'no_rm' => $noRM]) }}">
                        {{ $row->nm_pasien }}
                    </a>
                </td>
                <td>{{ $row->no_rawat }}</td>
                <td>{{ $row->no_tlp ?? '-' }}</td>
                <td>{{ $row->nm_dokter }}</td>
                <td>
                    @if($row->stts == 'Sudah')
                        <span class="badge badge-success">Sudah</span>
                    @else
                        <span class="badge badge-warning">Belum</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>
</x-adminlte-callout>
@stop

@section('plugins.Datatables', true)
@push('js')
<script>
    (function () {
        const pageDate = '{{ $tanggal }}';
        function todayStr() {
            const parts = new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Asia/Makassar', year: 'numeric', month: '2-digit', day: '2-digit'
            }).formatToParts(new Date());
            const get = (t) => parts.find(p => p.type === t).value;
            return `${get('year')}-${get('month')}-${get('day')}`;
        }
        function check() {
            if (pageDate !== todayStr()) {
                const url = new URL(window.location.href);
                url.searchParams.set('tanggal', todayStr());
                window.location.href = url.toString();
            }
        }
        setInterval(check, 60 * 1000);
        document.addEventListener('visibilitychange', function () { if (!document.hidden) check(); });
    })();
</script>
@endpush
