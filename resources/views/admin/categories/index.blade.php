@extends('layouts.admin')
@section('title', '카테고리 관리')
@section('page-title', '카테고리')

@section('content')
<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start">

    {{-- 카테고리 목록 --}}
    <div class="card">
        <div class="card-header">
            <h3>카테고리 목록</h3>
            <span style="font-size:.8rem;color:#94a3b8">{{ $categories->count() }}개</span>
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
                                        onclick="openEdit({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description) }}', {{ $cat->sort_order }})">
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
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="예: 개발" required>
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
                        <input type="text" name="name" id="edit-name" class="form-control" required>
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

function openEdit(id, name, desc, sort) {
    document.getElementById('edit-form').action = baseUrl + '/' + id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-description').value = desc;
    document.getElementById('edit-sort').value = sort;
    document.getElementById('form-edit').style.display = 'block';
    document.getElementById('form-edit').scrollIntoView({ behavior: 'smooth' });
}

function closeEdit() {
    document.getElementById('form-edit').style.display = 'none';
}
</script>
@endpush
