<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function show(): View
    {
        $isEnabled = Setting::get('about_enabled', '0') === '1';
        if (!$isEnabled) {
            abort(404);
        }

        $aboutTitle = trim((string) Setting::get('about_title', 'About'));
        $aboutHtml = (string) Setting::get('about_html', '');

        return view('about.show', [
            'aboutTitle' => $aboutTitle !== '' ? $aboutTitle : 'About',
            'aboutHtml' => $aboutHtml,
        ]);
    }

    public function edit(): View
    {
        return view('admin.about.edit', [
            'aboutEnabled' => Setting::get('about_enabled', '0') === '1',
            'aboutTitle' => (string) Setting::get('about_title', 'About'),
            'aboutHtml' => (string) Setting::get('about_html', ''),
            'aboutCardHtml' => (string) Setting::get('about_card_html', ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'about_enabled' => 'nullable|boolean',
            'about_title' => 'nullable|string|max:120',
            'about_html' => 'nullable|string',
            'about_card_html' => 'nullable|string',
        ]);

        $enabled = isset($data['about_enabled']) && (string) $data['about_enabled'] === '1';
        $title = trim((string) ($data['about_title'] ?? ''));
        $html = (string) ($data['about_html'] ?? '');
        $cardHtml = (string) ($data['about_card_html'] ?? '');

        Setting::set('about_enabled', $enabled ? '1' : '0');
        Setting::set('about_title', $title !== '' ? $title : 'About');
        Setting::set('about_html', $html);
        Setting::set('about_card_html', $cardHtml);

        return redirect()->route('admin.about.edit')->with('success', 'About 페이지 설정이 저장되었습니다.');
    }
}
