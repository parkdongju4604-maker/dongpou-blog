@extends('layouts.admin')
@section('title', '카테고리 관리')
@section('page-title', '카테고리')

@section('content')
<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start">

    {{-- 카테고리 목록 --}}
    <div class="card">
        <div class="card-header">
            <h3>카테고리 목록</h3>
            <div style="display:flex;align-items:center;gap:8px">
                <span style="font-size:.8rem;color:#94a3b8">{{ $categories->count() }}개</span>
                <form method="POST" action="{{ route('admin.categories.suggestions') }}"
                      onsubmit="return confirm('추천 카테고리 3개를 자동 생성할까요?')">
                    @csrf
                    <input type="hidden" name="apply" value="1">
                    <button type="submit" class="btn btn-success btn-sm">자동 생성</button>
                </form>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>순서</th>
                        <th>카테고리명</th>
                        <th>슬러그</th>
                        <th>설명</th>
                        <th>글 수</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td style="color:#94a3b8;width:50px">{{ $cat->sort_order }}</td>
                        <td style="font-weight:600">{{ $cat->name }}</td>
                        <td><code style="font-size:.78rem;background:#f1f5f9;padding:2px 6px;border-radius:4px">{{ $cat->slug }}</code></td>
                        <td style="color:#64748b">{{ $cat->description ?: '—' }}</td>
                        <td><span class="badge badge-purple">{{ $cat->post_count }}</span></td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <button type="button" class="btn btn-secondary btn-sm"
                                        onclick="openEdit({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->slug) }}', '{{ addslashes($cat->description) }}', {{ $cat->sort_order }})">
                                    수정
                                </button>
                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                                      onsubmit="return confirm('카테고리를 삭제하시겠습니까?\n삭제해도 기존 글의 카테고리는 유지됩니다.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">삭제</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:32px;color:#94a3b8">카테고리가 없습니다.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 추가/수정 폼 --}}
    <div>
        {{-- 자동 생성 --}}
        <div class="card" style="margin-bottom:16px">
            <div class="card-header"><h3>AI 카테고리 추천/생성</h3></div>
            <div class="card-body">
                <p style="font-size:.82rem;color:#64748b;line-height:1.6;margin-bottom:12px">
                    현재 블로그 URL 기준으로 외부 관리서버에 요청하여 추천 카테고리 3개를 생성합니다.
                </p>
                <form method="POST" action="{{ route('admin.categories.suggestions') }}"
                      onsubmit="return confirm('추천 카테고리 3개를 자동 생성할까요?')">
                    @csrf
                    <input type="hidden" name="apply" value="1">
                    <button type="submit" class="btn btn-success" style="width:100%;justify-content:center">
                        카테고리 3개 자동 생성
                    </button>
                </form>
            </div>
        </div>

        {{-- 추가 폼 --}}
        <div class="card" id="form-add">
            <div class="card-header"><h3>카테고리 추가</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    @if($errors->any())
                        <div class="alert alert-danger" style="margin-bottom:14px">{{ $errors->first() }}</div>
                    @endif
                    <div class="form-group">
                        <label class="form-label">카테고리명 *</label>
                        <input type="text" name="name" id="add-name" class="form-control"
                               value="{{ old('name') }}" placeholder="예: 개발" required
                               oninput="autoSlug(this.value, 'add-slug', addSlugEdited)">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="display:flex;justify-content:space-between">
                            <span>슬러그 <span style="color:#94a3b8;font-weight:400">(URL 경로)</span></span>
                            <span style="font-size:.72rem;color:#94a3b8">영소문자·숫자·하이픈만</span>
                        </label>
                        <input type="text" name="slug" id="add-slug" class="form-control"
                               value="{{ old('slug') }}" placeholder="비워두면 카테고리명에서 자동 생성"
                               oninput="addSlugEdited = true" style="font-family:monospace;font-size:.875rem">
                        @error('slug')
                            <p style="font-size:.75rem;color:#ef4444;margin-top:4px">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">설명 <span style="color:#94a3b8;font-weight:400">(선택)</span></label>
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="카테고리 설명">
                    </div>
                    <div class="form-group">
                        <label class="form-label">정렬 순서 <span style="color:#94a3b8;font-weight:400">(숫자 낮을수록 앞)</span></label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">추가</button>
                </form>
            </div>
        </div>

        {{-- 수정 폼 (숨김) --}}
        <div class="card" id="form-edit" style="display:none;margin-top:16px">
            <div class="card-header">
                <h3>카테고리 수정</h3>
                <button type="button" onclick="closeEdit()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:1.2rem">✕</button>
            </div>
            <div class="card-body">
                <form method="POST" id="edit-form">
                    @csrf @method('PUT')
                    <div class="form-group">
                        <label class="form-label">카테고리명 *</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required
                               oninput="autoSlug(this.value, 'edit-slug', editSlugEdited)">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="display:flex;justify-content:space-between">
                            <span>슬러그</span>
                            <span style="font-size:.72rem;color:#94a3b8">영소문자·숫자·하이픈만</span>
                        </label>
                        <input type="text" name="slug" id="edit-slug" class="form-control"
                               oninput="editSlugEdited = true"
                               style="font-family:monospace;font-size:.875rem">
                    </div>
                    <div class="form-group">
                        <label class="form-label">설명</label>
                        <input type="text" name="description" id="edit-description" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">정렬 순서</label>
                        <input type="number" name="sort_order" id="edit-sort" class="form-control" min="0">
                    </div>
                    <div style="display:flex;gap:8px">
                        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">저장</button>
                        <button type="button" onclick="closeEdit()" class="btn btn-secondary">취소</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const baseUrl = '{{ url("admin/categories") }}';
let addSlugEdited  = false;
let editSlugEdited = false;

// 이름 → 슬러그 자동 변환 (직접 편집하지 않은 경우에만)
function autoSlug(name, targetId, isEdited) {
    if (isEdited) return;
    const slug = name.toLowerCase()
        .replace(/[^\w\s\-가-힣]/g, '')
        .replace(/\s+/g, '-')
        .replace(/[^a-z0-9\-]/g, '')   // 한글 등 비ASCII 제거
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    document.getElementById(targetId).value = slug;
}

function openEdit(id, name, slug, desc, sort) {
    document.getElementById('edit-form').action = baseUrl + '/' + id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-slug').value = slug;
    document.getElementById('edit-description').value = desc;
    document.getElementById('edit-sort').value = sort;
    editSlugEdited = true;  // 기존 슬러그는 수동 편집 모드로
    document.getElementById('form-edit').style.display = 'block';
    document.getElementById('form-edit').scrollIntoView({ behavior: 'smooth' });
}

function closeEdit() {
    document.getElementById('form-edit').style.display = 'none';
    editSlugEdited = false;
}
</script>
@endpush
