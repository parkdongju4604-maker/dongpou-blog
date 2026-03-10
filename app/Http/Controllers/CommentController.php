<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class CommentController extends Controller
{
    /** POST /posts/{post}/comments */
    public function store(Request $request, Post $post)
    {
        // 발행된 글에만 댓글 허용
        if (!$post->published || ($post->published_at && $post->published_at->isFuture())) {
            abort(403);
        }

        $request->validate([
            'author_name' => 'required|string|max:50',
            'password'    => 'required|string|min:4|max:30',
            'content'     => 'required|string|min:2|max:2000',
            'parent_id'   => 'nullable|integer|exists:comments,id',
            'website'     => 'max:0',   // honeypot: 값이 있으면 실패
        ], [
            'author_name.required' => '이름을 입력해주세요.',
            'password.required'    => '비밀번호를 입력해주세요.',
            'password.min'         => '비밀번호는 최소 4자 이상이어야 합니다.',
            'content.required'     => '댓글 내용을 입력해주세요.',
            'content.min'          => '댓글은 최소 2자 이상이어야 합니다.',
            'content.max'          => '댓글은 2000자를 초과할 수 없습니다.',
            'website.max'          => '댓글 등록에 실패했습니다.',  // 봇 차단
        ]);

        $ipHash  = hash('sha256', $request->ip() . config('app.key'));
        $content = trim($request->content);

        // IP 레이트 리밋 (10분 5개)
        $rateLimitKey = 'comment:' . $ipHash;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            return back()->withErrors(['content' => '잠시 후 다시 시도해주세요. (10분에 5개 제한)'])
                         ->withInput();
        }
        RateLimiter::hit($rateLimitKey, 600);

        // 스팸 점수 계산
        $honeypot  = $request->input('website_url', ''); // 두 번째 허니팟
        $spamScore = Comment::calcSpamScore($content, $honeypot, $ipHash, $post->id);
        $isSpam    = $spamScore >= 10;

        // parent_id 검증 (같은 포스트 소속, 최상위 댓글만)
        $parentId = null;
        if ($request->filled('parent_id')) {
            $parent = Comment::where('id', $request->parent_id)
                              ->where('post_id', $post->id)
                              ->whereNull('parent_id')
                              ->first();
            $parentId = $parent?->id;
        }

        Comment::create([
            'post_id'       => $post->id,
            'parent_id'     => $parentId,
            'author_name'   => $request->author_name,
            'author_email'  => $request->author_email ?: null,
            'password_hash' => $request->password ? Hash::make($request->password) : null,
            'content'       => $content,
            'ip_hash'       => $ipHash,
            'is_approved'   => !$isSpam,
            'is_spam'       => $isSpam,
            'spam_score'    => $spamScore,
        ]);

        if ($isSpam) {
            return back()->with('comment_notice', '댓글이 스팸으로 분류되어 검토 중입니다.')
                         ->withFragment('comments');
        }

        return back()->with('comment_notice', '댓글이 등록되었습니다. 감사합니다!')
                     ->withFragment('comments');
    }

    /** DELETE /comments/{comment} */
    public function destroy(Request $request, Comment $comment)
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => '비밀번호를 입력해주세요.',
        ]);

        if (!$comment->checkPassword($request->password)) {
            return back()->withErrors(['delete_' . $comment->id => '비밀번호가 틀렸습니다.'])
                         ->withFragment('comment-' . $comment->id);
        }

        $comment->delete();
        return back()->with('comment_notice', '댓글이 삭제되었습니다.')
                     ->withFragment('comments');
    }
}
