@extends('layouts.app')

@section('title', '개인정보처리방침')
@section('description', '개인정보 처리방침 안내 페이지입니다.')

@section('content')
<section style="max-width:860px;margin:0 auto;padding:34px 20px 60px;">
    <h1 style="font-size:1.9rem;font-weight:800;line-height:1.3;color:#111827;margin-bottom:18px;">개인정보 처리방침</h1>

    <article style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:22px 20px;color:#1f2937;line-height:1.8;">
        <h2 style="font-size:1.08rem;font-weight:700;margin:0 0 8px;">1. 개인정보의 처리 목적 및 항목</h2>
        <p style="margin:0 0 10px;">
            본 웹사이트({{ url('/') }}, 이하 '본 사이트')는 방문자의 서비스 이용 과정에서 최소한의 개인정보를 수집하며, 수집된 정보는 다음의 목적 이외의 용도로는 이용되지 않습니다.
        </p>
        <p style="margin:0 0 4px;">댓글 작성 및 관리: 이름(닉네임), 이메일 주소, 접속 IP 주소, 브라우저 정보(스팸 방지용)</p>
        <p style="margin:0 0 16px;">서비스 이용 분석: 쿠키(Cookie)를 통한 방문 기록 및 설정 저장</p>

        <h2 style="font-size:1.08rem;font-weight:700;margin:0 0 8px;">2. 개인정보의 보유 및 이용 기간</h2>
        <p style="margin:0 0 10px;">이용자의 개인정보는 원칙적으로 서비스 이용 목적이 달성되면 지체 없이 파기합니다.</p>
        <p style="margin:0 0 4px;">댓글 정보: 게시글 삭제 또는 작성자가 직접 삭제를 요청할 때까지 보유합니다.</p>
        <p style="margin:0 0 16px;">로그 정보: 스팸 관리 및 보안 목적으로 일정 기간 보관 후 자동 삭제됩니다.</p>

        <h2 style="font-size:1.08rem;font-weight:700;margin:0 0 8px;">3. 개인정보의 파기 절차 및 방법</h2>
        <p style="margin:0 0 4px;">절차: 목적 달성 후 내부 방침에 따라 즉시 파기합니다.</p>
        <p style="margin:0 0 16px;">방법: 전자적 파일 형태의 정보는 기록을 재생할 수 없는 기술적 방법을 사용하여 삭제합니다.</p>

        <h2 style="font-size:1.08rem;font-weight:700;margin:0 0 8px;">4. 개인정보 자동 수집 장치(쿠키) 및 광고에 관한 사항</h2>
        <p style="margin:0 0 10px;">
            본 블로그는 서비스 개선 및 Google AdSense 등 제3자 광고 시스템의 맞춤형 광고 제공을 위해 쿠키를 사용합니다.
        </p>
        <p style="margin:0 0 10px;">
            제3자 제공업체(Google 포함)는 사용자의 이전 방문 기록을 바탕으로 광고를 게재하며, 이용자는 Google 광고 설정을 통해 맞춤 설정된 광고를 해제할 수 있습니다.
        </p>
        <p style="margin:0 0 16px;">
            이용자는 브라우저 설정을 통해 쿠키 저장을 거부할 수 있으나, 이 경우 일부 서비스 이용에 제한이 있을 수 있습니다.
        </p>

        <h2 style="font-size:1.08rem;font-weight:700;margin:0 0 8px;">5. 정보주체의 권리와 행사방법</h2>
        <p style="margin:0 0 16px;">
            이용자는 언제든 본인이 제공한 개인정보의 열람, 수정, 삭제를 요청할 수 있습니다. 댓글 삭제를 원하실 경우 운영자에게 메일을 주시거나 직접 삭제 기능을 이용하실 수 있습니다.
        </p>

        <h2 style="font-size:1.08rem;font-weight:700;margin:0 0 8px;">6. 개인정보 보호책임자 및 연락처</h2>
        <p style="margin:0 0 4px;">본 블로그는 개인정보 관련 문의 및 불만 처리를 위해 아래와 같이 연락처를 제공하고 있습니다.</p>
        <p style="margin:0 0 16px;">- jumangocoltd2026@gmail.com</p>

        <p style="margin:0;font-size:.95rem;color:#4b5563;">시행일: 2026년 3월 20일</p>
    </article>
</section>
@endsection
