<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title'    => '구글 SEO 완벽 가이드 2025',
                'category' => 'SEO',
                'excerpt'  => '구글 검색 상위 노출을 위한 핵심 전략을 정리했습니다.',
                'content'  => "구글 SEO는 단순히 키워드를 많이 넣는 것이 아닙니다.\n\n## 핵심 원칙\n\n**1. 콘텐츠 품질이 최우선**\n검색 엔진은 사용자가 원하는 답을 제공하는 콘텐츠를 좋아합니다. 깊이 있는 내용을 작성하세요.\n\n**2. 기술적 SEO**\n페이지 속도, 모바일 최적화, 구조화된 데이터 등 기술적인 부분도 중요합니다.\n\n**3. 백링크 전략**\n신뢰할 수 있는 사이트에서의 백링크는 여전히 강력한 랭킹 시그널입니다.\n\n## 실전 팁\n\n- 제목 태그에 핵심 키워드 포함\n- 메타 설명은 클릭을 유도하는 문구로\n- 내부 링크 구조 최적화\n- 이미지 alt 텍스트 작성",
                'published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title'    => '라라벨로 빠르게 웹 서비스 만들기',
                'category' => '개발',
                'excerpt'  => 'PHP 프레임워크 라라벨을 사용해서 실용적인 웹 서비스를 구축하는 방법을 알아봅니다.',
                'content'  => "라라벨은 PHP 생태계에서 가장 인기 있는 프레임워크입니다.\n\n## 왜 라라벨인가?\n\n- **Eloquent ORM**: 직관적인 데이터베이스 조작\n- **Blade 템플릿**: 강력하고 유연한 뷰 시스템\n- **Artisan CLI**: 개발 생산성을 높이는 명령어 도구\n- **풍부한 에코시스템**: Livewire, Filament 등 다양한 패키지\n\n## 시작하기\n\n```bash\ncomposer create-project laravel/laravel myapp\ncd myapp\nphp artisan serve\n```\n\n위 명령어만 실행하면 바로 개발을 시작할 수 있습니다.",
                'published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title'    => 'AI 시대, 콘텐츠 마케팅의 변화',
                'category' => '마케팅',
                'excerpt'  => 'ChatGPT, Claude 등 AI 도구가 콘텐츠 마케팅을 어떻게 바꾸고 있는지 분석합니다.',
                'content'  => "AI가 콘텐츠 생성 방식을 근본적으로 바꾸고 있습니다.\n\n## AI 도구 활용 방법\n\n**1. 아이디어 발굴**\nAI에게 특정 주제로 10가지 블로그 아이디어를 요청하면 빠르게 콘텐츠 캘린더를 채울 수 있습니다.\n\n**2. 초고 작성**\nAI가 작성한 초고를 바탕으로 인간의 경험과 인사이트를 더해 완성도 높은 글을 만듭니다.\n\n**3. SEO 최적화**\n키워드 리서치, 메타 설명 작성, 제목 A/B 테스트 등에서 AI의 도움을 받을 수 있습니다.\n\n## 주의사항\n\nAI 콘텐츠만으로는 한계가 있습니다. 독자의 신뢰를 얻으려면 고유한 경험과 전문성이 필요합니다.",
                'published' => true,
                'published_at' => now()->subDays(1),
            ],
        ];

        foreach ($posts as $data) {
            $data['slug'] = Str::slug($data['title']);
            Post::create($data);
        }
    }
}
