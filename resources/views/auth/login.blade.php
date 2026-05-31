<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login &mdash; {{ env('NAMA_INSTANSI', config('app.name')) }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    @php
        $logo = asset(env('LOGO_INSTANSI', 'images/logo-rsud-baubau.png'));
        $bgPath = public_path('images/login-bg.jpg');
        $bg = file_exists($bgPath) ? asset('images/login-bg.jpg') : null;
    @endphp
    <style>
        :root{
            --brand:#2f7da3; --brand-d:#256683; --brand-l:#e8f3f8;
            --ink:#1f2d3d; --muted:#7a8aa0; --line:#e3e8ef;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{
            font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
            color:var(--ink);background:#f3f6f9;min-height:100vh;
        }
        .wrap{display:flex;min-height:100vh}

        /* ===== Kiri: visual ===== */
        .side{
            flex:1 1 50%;position:relative;overflow:hidden;
            background:linear-gradient(135deg,var(--brand) 0%,var(--brand-d) 100%);
            @if($bg) background-image:linear-gradient(180deg,rgba(37,102,131,.35),rgba(37,102,131,.75)),url('{{ $bg }}');
            background-size:cover;background-position:center; @endif
            display:flex;flex-direction:column;justify-content:flex-end;
            padding:48px;color:#fff;
        }
        .side .deco{
            position:absolute;font-size:520px;line-height:1;right:-120px;top:-60px;
            color:rgba(255,255,255,.07);transform:rotate(-12deg);pointer-events:none;
        }
        .side .brand{position:relative;z-index:2}
        .side .brand img{height:56px;background:#fff;border-radius:14px;padding:8px;margin-bottom:18px}
        .side .brand h2{font-size:26px;font-weight:700;line-height:1.25;max-width:460px}
        .side .brand p{margin-top:10px;font-size:14px;opacity:.9;max-width:440px}

        /* ===== Kanan: form ===== */
        .panel{
            flex:1 1 50%;display:flex;align-items:center;justify-content:center;
            padding:40px;background:#fff;
        }
        .card{width:100%;max-width:380px}
        .card .logo-sm{height:46px;margin-bottom:26px}
        .card h1{font-size:26px;font-weight:700;margin-bottom:6px}
        .card .sub{color:var(--muted);font-size:14px;margin-bottom:26px}

        .alert{
            background:#fdecea;border:1px solid #f5c6cb;color:#a32f2f;
            border-radius:10px;padding:10px 14px;font-size:13px;margin-bottom:18px;
            display:flex;align-items:center;gap:8px;
        }
        .field{margin-bottom:16px}
        .field label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#41506a}
        .inp{position:relative}
        .inp .ic{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            color:var(--muted);font-size:15px;
        }
        .inp input,.inp select{
            width:100%;height:46px;border:1px solid var(--line);border-radius:10px;
            padding:0 42px;font-size:14px;color:var(--ink);background:#fff;
            transition:border-color .15s,box-shadow .15s;appearance:none;-webkit-appearance:none;
        }
        .inp input:focus,.inp select:focus{
            outline:none;border-color:var(--brand);box-shadow:0 0 0 3px rgba(47,125,163,.15);
        }
        .inp input.err,.inp select.err{border-color:#e3556e;box-shadow:0 0 0 3px rgba(227,85,110,.12)}
        .inp .eye{
            position:absolute;right:12px;top:50%;transform:translateY(-50%);
            color:var(--muted);cursor:pointer;font-size:15px;background:none;border:none;padding:6px;
        }
        .inp .chev{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none}
        .err-msg{color:#d23f57;font-size:12px;margin-top:5px;display:block}

        .btn{
            width:100%;height:48px;border:none;border-radius:10px;cursor:pointer;
            background:var(--brand);color:#fff;font-size:15px;font-weight:600;
            display:flex;align-items:center;justify-content:center;gap:8px;
            transition:background .15s,transform .05s;margin-top:6px;
        }
        .btn:hover{background:var(--brand-d)}
        .btn:active{transform:translateY(1px)}

        .note{
            margin-top:22px;background:var(--brand-l);border-radius:10px;
            padding:12px 14px;font-size:12px;color:#3a5366;
        }
        .note b{display:block;margin-bottom:4px;color:var(--brand-d)}
        .note ol{margin:0;padding-left:18px;line-height:1.7}

        .foot{text-align:center;margin-top:22px;color:var(--muted);font-size:12px}

        @media (max-width:860px){
            .side{display:none}
            .panel{flex:1 1 100%}
        }
    </style>
</head>
<body>
<div class="wrap">
    {{-- ===== Visual kiri ===== --}}
    <div class="side">
        <i class="fas fa-stethoscope deco"></i>
        <div class="brand">
            <img src="{{ $logo }}" alt="logo">
            <h2>{{ env('NAMA_INSTANSI', config('app.name')) }}</h2>
            <p>Sistem E-Dokter &mdash; dokumentasi rekam medis elektronik dalam satu genggaman, cepat dan terintegrasi.</p>
        </div>
    </div>

    {{-- ===== Form kanan ===== --}}
    <div class="panel">
        <div class="card">
            <img class="logo-sm" src="{{ $logo }}" alt="logo">
            <h1>Login</h1>
            <p class="sub">Masuk ke akun dokter untuk memulai pelayanan.</p>

            @error('message')
                <div class="alert"><i class="fas fa-exclamation-circle"></i><span>{{ $message }}</span></div>
            @enderror

            <form action="{{ route('customlogin') }}" method="post">
                @csrf

                <div class="field">
                    <label>NIP Dokter</label>
                    <div class="inp">
                        <span class="ic"><i class="fas fa-user-md"></i></span>
                        <input type="text" name="username" value="{{ old('username') }}"
                               class="@error('username') err @enderror" placeholder="Masukkan NIP dokter" autofocus>
                    </div>
                    @error('username') <span class="err-msg">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label>Password</label>
                    <div class="inp">
                        <span class="ic"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="pw"
                               class="@error('password') err @enderror" placeholder="Masukkan password">
                        <button type="button" class="eye" onclick="togglePw()" tabindex="-1"><i class="fas fa-eye" id="pwIcon"></i></button>
                    </div>
                    @error('password') <span class="err-msg">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label>Poliklinik</label>
                    <div class="inp">
                        <span class="ic"><i class="fas fa-hospital"></i></span>
                        <select name="poli" class="@error('poli') err @enderror">
                            <option value="" disabled {{ old('poli') ? '' : 'selected' }}>Pilih poliklinik</option>
                            @foreach($poli as $p)
                                <option value="{{ $p->kd_poli }}" {{ old('poli') == $p->kd_poli ? 'selected' : '' }}>{{ $p->nm_poli }}</option>
                            @endforeach
                        </select>
                        <span class="chev"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    @error('poli') <span class="err-msg">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </button>
            </form>

            <div class="note">
                <b><i class="fas fa-info-circle mr-1"></i> Catatan</b>
                <ol>
                    <li>Login menggunakan <strong>NIP Dokter</strong></li>
                    <li>Pilih <strong>Poliklinik</strong> sesuai spesialis</li>
                    <li>Sesi 30 menit, auto-logout bila tidak ada transaksi</li>
                </ol>
            </div>

            <div class="foot">&copy; {{ date('Y') }} {{ env('NAMA_INSTANSI', config('app.name')) }}</div>
        </div>
    </div>
</div>

<script>
    function togglePw(){
        var i=document.getElementById('pw'), ic=document.getElementById('pwIcon');
        if(i.type==='password'){i.type='text';ic.className='fas fa-eye-slash';}
        else{i.type='password';ic.className='fas fa-eye';}
    }
</script>
</body>
</html>
