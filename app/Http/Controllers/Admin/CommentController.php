<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $query = Comment::with(['post:id,title,slug', 'parent:id,author_name'])
                        ->orderByDesc('created_at');

        match ($filter) {
            'spam'     => $query->where('is_spam', true),
            'pending'  => $query->where('is_approved', false)->where('is_spam', false),
            'approved' => $query->where('is_approved', true)->where('is_spam', false),
            default    => null,
        };

        $comments = $query->paginate(30)->withQueryString();

        $counts = [
            'all'      => Comment::count(),
            'approved' => Comment::where('is_approved', true)->where('is_spam', false)->count(),
            'pending'  => Comment::where('is_approved', false)->where('is_spam', false)->count(),
            'spam'     => Comment::where('is_spam', true)->count(),
        ];

        return view('admin.comments.index', compact('comments', 'filter', 'counts'));
    }

    /** 승인 토글 */
    public function approve(Comment $comment)
    {
        $comment->update([
            'is_approved' => !$comment->is_approved,
            'is_spam'     => false,
        ]);
        return back()->with('success', $comment->is_approved ? '댓글을 승인했습니다.' : '댓글 승인을 취소했습니다.');
    }

    /** 스팸 처리 */
    public function spam(Comment $comment)
    {
        $comment->update(['is_spam' => true, 'is_approved' => false]);
        return back()->with('success', '스팸으로 처리했습니다.');
    }

    /** 단일 삭제 */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', '댓글을 삭제했습니다.');
    }

    /** 스팸 일괄 삭제 */
    public function destroySpam()
    {
        $count = Comment::where('is_spam', true)->delete();
        return back()->with('success', "스팸 댓글 {$count}개를 삭제했습니다.");
    }
}
