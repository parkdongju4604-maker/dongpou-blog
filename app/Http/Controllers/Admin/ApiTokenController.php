<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = ApiToken::orderByDesc('created_at')->get();
        return view('admin.api.tokens', compact('tokens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'expires_at' => 'nullable|date|after:now',
        ], [
            'name.required'      => '토큰 이름을 입력해주세요.',
            'expires_at.after'   => '만료일은 현재 시간 이후여야 합니다.',
        ]);

        [$plain, $token] = ApiToken::generate(
            $request->name,
            $request->expires_at ?: null
        );

        return redirect()
            ->route('admin.api-tokens.index')
            ->with('new_token', $plain)
            ->with('new_token_name', $token->name);
    }

    public function destroy(ApiToken $apiToken)
    {
        $apiToken->delete();
        return redirect()
            ->route('admin.api-tokens.index')
            ->with('success', "'{$apiToken->name}' 토큰이 삭제되었습니다.");
    }

    public function docs()
    {
        $baseUrl = url('/api');
        return view('admin.api.docs', compact('baseUrl'));
    }
}
