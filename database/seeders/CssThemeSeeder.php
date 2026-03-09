<?php

namespace Database\Seeders;

use App\Models\CssTheme;
use Illuminate\Database\Seeder;

class CssThemeSeeder extends Seeder
{
    public function run(): void
    {
        CssTheme::truncate();

        // ── 테마 1: Modern Indigo (기본, 현재 디자인) ──────────────────
        $theme1 = <<<'CSS'
/* ── Theme: Modern Indigo ── */
:root {
    --primary: #4f46e5;
    --primary-dark: #4338ca;
    --primary-light: #eef2ff;
    --primary-mid: #e0e7ff;
    --bg: #ffffff;
    --bg-soft: #f8fafc;
    --surface: #ffffff;
    --border: #f0f0f5;
    --border-mid: #e8e8ee;
    --text: #1a1a2e;
    --text-soft: #6b7280;
    --text-muted: #9ca3af;
    --header-bg: rgba(255,255,255,.92);
    --footer-bg: #0f0f23;
    --footer-text: rgba(255,255,255,.5);
    --card-bg: #ffffff;
    --card-border: #f0f0f5;
    --code-bg: #f3f4f6;
    --code-color: #e11d48;
    --pre-bg: #0f172a;
    --pre-color: #e2e8f0;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
body {
    font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg); color: var(--text);
    line-height: 1.7; -webkit-font-smoothing: antialiased;
    display: flex; flex-direction: column; min-height: 100vh;
}
a { color: inherit; text-decoration: none; }
img { max-width: 100%; height: auto; display: block; }

/* HEADER */
header {
    position: sticky; top: 0; z-index: 200;
    background: var(--header-bg);
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(0,0,0,.06);
}
.header-inner {
    max-width: 1200px; margin: 0 auto; padding: 0 28px;
    display: flex; align-items: center; justify-content: space-between; height: 64px;
}
.logo { font-size: 1.3rem; font-weight: 800; color: #0f0f23; letter-spacing: -0.5px; display: flex; align-items: center; gap: 8px; }
.logo-dot { width: 8px; height: 8px; background: var(--primary); border-radius: 50%; display: inline-block; }
.nav-desktop { display: flex; align-items: center; gap: 2px; }
.nav-desktop a { padding: 7px 14px; font-size: .875rem; font-weight: 500; color: #555; border-radius: 8px; transition: all .15s; }
.nav-desktop a:hover { background: #f4f4f8; color: #0f0f23; }
.nav-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; border-radius: 8px; color: #555; }
.nav-toggle:hover { background: #f4f4f8; }
.nav-mobile { display: none; position: fixed; inset: 0; z-index: 300; }
.nav-mobile.open { display: block; }
.nav-overlay { position: absolute; inset: 0; background: rgba(0,0,0,.5); }
.nav-drawer { position: absolute; top: 0; right: 0; bottom: 0; width: min(300px, 88vw); background: #fff; display: flex; flex-direction: column; padding: 24px 20px; transform: translateX(100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
.nav-mobile.open .nav-drawer { transform: translateX(0); }
.nav-drawer-close { align-self: flex-end; background: none; border: none; cursor: pointer; color: #888; margin-bottom: 20px; padding: 4px; }
.nav-drawer a { display: block; padding: 14px 4px; font-size: 1rem; font-weight: 600; color: #1a1a2e; border-bottom: 1px solid #f1f1f5; }
.nav-drawer a:hover { color: var(--primary); }

/* MAIN */
main { max-width: 1200px; margin: 0 auto; padding: 48px 28px; flex: 1; width: 100%; }

/* HERO */
.hero { text-align: center; padding: 72px 0 56px; border-bottom: 1px solid var(--border); margin-bottom: 48px; }
.hero-eyebrow { display: inline-block; font-size: .75rem; font-weight: 700; color: var(--primary); letter-spacing: 2px; text-transform: uppercase; background: var(--primary-light); padding: 5px 14px; border-radius: 20px; margin-bottom: 20px; }
.hero h1 { font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 800; color: #0f0f23; line-height: 1.2; margin-bottom: 14px; letter-spacing: -1px; word-break: keep-all; }
.hero p { color: var(--text-soft); font-size: clamp(.95rem, 2vw, 1.15rem); }

/* CATEGORY TABS */
.category-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 36px; }
.tab { padding: 7px 18px; border-radius: 24px; font-size: .82rem; font-weight: 600; background: #f5f5f8; border: 1.5px solid transparent; color: #555; transition: all .18s; white-space: nowrap; min-height: 38px; display: inline-flex; align-items: center; cursor: pointer; }
.tab:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }

/* CARD GRID */
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
.card { background: var(--card-bg); border-radius: 16px; overflow: hidden; border: 1.5px solid var(--card-border); transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease; display: block; }
.card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,.08); border-color: var(--primary); }
.card-body { padding: 26px; }
.card-category { font-size: .7rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px; }
.card-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 10px; line-height: 1.5; color: #0f0f23; word-break: keep-all; }
.card:hover .card-title { color: var(--primary); }
.card-excerpt { font-size: .875rem; color: var(--text-soft); margin-bottom: 18px; line-height: 1.7; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.card-meta { font-size: .75rem; color: var(--text-muted); display: flex; gap: 10px; align-items: center; }
.card-meta-dot { width: 3px; height: 3px; border-radius: 50%; background: #d1d5db; }

/* POST LAYOUT */
.post-layout { display: grid; grid-template-columns: 1fr 240px; gap: 60px; align-items: start; max-width: 1080px; margin: 0 auto; }
.post-hero { padding-bottom: 32px; border-bottom: 1px solid var(--border); margin-bottom: 40px; }
.post-category-badge { display: inline-flex; align-items: center; font-size: .72rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--primary); background: var(--primary-light); padding: 5px 14px; border-radius: 20px; margin-bottom: 20px; }
.post-title { font-size: clamp(1.75rem, 4vw, 2.6rem); font-weight: 800; line-height: 1.3; color: #0f0f23; margin-bottom: 20px; word-break: keep-all; letter-spacing: -0.5px; }
.post-meta { display: flex; align-items: center; gap: 8px; font-size: .82rem; color: var(--text-muted); flex-wrap: wrap; }
.post-meta-sep { color: #d1d5db; }
.post-meta-badge { background: #f5f5f8; padding: 3px 10px; border-radius: 8px; font-weight: 600; color: #6b7280; font-size: .75rem; }
.breadcrumb { display: flex; align-items: center; gap: 6px; list-style: none; font-size: .78rem; color: var(--text-muted); flex-wrap: wrap; margin-bottom: 28px; }
.breadcrumb a { color: var(--text-muted); transition: color .15s; }
.breadcrumb a:hover { color: var(--primary); }
.breadcrumb-sep { font-size: .65rem; color: #d1d5db; }

/* POST CONTENT */
.post-content { font-size: 1.05rem; line-height: 1.95; color: #374151; word-break: keep-all; }
.post-content > *:first-child { margin-top: 0; }
.post-content h1, .post-content h2 { font-size: clamp(1.25rem, 2.5vw, 1.55rem); font-weight: 800; color: #0f0f23; margin: 2.8rem 0 1rem; padding-bottom: 10px; border-bottom: 2px solid var(--border); letter-spacing: -.3px; }
.post-content h3 { font-size: clamp(1.1rem, 2vw, 1.25rem); font-weight: 700; color: #1a1a2e; margin: 2.2rem 0 .8rem; }
.post-content h4 { font-size: 1rem; font-weight: 700; color: #374151; margin: 1.6rem 0 .6rem; }
.post-content p { margin-bottom: 1.5rem; }
.post-content strong { font-weight: 700; color: #0f0f23; }
.post-content em { font-style: italic; }
.post-content ul { list-style: none; margin: 0 0 1.5rem; padding: 0; }
.post-content ul li { padding: 4px 0 4px 22px; position: relative; color: #374151; }
.post-content ul li::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 7px; height: 7px; background: var(--primary); border-radius: 50%; opacity: .8; }
.post-content ol { margin: 0 0 1.5rem 1.6rem; padding: 0; }
.post-content ol li { padding: 3px 0; color: #374151; }
.post-content a { color: var(--primary); text-decoration: underline; text-underline-offset: 3px; }
.post-content a:hover { opacity: .75; }
.post-content hr { border: none; border-top: 2px solid var(--border); margin: 3rem 0; }
.post-content code { background: var(--code-bg); color: var(--code-color); padding: 2px 8px; border-radius: 6px; font-size: .875em; font-family: 'JetBrains Mono','Fira Code',monospace; word-break: break-all; }
.post-content pre { background: var(--pre-bg); border-radius: 12px; padding: 22px 24px; overflow-x: auto; margin-bottom: 1.8rem; box-shadow: 0 4px 20px rgba(0,0,0,.12); }
.post-content pre code { background: none; color: var(--pre-color); padding: 0; font-size: .875rem; line-height: 1.75; word-break: normal; }
.post-content blockquote { border-left: 4px solid var(--primary); padding: 16px 22px; background: var(--primary-light); border-radius: 0 12px 12px 0; margin: 0 0 1.6rem; color: #374151; }
.post-content blockquote p:last-child { margin-bottom: 0; }
.post-content img { border-radius: 12px; margin: 1.6rem auto; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
.post-content table { width: 100%; border-collapse: collapse; margin-bottom: 1.8rem; font-size: .9rem; overflow: hidden; border-radius: 10px; }
.post-content th { background: #f9fafb; font-weight: 700; color: #374151; padding: 12px 16px; border: 1px solid #e5e7eb; text-align: left; }
.post-content td { padding: 10px 16px; border: 1px solid #e5e7eb; color: #4b5563; }
.post-content tr:nth-child(even) td { background: #f9fafb; }

/* TOC */
.post-sidebar { position: sticky; top: 88px; }
.toc-box { background: #f9f9fc; border-radius: 14px; padding: 20px; border: 1.5px solid var(--border); }
.toc-title { font-size: .7rem; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 14px; }
.toc-list { list-style: none; }
.toc-list li { margin-bottom: 4px; }
.toc-list a { font-size: .8rem; color: #6b7280; line-height: 1.5; transition: color .15s; display: block; padding: 3px 0 3px 10px; border-left: 2px solid transparent; }
.toc-list a:hover, .toc-list a.active { color: var(--primary); border-left-color: var(--primary); font-weight: 600; }
.toc-list li.toc-h3 a { padding-left: 22px; font-size: .77rem; }

/* RELATED */
.related { margin-top: 56px; padding-top: 40px; border-top: 1px solid var(--border); }
.related h2 { font-size: 1.1rem; font-weight: 800; color: #0f0f23; margin-bottom: 20px; letter-spacing: -.2px; }
.related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.post-footer { margin-top: 40px; padding-top: 28px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }

/* BUTTONS */
.btn { display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; border: none; transition: all .18s; min-height: 44px; text-decoration: none; }
.btn-primary { background: var(--primary); color: #fff; }
.btn-primary:hover { background: var(--primary-dark); }
.btn-secondary { background: #f5f5f8; color: #374151; border: 1.5px solid var(--border-mid); }
.btn-secondary:hover { background: #ebebf2; border-color: #d0d0da; }

/* PAGINATION */
.pagination { margin-top: 48px; display: flex; justify-content: center; gap: 6px; flex-wrap: wrap; }
.pagination a, .pagination span { padding: 9px 14px; border-radius: 10px; background: #fff; border: 1.5px solid var(--border-mid); font-size: .83rem; color: #555; min-width: 40px; text-align: center; transition: all .15s; }
.pagination a:hover { border-color: var(--primary); color: var(--primary); }
.pagination .active span { background: var(--primary); color: #fff; border-color: var(--primary); }

/* READING PROGRESS */
#reading-progress { position: fixed; top: 0; left: 0; height: 3px; background: var(--primary); z-index: 500; width: 0%; transition: width .1s linear; }

/* FOOTER */
footer { background: var(--footer-bg); color: var(--footer-text); text-align: center; padding: 36px 24px; font-size: .83rem; margin-top: 80px; }
footer a { color: var(--footer-text); }
footer strong { color: rgba(255,255,255,.8); font-weight: 600; }

/* RESPONSIVE */
@media (max-width: 960px) { .post-layout { grid-template-columns: 1fr; gap: 0; } .post-sidebar { display: none; } }
@media (max-width: 768px) { main { padding: 32px 20px; } .header-inner { padding: 0 20px; } .hero { padding: 48px 0 36px; } .nav-desktop { display: none; } .nav-toggle { display: flex; align-items: center; } .grid { grid-template-columns: 1fr; gap: 16px; } .related-grid { grid-template-columns: 1fr; } }
@media (max-width: 480px) { .logo { font-size: 1.1rem; } .post-title { font-size: 1.55rem; } }
@media (min-width: 1100px) { .grid { grid-template-columns: repeat(3, 1fr); } }
CSS;

        // ── 테마 2: Dark Mode ─────────────────────────────────────────
        $theme2 = <<<'CSS'
/* ── Theme: Dark Mode ── */
:root {
    --primary: #818cf8;
    --primary-dark: #6366f1;
    --primary-light: rgba(129,140,248,.12);
    --primary-mid: rgba(129,140,248,.2);
    --bg: #0f172a;
    --bg-soft: #1e293b;
    --surface: #1e293b;
    --border: #1e293b;
    --border-mid: #334155;
    --text: #e2e8f0;
    --text-soft: #94a3b8;
    --text-muted: #64748b;
    --header-bg: rgba(15,23,42,.92);
    --footer-bg: #020617;
    --footer-text: rgba(255,255,255,.35);
    --card-bg: #1e293b;
    --card-border: #334155;
    --code-bg: #0f172a;
    --code-color: #f472b6;
    --pre-bg: #020617;
    --pre-color: #e2e8f0;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
body {
    font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg); color: var(--text);
    line-height: 1.7; -webkit-font-smoothing: antialiased;
    display: flex; flex-direction: column; min-height: 100vh;
}
a { color: inherit; text-decoration: none; }
img { max-width: 100%; height: auto; display: block; }

/* HEADER */
header {
    position: sticky; top: 0; z-index: 200;
    background: var(--header-bg);
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.header-inner { max-width: 1200px; margin: 0 auto; padding: 0 28px; display: flex; align-items: center; justify-content: space-between; height: 64px; }
.logo { font-size: 1.3rem; font-weight: 800; color: #f1f5f9; letter-spacing: -0.5px; display: flex; align-items: center; gap: 8px; }
.logo-dot { width: 8px; height: 8px; background: var(--primary); border-radius: 50%; display: inline-block; }
.nav-desktop { display: flex; align-items: center; gap: 2px; }
.nav-desktop a { padding: 7px 14px; font-size: .875rem; font-weight: 500; color: #94a3b8; border-radius: 8px; transition: all .15s; }
.nav-desktop a:hover { background: #1e293b; color: #f1f5f9; }
.nav-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; border-radius: 8px; color: #94a3b8; }
.nav-toggle:hover { background: #1e293b; }
.nav-mobile { display: none; position: fixed; inset: 0; z-index: 300; }
.nav-mobile.open { display: block; }
.nav-overlay { position: absolute; inset: 0; background: rgba(0,0,0,.7); }
.nav-drawer { position: absolute; top: 0; right: 0; bottom: 0; width: min(300px, 88vw); background: #1e293b; display: flex; flex-direction: column; padding: 24px 20px; transform: translateX(100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
.nav-mobile.open .nav-drawer { transform: translateX(0); }
.nav-drawer-close { align-self: flex-end; background: none; border: none; cursor: pointer; color: #64748b; margin-bottom: 20px; padding: 4px; }
.nav-drawer a { display: block; padding: 14px 4px; font-size: 1rem; font-weight: 600; color: #e2e8f0; border-bottom: 1px solid #334155; }
.nav-drawer a:hover { color: var(--primary); }

/* MAIN */
main { max-width: 1200px; margin: 0 auto; padding: 48px 28px; flex: 1; width: 100%; }

/* HERO */
.hero { text-align: center; padding: 72px 0 56px; border-bottom: 1px solid #1e293b; margin-bottom: 48px; }
.hero-eyebrow { display: inline-block; font-size: .75rem; font-weight: 700; color: var(--primary); letter-spacing: 2px; text-transform: uppercase; background: var(--primary-light); padding: 5px 14px; border-radius: 20px; margin-bottom: 20px; }
.hero h1 { font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 800; color: #f1f5f9; line-height: 1.2; margin-bottom: 14px; letter-spacing: -1px; word-break: keep-all; }
.hero p { color: var(--text-soft); font-size: clamp(.95rem, 2vw, 1.15rem); }

/* CATEGORY TABS */
.category-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 36px; }
.tab { padding: 7px 18px; border-radius: 24px; font-size: .82rem; font-weight: 600; background: #1e293b; border: 1.5px solid #334155; color: #94a3b8; transition: all .18s; white-space: nowrap; min-height: 38px; display: inline-flex; align-items: center; cursor: pointer; }
.tab:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.tab.active { background: var(--primary); color: #0f172a; border-color: var(--primary); }

/* CARD GRID */
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
.card { background: var(--card-bg); border-radius: 16px; overflow: hidden; border: 1.5px solid var(--card-border); transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease; display: block; }
.card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,.4); border-color: var(--primary); }
.card-body { padding: 26px; }
.card-category { font-size: .7rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px; }
.card-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 10px; line-height: 1.5; color: #f1f5f9; word-break: keep-all; }
.card:hover .card-title { color: var(--primary); }
.card-excerpt { font-size: .875rem; color: var(--text-soft); margin-bottom: 18px; line-height: 1.7; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.card-meta { font-size: .75rem; color: var(--text-muted); display: flex; gap: 10px; align-items: center; }
.card-meta-dot { width: 3px; height: 3px; border-radius: 50%; background: #475569; }

/* POST LAYOUT */
.post-layout { display: grid; grid-template-columns: 1fr 240px; gap: 60px; align-items: start; max-width: 1080px; margin: 0 auto; }
.post-hero { padding-bottom: 32px; border-bottom: 1px solid #1e293b; margin-bottom: 40px; }
.post-category-badge { display: inline-flex; align-items: center; font-size: .72rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--primary); background: var(--primary-light); padding: 5px 14px; border-radius: 20px; margin-bottom: 20px; }
.post-title { font-size: clamp(1.75rem, 4vw, 2.6rem); font-weight: 800; line-height: 1.3; color: #f1f5f9; margin-bottom: 20px; word-break: keep-all; letter-spacing: -0.5px; }
.post-meta { display: flex; align-items: center; gap: 8px; font-size: .82rem; color: var(--text-muted); flex-wrap: wrap; }
.post-meta-sep { color: #334155; }
.post-meta-badge { background: #1e293b; padding: 3px 10px; border-radius: 8px; font-weight: 600; color: #64748b; font-size: .75rem; border: 1px solid #334155; }
.breadcrumb { display: flex; align-items: center; gap: 6px; list-style: none; font-size: .78rem; color: var(--text-muted); flex-wrap: wrap; margin-bottom: 28px; }
.breadcrumb a { color: var(--text-muted); transition: color .15s; }
.breadcrumb a:hover { color: var(--primary); }
.breadcrumb-sep { font-size: .65rem; color: #334155; }

/* POST CONTENT */
.post-content { font-size: 1.05rem; line-height: 1.95; color: #cbd5e1; word-break: keep-all; }
.post-content > *:first-child { margin-top: 0; }
.post-content h1, .post-content h2 { font-size: clamp(1.25rem, 2.5vw, 1.55rem); font-weight: 800; color: #f1f5f9; margin: 2.8rem 0 1rem; padding-bottom: 10px; border-bottom: 2px solid #1e293b; letter-spacing: -.3px; }
.post-content h3 { font-size: clamp(1.1rem, 2vw, 1.25rem); font-weight: 700; color: #e2e8f0; margin: 2.2rem 0 .8rem; }
.post-content h4 { font-size: 1rem; font-weight: 700; color: #cbd5e1; margin: 1.6rem 0 .6rem; }
.post-content p { margin-bottom: 1.5rem; }
.post-content strong { font-weight: 700; color: #f1f5f9; }
.post-content em { font-style: italic; }
.post-content ul { list-style: none; margin: 0 0 1.5rem; padding: 0; }
.post-content ul li { padding: 4px 0 4px 22px; position: relative; color: #cbd5e1; }
.post-content ul li::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 7px; height: 7px; background: var(--primary); border-radius: 50%; opacity: .8; }
.post-content ol { margin: 0 0 1.5rem 1.6rem; padding: 0; }
.post-content ol li { padding: 3px 0; color: #cbd5e1; }
.post-content a { color: var(--primary); text-decoration: underline; text-underline-offset: 3px; }
.post-content a:hover { opacity: .75; }
.post-content hr { border: none; border-top: 2px solid #1e293b; margin: 3rem 0; }
.post-content code { background: var(--code-bg); color: var(--code-color); padding: 2px 8px; border-radius: 6px; font-size: .875em; font-family: 'JetBrains Mono','Fira Code',monospace; word-break: break-all; border: 1px solid #334155; }
.post-content pre { background: var(--pre-bg); border-radius: 12px; padding: 22px 24px; overflow-x: auto; margin-bottom: 1.8rem; box-shadow: 0 4px 20px rgba(0,0,0,.4); border: 1px solid #1e293b; }
.post-content pre code { background: none; color: var(--pre-color); padding: 0; font-size: .875rem; line-height: 1.75; word-break: normal; border: none; }
.post-content blockquote { border-left: 4px solid var(--primary); padding: 16px 22px; background: var(--primary-light); border-radius: 0 12px 12px 0; margin: 0 0 1.6rem; color: #cbd5e1; }
.post-content blockquote p:last-child { margin-bottom: 0; }
.post-content img { border-radius: 12px; margin: 1.6rem auto; box-shadow: 0 4px 20px rgba(0,0,0,.4); }
.post-content table { width: 100%; border-collapse: collapse; margin-bottom: 1.8rem; font-size: .9rem; overflow: hidden; border-radius: 10px; }
.post-content th { background: #1e293b; font-weight: 700; color: #e2e8f0; padding: 12px 16px; border: 1px solid #334155; text-align: left; }
.post-content td { padding: 10px 16px; border: 1px solid #334155; color: #94a3b8; }
.post-content tr:nth-child(even) td { background: #1e293b; }

/* TOC */
.post-sidebar { position: sticky; top: 88px; }
.toc-box { background: #1e293b; border-radius: 14px; padding: 20px; border: 1.5px solid #334155; }
.toc-title { font-size: .7rem; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: #475569; margin-bottom: 14px; }
.toc-list { list-style: none; }
.toc-list li { margin-bottom: 4px; }
.toc-list a { font-size: .8rem; color: #64748b; line-height: 1.5; transition: color .15s; display: block; padding: 3px 0 3px 10px; border-left: 2px solid transparent; }
.toc-list a:hover, .toc-list a.active { color: var(--primary); border-left-color: var(--primary); font-weight: 600; }
.toc-list li.toc-h3 a { padding-left: 22px; font-size: .77rem; }

/* RELATED */
.related { margin-top: 56px; padding-top: 40px; border-top: 1px solid #1e293b; }
.related h2 { font-size: 1.1rem; font-weight: 800; color: #f1f5f9; margin-bottom: 20px; }
.related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.post-footer { margin-top: 40px; padding-top: 28px; border-top: 1px solid #1e293b; display: flex; align-items: center; justify-content: space-between; }

/* BUTTONS */
.btn { display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; border: none; transition: all .18s; min-height: 44px; text-decoration: none; }
.btn-primary { background: var(--primary); color: #0f172a; }
.btn-primary:hover { background: var(--primary-dark); }
.btn-secondary { background: #1e293b; color: #e2e8f0; border: 1.5px solid #334155; }
.btn-secondary:hover { background: #334155; }

/* PAGINATION */
.pagination { margin-top: 48px; display: flex; justify-content: center; gap: 6px; flex-wrap: wrap; }
.pagination a, .pagination span { padding: 9px 14px; border-radius: 10px; background: #1e293b; border: 1.5px solid #334155; font-size: .83rem; color: #94a3b8; min-width: 40px; text-align: center; transition: all .15s; }
.pagination a:hover { border-color: var(--primary); color: var(--primary); }
.pagination .active span { background: var(--primary); color: #0f172a; border-color: var(--primary); }

/* READING PROGRESS */
#reading-progress { position: fixed; top: 0; left: 0; height: 3px; background: var(--primary); z-index: 500; width: 0%; transition: width .1s linear; }

/* FOOTER */
footer { background: var(--footer-bg); color: var(--footer-text); text-align: center; padding: 36px 24px; font-size: .83rem; margin-top: 80px; border-top: 1px solid #1e293b; }
footer a { color: var(--footer-text); }
footer strong { color: rgba(255,255,255,.6); font-weight: 600; }

/* RESPONSIVE */
@media (max-width: 960px) { .post-layout { grid-template-columns: 1fr; gap: 0; } .post-sidebar { display: none; } }
@media (max-width: 768px) { main { padding: 32px 20px; } .header-inner { padding: 0 20px; } .hero { padding: 48px 0 36px; } .nav-desktop { display: none; } .nav-toggle { display: flex; align-items: center; } .grid { grid-template-columns: 1fr; gap: 16px; } .related-grid { grid-template-columns: 1fr; } }
@media (max-width: 480px) { .logo { font-size: 1.1rem; } .post-title { font-size: 1.55rem; } }
@media (min-width: 1100px) { .grid { grid-template-columns: repeat(3, 1fr); } }
CSS;

        // ── 테마 3: Minimal Clean ────────────────────────────────────
        $theme3 = <<<'CSS'
/* ── Theme: Minimal Clean ── */
:root {
    --primary: #16a34a;
    --primary-dark: #15803d;
    --primary-light: #f0fdf4;
    --primary-mid: #dcfce7;
    --bg: #fafaf9;
    --bg-soft: #f5f5f0;
    --surface: #ffffff;
    --border: #e7e5e4;
    --border-mid: #d6d3d1;
    --text: #1c1917;
    --text-soft: #78716c;
    --text-muted: #a8a29e;
    --header-bg: rgba(250,250,249,.95);
    --footer-bg: #1c1917;
    --footer-text: rgba(255,255,255,.45);
    --card-bg: #ffffff;
    --card-border: #e7e5e4;
    --code-bg: #f5f5f0;
    --code-color: #be185d;
    --pre-bg: #1c1917;
    --pre-color: #e7e5e4;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
body {
    font-family: 'Noto Sans KR', Georgia, 'Times New Roman', serif;
    background: var(--bg); color: var(--text);
    line-height: 1.85; -webkit-font-smoothing: antialiased;
    display: flex; flex-direction: column; min-height: 100vh;
}
a { color: inherit; text-decoration: none; }
img { max-width: 100%; height: auto; display: block; }

/* HEADER */
header {
    position: sticky; top: 0; z-index: 200;
    background: var(--header-bg);
    border-bottom: 1px solid var(--border);
}
.header-inner { max-width: 1100px; margin: 0 auto; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; height: 60px; }
.logo { font-size: 1.2rem; font-weight: 700; color: var(--text); letter-spacing: -0.3px; display: flex; align-items: center; gap: 8px; font-family: Georgia, serif; }
.logo-dot { width: 6px; height: 6px; background: var(--primary); border-radius: 50%; display: inline-block; }
.nav-desktop { display: flex; align-items: center; gap: 0; }
.nav-desktop a { padding: 7px 16px; font-size: .875rem; font-weight: 400; color: var(--text-soft); transition: color .15s; border-radius: 6px; font-family: 'Noto Sans KR', sans-serif; }
.nav-desktop a:hover { color: var(--text); }
.nav-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; color: var(--text-soft); }
.nav-mobile { display: none; position: fixed; inset: 0; z-index: 300; }
.nav-mobile.open { display: block; }
.nav-overlay { position: absolute; inset: 0; background: rgba(0,0,0,.4); }
.nav-drawer { position: absolute; top: 0; right: 0; bottom: 0; width: min(280px, 88vw); background: #fff; display: flex; flex-direction: column; padding: 28px 24px; transform: translateX(100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
.nav-mobile.open .nav-drawer { transform: translateX(0); }
.nav-drawer-close { align-self: flex-end; background: none; border: none; cursor: pointer; color: var(--text-muted); margin-bottom: 24px; padding: 4px; }
.nav-drawer a { display: block; padding: 13px 0; font-size: .95rem; color: var(--text); border-bottom: 1px solid var(--border); }
.nav-drawer a:hover { color: var(--primary); }

/* MAIN */
main { max-width: 1100px; margin: 0 auto; padding: 60px 32px; flex: 1; width: 100%; }

/* HERO */
.hero { padding: 64px 0 52px; border-bottom: 1px solid var(--border); margin-bottom: 52px; }
.hero-eyebrow { display: inline-block; font-size: .7rem; font-weight: 600; color: var(--primary); letter-spacing: 3px; text-transform: uppercase; margin-bottom: 18px; font-family: 'Noto Sans KR', sans-serif; }
.hero h1 { font-size: clamp(1.9rem, 4.5vw, 2.9rem); font-weight: 700; color: var(--text); line-height: 1.3; margin-bottom: 16px; letter-spacing: -0.5px; word-break: keep-all; font-family: Georgia, serif; }
.hero p { color: var(--text-soft); font-size: 1.05rem; max-width: 560px; font-family: 'Noto Sans KR', sans-serif; }

/* CATEGORY TABS */
.category-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 40px; }
.tab { padding: 6px 16px; border-radius: 4px; font-size: .8rem; font-weight: 500; background: transparent; border: 1px solid var(--border-mid); color: var(--text-soft); transition: all .15s; white-space: nowrap; min-height: 34px; display: inline-flex; align-items: center; cursor: pointer; font-family: 'Noto Sans KR', sans-serif; }
.tab:hover { border-color: var(--primary); color: var(--primary); }
.tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }

/* CARD GRID */
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 28px; }
.card { background: var(--card-bg); border-radius: 6px; overflow: hidden; border: 1px solid var(--card-border); transition: box-shadow .2s ease; display: block; }
.card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.08); }
.card-body { padding: 28px; }
.card-category { font-size: .68rem; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; font-family: 'Noto Sans KR', sans-serif; }
.card-title { font-size: 1.05rem; font-weight: 600; margin-bottom: 10px; line-height: 1.55; color: var(--text); word-break: keep-all; font-family: Georgia, serif; }
.card:hover .card-title { color: var(--primary); }
.card-excerpt { font-size: .875rem; color: var(--text-soft); margin-bottom: 20px; line-height: 1.75; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-family: 'Noto Sans KR', sans-serif; }
.card-meta { font-size: .73rem; color: var(--text-muted); display: flex; gap: 10px; align-items: center; font-family: 'Noto Sans KR', sans-serif; }
.card-meta-dot { width: 2px; height: 2px; border-radius: 50%; background: var(--border-mid); }

/* POST LAYOUT */
.post-layout { display: grid; grid-template-columns: 1fr 220px; gap: 64px; align-items: start; max-width: 1000px; margin: 0 auto; }
.post-hero { padding-bottom: 28px; border-bottom: 1px solid var(--border); margin-bottom: 36px; }
.post-category-badge { display: inline-block; font-size: .68rem; font-weight: 600; letter-spacing: 2.5px; text-transform: uppercase; color: var(--primary); margin-bottom: 18px; font-family: 'Noto Sans KR', sans-serif; }
.post-title { font-size: clamp(1.65rem, 3.5vw, 2.4rem); font-weight: 700; line-height: 1.35; color: var(--text); margin-bottom: 20px; word-break: keep-all; font-family: Georgia, serif; }
.post-meta { display: flex; align-items: center; gap: 8px; font-size: .8rem; color: var(--text-muted); flex-wrap: wrap; font-family: 'Noto Sans KR', sans-serif; }
.post-meta-sep { color: var(--border-mid); }
.post-meta-badge { font-weight: 500; color: var(--text-soft); font-size: .75rem; }
.breadcrumb { display: flex; align-items: center; gap: 5px; list-style: none; font-size: .77rem; color: var(--text-muted); flex-wrap: wrap; margin-bottom: 28px; font-family: 'Noto Sans KR', sans-serif; }
.breadcrumb a { color: var(--text-muted); transition: color .15s; }
.breadcrumb a:hover { color: var(--primary); }
.breadcrumb-sep { font-size: .6rem; color: var(--border-mid); }

/* POST CONTENT */
.post-content { font-size: 1.075rem; line-height: 2.0; color: #292524; word-break: keep-all; font-family: 'Noto Sans KR', sans-serif; }
.post-content > *:first-child { margin-top: 0; }
.post-content h1, .post-content h2 { font-size: clamp(1.2rem, 2.5vw, 1.45rem); font-weight: 700; color: var(--text); margin: 3rem 0 1rem; font-family: Georgia, serif; letter-spacing: -.2px; }
.post-content h3 { font-size: 1.15rem; font-weight: 600; color: var(--text); margin: 2.2rem 0 .8rem; font-family: Georgia, serif; }
.post-content h4 { font-size: 1rem; font-weight: 600; color: var(--text-soft); margin: 1.6rem 0 .6rem; }
.post-content p { margin-bottom: 1.6rem; }
.post-content strong { font-weight: 700; color: var(--text); }
.post-content em { font-style: italic; color: var(--text-soft); }
.post-content ul { list-style: disc; margin: 0 0 1.6rem 1.4rem; padding: 0; }
.post-content ul li { padding: 3px 0; color: #292524; }
.post-content ol { margin: 0 0 1.6rem 1.4rem; padding: 0; }
.post-content ol li { padding: 3px 0; color: #292524; }
.post-content a { color: var(--primary); text-decoration: underline; text-underline-offset: 3px; }
.post-content a:hover { opacity: .75; }
.post-content hr { border: none; border-top: 1px solid var(--border); margin: 3.5rem 0; }
.post-content code { background: var(--code-bg); color: var(--code-color); padding: 2px 6px; border-radius: 4px; font-size: .875em; font-family: 'JetBrains Mono','Fira Code',monospace; }
.post-content pre { background: var(--pre-bg); border-radius: 8px; padding: 20px 22px; overflow-x: auto; margin-bottom: 1.8rem; }
.post-content pre code { background: none; color: var(--pre-color); padding: 0; font-size: .875rem; line-height: 1.75; }
.post-content blockquote { border-left: 3px solid var(--border-mid); padding: 12px 20px; color: var(--text-soft); margin: 0 0 1.6rem; font-style: italic; }
.post-content blockquote p:last-child { margin-bottom: 0; }
.post-content img { border-radius: 8px; margin: 2rem auto; }
.post-content table { width: 100%; border-collapse: collapse; margin-bottom: 1.8rem; font-size: .9rem; }
.post-content th { font-weight: 600; color: var(--text); padding: 10px 14px; border-bottom: 2px solid var(--border); text-align: left; }
.post-content td { padding: 9px 14px; border-bottom: 1px solid var(--border); color: var(--text-soft); }

/* TOC */
.post-sidebar { position: sticky; top: 80px; }
.toc-box { padding: 0; }
.toc-title { font-size: .68rem; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 12px; font-family: 'Noto Sans KR', sans-serif; }
.toc-list { list-style: none; border-left: 1px solid var(--border); }
.toc-list li { margin-bottom: 2px; }
.toc-list a { font-size: .78rem; color: var(--text-muted); line-height: 1.5; display: block; padding: 3px 0 3px 12px; border-left: 2px solid transparent; margin-left: -1px; transition: all .15s; }
.toc-list a:hover, .toc-list a.active { color: var(--primary); border-left-color: var(--primary); }
.toc-list li.toc-h3 a { padding-left: 22px; font-size: .74rem; }

/* RELATED */
.related { margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--border); }
.related h2 { font-size: 1rem; font-weight: 700; color: var(--text); margin-bottom: 24px; font-family: 'Noto Sans KR', sans-serif; letter-spacing: .02em; text-transform: uppercase; font-size: .8rem; color: var(--text-muted); }
.related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.post-footer { margin-top: 40px; padding-top: 28px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }

/* BUTTONS */
.btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 20px; border-radius: 6px; font-size: .875rem; font-weight: 500; cursor: pointer; border: none; transition: all .15s; min-height: 42px; text-decoration: none; font-family: 'Noto Sans KR', sans-serif; }
.btn-primary { background: var(--primary); color: #fff; }
.btn-primary:hover { background: var(--primary-dark); }
.btn-secondary { background: transparent; color: var(--text-soft); border: 1px solid var(--border-mid); }
.btn-secondary:hover { background: var(--bg-soft); color: var(--text); }

/* PAGINATION */
.pagination { margin-top: 56px; display: flex; justify-content: center; gap: 4px; flex-wrap: wrap; }
.pagination a, .pagination span { padding: 8px 13px; border-radius: 4px; background: transparent; border: 1px solid var(--border); font-size: .82rem; color: var(--text-soft); min-width: 38px; text-align: center; transition: all .15s; }
.pagination a:hover { border-color: var(--primary); color: var(--primary); }
.pagination .active span { background: var(--primary); color: #fff; border-color: var(--primary); }

/* READING PROGRESS */
#reading-progress { position: fixed; top: 0; left: 0; height: 2px; background: var(--primary); z-index: 500; width: 0%; transition: width .1s linear; }

/* FOOTER */
footer { background: var(--footer-bg); color: var(--footer-text); text-align: center; padding: 32px 24px; font-size: .82rem; margin-top: 100px; font-family: 'Noto Sans KR', sans-serif; }
footer a { color: var(--footer-text); }
footer strong { color: rgba(255,255,255,.65); font-weight: 600; }

/* RESPONSIVE */
@media (max-width: 960px) { .post-layout { grid-template-columns: 1fr; gap: 0; } .post-sidebar { display: none; } }
@media (max-width: 768px) { main { padding: 40px 20px; } .header-inner { padding: 0 20px; } .hero { padding: 48px 0 40px; } .nav-desktop { display: none; } .nav-toggle { display: flex; align-items: center; } .grid { grid-template-columns: 1fr; gap: 20px; } .related-grid { grid-template-columns: 1fr; } }
@media (max-width: 480px) { .logo { font-size: 1.05rem; } .post-title { font-size: 1.5rem; } }
@media (min-width: 1100px) { .grid { grid-template-columns: repeat(3, 1fr); } }
CSS;

        CssTheme::create(['name' => 'Modern Indigo', 'description' => '인디고 컬러의 모던 에디토리얼 스타일 (기본)', 'preview_color' => '#4f46e5', 'css' => $theme1, 'is_active' => true]);
        CssTheme::create(['name' => 'Dark Mode',    'description' => '다크 네이비 배경의 다크모드 테마', 'preview_color' => '#818cf8', 'css' => $theme2, 'is_active' => false]);
        CssTheme::create(['name' => 'Minimal Clean', 'description' => '웜화이트 배경의 미니멀 클린 테마', 'preview_color' => '#16a34a', 'css' => $theme3, 'is_active' => false]);
    }
}
