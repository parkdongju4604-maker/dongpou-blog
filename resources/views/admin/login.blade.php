<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 로그인 - DongPou Blog</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 1rem;
        }

        .login-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            padding: 2.5rem 2rem;
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo a {
            text-decoration: none;
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            background: #4f46e5;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
        }

        .logo-icon svg {
            width: 28px;
            height: 28px;
            fill: #fff;
        }

        .logo h1 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #111827;
        }

        .logo p {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 0.2rem;
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            color: #dc2626;
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.1rem;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.4rem;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.65rem 0.9rem;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            color: #111827;
            outline: none;
            transition: border-color 0.15s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: #4f46e5;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: background 0.15s;
        }

        .btn-login:hover { background: #4338ca; }

        .back-link {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.875rem;
        }

        .back-link a {
            color: #4f46e5;
            text-decoration: none;
        }

        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-box">
        <div class="logo">
            <a href="{{ route('home') }}">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                </div>
                <h1>DongPou Blog</h1>
            </a>
            <p>관리자 로그인</p>
        </div>

        @if ($errors->has('login'))
            <div class="error-box">{{ $errors->first('login') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label for="username">아이디</label>
                <input type="text" id="username" name="username"
                       value="{{ old('username') }}"
                       placeholder="아이디를 입력하세요"
                       autofocus>
            </div>
            <div class="form-group">
                <label for="password">비밀번호</label>
                <input type="password" id="password" name="password"
                       placeholder="비밀번호를 입력하세요">
            </div>
            <button type="submit" class="btn-login">로그인</button>
        </form>

        <div class="back-link">
            <a href="{{ route('home') }}">← 블로그로 돌아가기</a>
        </div>
    </div>
</div>
</body>
</html>
