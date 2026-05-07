<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>NGD Admin — Sign in</title>
    <link rel="stylesheet" href="{{ asset('adminpanel/dist/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('adminpanel/dist/assets/images/favicon.png') }}" />
    <style>
        :root {
            --bg-app: #f6f8fb;
            --bg-surface: #ffffff;
            --bg-muted: #f3f5f9;
            --border: #e6e8ee;
            --text-primary: #0f172a;
            --text-secondary: #5b6473;
            --text-muted: #94a3b8;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --accent-soft: #eef2ff;
            --danger: #ef4444;
            --danger-soft: #fef2f2;
            --radius: 16px;
            --radius-sm: 10px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-app);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }

        /* Decorative backdrop */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 15% 20%, rgba(99, 102, 241, 0.10) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(14, 165, 233, 0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .auth-shell {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        .brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: var(--accent);
            color: #fff;
            font-weight: 700;
            font-size: 22px;
            letter-spacing: -0.5px;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.35);
            margin-bottom: 14px;
        }

        .brand-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .auth-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 36px 32px;
            box-shadow:
                0 1px 3px rgba(15, 23, 42, .04),
                0 10px 30px rgba(15, 23, 42, .06);
        }

        .auth-title {
            font-size: 22px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .auth-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 28px;
        }

        .alert-error {
            background: var(--danger-soft);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #b91c1c;
            border-radius: var(--radius-sm);
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .alert-error ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .alert-error li {
            padding: 2px 0;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .input-wrap .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 18px;
            line-height: 1;
        }

        .input-wrap .toggle-pw:hover {
            color: var(--accent);
        }

        .form-control {
            width: 100%;
            height: 46px;
            padding: 0 14px 0 44px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text-primary);
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            transition: border-color .15s ease, box-shadow .15s ease;
            outline: none;
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .form-control.has-toggle {
            padding-right: 44px;
        }

        .row-helper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 6px 0 22px;
            font-size: 13px;
        }

        .check-row {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
        }

        .check-row input {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
        }

        .link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        .link:hover {
            color: var(--accent-hover);
            text-decoration: underline;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            height: 46px;
            background: var(--accent);
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.2px;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background .15s ease, transform .05s ease, box-shadow .15s ease;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:active {
            transform: translateY(1px);
        }

        .auth-footer {
            margin-top: 22px;
            text-align: center;
            color: var(--text-muted);
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="auth-shell">
        <div class="brand">
            <div class="brand-mark">N</div>
            <div class="brand-name">NGD Admin</div>
        </div>

        <div class="auth-card">
            <h1 class="auth-title">Welcome back</h1>
            <p class="auth-subtitle">Sign in to your account to continue.</p>

            @if ($errors->any())
                <div class="alert-error" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" autocomplete="on">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <div class="input-wrap">
                        <i class="mdi mdi-email-outline input-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="you@example.com"
                            value="{{ old('email') }}"
                            required
                            autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <i class="mdi mdi-lock-outline input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control has-toggle"
                            placeholder="Enter your password"
                            required>
                        <button type="button" class="toggle-pw" id="togglePw" aria-label="Show password">
                            <i class="mdi mdi-eye-outline" id="togglePwIcon"></i>
                        </button>
                    </div>
                </div>

                <div style="margin-bottom: 22px;"></div>

                <button type="submit" class="btn-primary">
                    Sign in
                    <i class="mdi mdi-arrow-right"></i>
                </button>
            </form>
        </div>

        <p class="auth-footer">&copy; {{ date('Y') }} NGD Admin. All rights reserved.</p>
    </div>

    <script>
        (function () {
            var btn = document.getElementById('togglePw');
            var input = document.getElementById('password');
            var icon = document.getElementById('togglePwIcon');
            if (!btn || !input || !icon) return;
            btn.addEventListener('click', function () {
                var isPw = input.type === 'password';
                input.type = isPw ? 'text' : 'password';
                icon.className = isPw ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline';
                btn.setAttribute('aria-label', isPw ? 'Hide password' : 'Show password');
            });
        })();
    </script>
</body>

</html>
