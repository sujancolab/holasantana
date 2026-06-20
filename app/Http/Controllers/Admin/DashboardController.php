<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'pageCount' => Page::count(),
            'publishedCount' => Page::where('status', 'published')->count(),
            'menuCount' => MenuItem::where('is_active', true)->count(),
            'recentPages' => Page::latest()->take(5)->get(),
        ]);
    }
}
