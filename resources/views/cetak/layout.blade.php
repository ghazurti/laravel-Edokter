<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dokumen')</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #000; margin: 0; }
        .kop { border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 10px; }
        .kop table { width: 100%; border-collapse: collapse; }
        .kop .logo { width: 80px; text-align: center; vertical-align: middle; }
        .kop .logo img { max-height: 75px; }
        .kop .inst { text-align: center; vertical-align: middle; }
        .kop .inst .nama { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .kop .inst .alamat { font-size: 11px; }
        .judul { text-align: center; font-weight: bold; font-size: 14px; text-decoration: underline; text-transform: uppercase; margin: 8px 0 4px; }
        .sub-judul { text-align: center; font-size: 11px; margin-bottom: 12px; }
        table.data { border-collapse: collapse; }
        table.data td { padding: 1px 4px; vertical-align: top; }
        table.box { width: 100%; border-collapse: collapse; }
        table.box th, table.box td { border: 1px solid #000; padding: 4px 6px; }
        table.box th { background: #eee; }
        .ttd { margin-top: 24px; }
        .ttd table { width: 100%; }
        .ttd .kolom { text-align: center; vertical-align: top; }
        .ttd .space { height: 60px; }
        .footer-note { font-size: 10px; color: #555; margin-top: 18px; border-top: 1px solid #ccc; padding-top: 4px; }
        pre { font-family: DejaVu Sans, sans-serif; white-space: pre-wrap; margin: 0; }
    </style>
</head>
<body>
    <div class="kop">
        <table>
            <tr>
                @php $logo = public_path(env('LOGO_INSTANSI', '')); @endphp
                <td class="logo">
                    @if($logo && file_exists($logo))
                        <img src="{{ $logo }}" alt="logo">
                    @endif
                </td>
                <td class="inst">
                    <div class="nama">{{ env('NAMA_INSTANSI', env('APP_NAME')) }}</div>
                    <div class="alamat">{{ env('ALAMAT_INSTANSI') }}, {{ env('KOTA_INSTANSI') }}</div>
                    <div class="alamat">
                        @if(env('TELP_INSTANSI')) Telp. {{ env('TELP_INSTANSI') }} @endif
                        @if(env('EMAIL_INSTANSI')) &middot; {{ env('EMAIL_INSTANSI') }} @endif
                    </div>
                </td>
                <td class="logo"></td>
            </tr>
        </table>
    </div>

    @yield('content')

    <div class="footer-note">
        Dicetak melalui E-Dokter pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WITA.
    </div>
</body>
</html>
