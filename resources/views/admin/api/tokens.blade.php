@extends('layouts.admin')
@section('title', 'API 토큰 관리')
@section('page-title', 'API 토큰 관리')

@section('content')

{{-- 새 토큰 발급 직후 1회 표시 --}}
@if(session('new_token'))
<div style="background:#064e3b;border:2px solid #10b981;border-radius:12px;padding:20px 24px;margin-bottom:24px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
        <span style="font-size:1.2rem">🔑</span>
        <strong style="color:#ecfdf5;font-size:.95rem">
            '{{ session('new_token_name') }}' 토큰이 발급되었습니다. 지금 바로 복사하세요!
        </strong>
    </div>
    <p style="font-size:.78rem;color:#6ee7b7;margin-bottom:10px">⚠️ 이 토큰은 지금만 표시됩니다. 창을 닫으면 다시 볼 수 없습니다.</p>
    <div style="display:flex;align-items:center;gap:8px">
        <code id="new-token-val"
              style="flex:1;background:#022c22;color:#34d399;padding:11px 16px;border-radius:8px;font-size:.85rem;font-family:monospace;word-break:break-all;border:1px solid #065f46">
            {{ session('new_token') }}
        </code>
        <button onclick="copyNewToken()"
                style="padding:10px 18px;background:#10b981;color:#fff;border:none;border-radius:8px;font-size:.85rem;font-weight:600;cursor:pointer;white-space:nowrap"
                id="copy-btn">복사</button>
    </div>
</div>
@endif

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 18px;margin-bottom:20px;color:#166534;font-size:.875rem">
    ✅ {{ session('success') }}
</div>
@endif

<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3>새 토큰 발급</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.api-tokens.store') }}"
              style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
            @csrf
            <div style="flex:1;min-width:180px">
                <label style="display:block;font-size:.75rem;font-weight:700;color:#64748b;margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em">토큰 이름</label>
                <input type="text" name="name" required
                       placeholder="예: 블로그 자동화 스크립트"
                       style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none"
                       onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e2e8f0'">
                @error('name')<p style="color:#ef4444;font-size:.75rem;margin-top:4px">{{ $message }}</p>@enderror
            </div>
            <div style="min-width:180px">
                <label style="display:block;font-size:.75rem;font-weight:700;color:#64748b;margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em">만료일 <span style="font-weight:400;color:#94a3b8">(선택, 비우면 영구)</span></label>
                <input type="datetime-local" name="expires_at"
                       style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none"
                       onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e2e8f0'">
                @error('expires_at')<p style="color:#ef4444;font-size:.75rem;margin-top:4px">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn btn-primary" style="padding:9px 22px;white-space:nowrap">
                + 토큰 발급
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>발급된 토큰 <span style="font-size:.8rem;color:#94a3b8;font-weight:400">{{ $tokens->count() }}개</span></h3>
        <a href="{{ route('admin.api-docs') }}" class="btn btn-secondary btn-sm">📄 API 문서 보기</a>
    </div>
    <div style="overflow-x:auto">
        <table class="table">
            <thead>
                <tr>
                    <th>이름</th>
                    <th>토큰 Prefix</th>
                    <th>상태</th>
                    <th>마지막 사용</th>
                    <th>만료일</th>
                    <th>발급일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tokens as $token)
                <tr>
                    <td style="font-weight:600;color:#1e293b">{{ $token->name }}</td>
                    <td>
                        <code style="background:#f1f5f9;color:#475569;padding:3px 9px;border-radius:5px;font-size:.8rem">
                            {{ $token->token_prefix }}…
                        </code>
                    </td>
                    <td>
                        @if($token->isExpired())
                            <span class="badge" style="background:#fee2e2;color:#dc2626">만료됨</span>
                        @else
                            <span class="badge" style="background:#dcfce7;color:#16a34a">활성</span>
                        @endif
                    </td>
                    <td style="color:#64748b;font-size:.85rem">
                        {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : '사용 안 함' }}
                    </td>
                    <td style="color:#64748b;font-size:.85rem">
                        {{ $token->expires_at ? $token->expires_at->format('Y.m.d H:i') : '영구' }}
                    </td>
                    <td style="color:#94a3b8;font-size:.85rem">
                        {{ $token->created_at->format('Y.m.d') }}
                    </td>
                    <td>
                        <form action="{{ route('admin.api-tokens.destroy', $token) }}" method="POST"
                              onsubmit="return confirm('\'{{ $token->name }}\' 토큰을 삭제하시겠습니까?\n삭제 후 해당 토큰으로의 API 요청이 모두 실패합니다.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">삭제</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8">
                        발급된 토큰이 없습니다. 위에서 새 토큰을 발급하세요.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function copyNewToken() {
    const val = document.getElementById('new-token-val').innerText.trim();
    navigator.clipboard.writeText(val).then(() => {
        const btn = document.getElementById('copy-btn');
        btn.textContent = '✓ 복사됨';
        btn.style.background = '#059669';
        setTimeout(() => { btn.textContent = '복사'; btn.style.background = '#10b981'; }, 2000);
    });
}
</script>
@endpush
