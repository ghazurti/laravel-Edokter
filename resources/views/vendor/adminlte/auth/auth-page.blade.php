@extends('adminlte::master')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('adminlte_css')
    <style>
        :root {
            --teal-light: #4cb56a;
            --teal: #1f8a3a;
            --teal-dark: #156b2c;
            --teal-darker: #0f5021;
            --accent: #f4c81f;
        }

        body.login-page,
        body.register-page {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #1f8a3a 0%, #156b2c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Source Sans Pro', sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Wave background SVG layers */
        body.login-page::before,
        body.login-page::after,
        body.register-page::before,
        body.register-page::after {
            content: "";
            position: absolute;
            left: 0; right: 0;
            height: 55vh;
            background-repeat: no-repeat;
            background-size: cover;
            z-index: 0;
            pointer-events: none;
        }

        body.login-page::before,
        body.register-page::before {
            top: 0;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320' preserveAspectRatio='none'><path fill='%23f4c81f' fill-opacity='0.18' d='M0,160L60,170.7C120,181,240,203,360,197.3C480,192,600,160,720,138.7C840,117,960,107,1080,128C1200,149,1320,203,1380,229.3L1440,256L1440,0L0,0Z'/></svg>");
            background-position: top;
        }

        body.login-page::after,
        body.register-page::after {
            bottom: 0;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320' preserveAspectRatio='none'><path fill='%230f5021' fill-opacity='0.55' d='M0,96L60,117.3C120,139,240,181,360,181.3C480,181,600,139,720,138.7C840,139,960,181,1080,202.7C1200,224,1320,224,1380,224L1440,224L1440,320L0,320Z'/></svg>");
            background-position: bottom;
        }

        /* Plexus dots top-left */
        .plexus-deco {
            position: absolute;
            top: 20px; left: 30px;
            width: 380px; height: 280px;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'><g stroke='%23ffffff' stroke-opacity='0.35' stroke-width='1' fill='%23ffffff' fill-opacity='0.6'><circle cx='40' cy='60' r='2'/><circle cx='110' cy='30' r='2'/><circle cx='180' cy='70' r='2'/><circle cx='250' cy='40' r='2'/><circle cx='90' cy='130' r='2'/><circle cx='200' cy='160' r='2'/><circle cx='300' cy='110' r='2'/><circle cx='60' cy='220' r='2'/><circle cx='160' cy='240' r='2'/><circle cx='260' cy='200' r='2'/><line x1='40' y1='60' x2='110' y2='30'/><line x1='110' y1='30' x2='180' y2='70'/><line x1='180' y1='70' x2='250' y2='40'/><line x1='40' y1='60' x2='90' y2='130'/><line x1='90' y1='130' x2='200' y2='160'/><line x1='200' y1='160' x2='300' y2='110'/><line x1='180' y1='70' x2='200' y2='160'/><line x1='90' y1='130' x2='60' y2='220'/><line x1='60' y1='220' x2='160' y2='240'/><line x1='160' y1='240' x2='260' y2='200'/><line x1='200' y1='160' x2='260' y2='200'/></g></svg>");
            background-size: contain;
            background-repeat: no-repeat;
            z-index: 1;
            pointer-events: none;
        }

        .login-box, .register-box {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 10;
            padding: 0 16px;
        }

        @media (max-width: 575px) {
            .login-card-body, .register-card-body { padding: 22px 18px; }
            .login-logo, .register-logo { padding: 18px 14px; }
            .login-logo a, .register-logo a { font-size: 15px; gap: 10px; letter-spacing: 2px; }
            .login-logo img, .register-logo img { height: 48px !important; }
            .plexus-deco { display: none; }
        }
        @media (max-width: 991px) {
            .plexus-deco { width: 220px; height: 160px; opacity: 0.6; }
        }

        /* Logo banner with gloss */
        .login-logo, .register-logo {
            background: linear-gradient(135deg, var(--teal-light) 0%, var(--teal) 100%);
            color: #ffffff !important;
            padding: 24px 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }
        .login-logo::after, .register-logo::after {
            content: "";
            position: absolute;
            top: -40%; left: -10%;
            width: 60%; height: 80%;
            background: linear-gradient(180deg, rgba(255,255,255,0.25), transparent);
            transform: rotate(-15deg);
            pointer-events: none;
        }
        .login-logo a, .register-logo a {
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            letter-spacing: 3px;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            position: relative;
            z-index: 1;
        }
        .login-logo img, .register-logo img {
            height: 60px !important;
            background: #ffffff;
            padding: 6px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.18);
            margin: 0 !important;
        }

        /* Form card */
        .login-box .card, .register-box .card {
            border: none;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 18px 48px rgba(0,0,0,0.22);
            margin-top: 0;
        }

        .login-card-body, .register-card-body {
            padding: 30px 32px 28px;
            border-radius: 0 0 10px 10px;
        }

        .login-box-msg, .register-box-msg {
            color: #4a5568;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            padding: 0 0 18px;
            margin-bottom: 18px;
            border-bottom: 1px solid #eef0f4;
        }

        .login-card-body .input-group,
        .register-card-body .input-group {
            margin-bottom: 16px;
            border-radius: 6px;
            transition: box-shadow .2s, border-color .2s;
        }

        .login-card-body .form-control,
        .register-card-body .form-control {
            border-right: 0;
            border-color: #dfe3eb;
            box-shadow: none;
            height: 46px;
            padding-left: 16px;
            font-size: 14px;
            background: #fafbfc;
        }

        .login-card-body .input-group-text,
        .register-card-body .input-group-text {
            background: #fafbfc;
            border-left: 0;
            border-color: #dfe3eb;
            color: #94a3b8;
            min-width: 46px;
            justify-content: center;
        }

        .login-card-body .form-control:focus,
        .register-card-body .form-control:focus {
            border-color: var(--teal);
            background: #ffffff;
        }
        .login-card-body .input-group:focus-within .input-group-text {
            border-color: var(--teal);
            color: var(--teal);
            background: #ffffff;
        }

        /* Select styling */
        .login-card-body .form-group,
        .login-card-body .bootstrap-select,
        .login-card-body select {
            margin-bottom: 18px;
        }
        .login-card-body .form-control[name="poli"],
        .login-card-body select.form-control {
            background: #fafbfc;
            border: 1px solid #dfe3eb;
            border-radius: 4px;
            height: 46px;
        }
        .login-card-body .bootstrap-select > .dropdown-toggle {
            background: #fafbfc !important;
            border: 1px solid #dfe3eb !important;
            height: 46px;
            font-size: 14px;
            color: #6c757d;
        }

        /* Button */
        .btn-primary, .btn-flat.btn-primary {
            background: var(--teal) !important;
            border-color: var(--teal) !important;
            color: #fff !important;
            font-weight: 600;
            letter-spacing: 1px;
            border-radius: 4px;
            height: 44px;
            box-shadow: 0 4px 12px rgba(75,163,168,0.35);
            transition: all .2s;
        }
        .btn-primary:hover {
            background: var(--teal-dark) !important;
            border-color: var(--teal-dark) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(75,163,168,0.45);
        }

        .icheck-primary input:checked + label::before {
            background-color: var(--teal) !important;
            border-color: var(--teal) !important;
        }

        /* NB box */
        .login-nb {
            margin-top: 18px;
            padding: 14px 16px;
            background: #f0f8f9;
            border-left: 3px solid var(--teal);
            border-radius: 4px;
            font-size: 13px;
            color: #4a5568;
        }
        .login-nb b {
            color: var(--teal-darker);
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        .login-nb ol {
            margin: 0;
            padding-left: 20px;
        }
        .login-nb ol li {
            margin-bottom: 3px;
            line-height: 1.5;
        }
    </style>
    @stack('css')
    @yield('css')
@stop

@section('classes_body'){{ ($auth_type ?? 'login') . '-page' }}@stop

@section('body')
    <div class="plexus-deco"></div>

    <div class="{{ $auth_type ?? 'login' }}-box">

        {{-- Logo banner --}}
        <div class="{{ $auth_type ?? 'login' }}-logo">
            <a href="{{ $dashboard_url }}">
                <img src="{{ asset(config('adminlte.logo_img')) }}">
                <span>{!! config('adminlte.logo', '<b>Admin</b>LTE') !!}</span>
            </a>
        </div>

        {{-- Form card --}}
        <div class="card">
            @hasSection('auth_header')
                <div class="card-header {{ config('adminlte.classes_auth_header', '') }}">
                    <h3 class="card-title float-none text-center">
                        @yield('auth_header')
                    </h3>
                </div>
            @endif

            <div class="card-body {{ $auth_type ?? 'login' }}-card-body {{ config('adminlte.classes_auth_body', '') }}">
                @yield('auth_body')
            </div>

            @hasSection('auth_footer')
                <div class="card-footer {{ config('adminlte.classes_auth_footer', '') }}">
                    @yield('auth_footer')
                </div>
            @endif
        </div>

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
