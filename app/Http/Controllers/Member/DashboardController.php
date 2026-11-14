<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShortUrl;
use Illuminate\Support\Str;


class DashboardController extends Controller
{
    //




    // Member Dashboard
    // public function index()
    // {
    //     $shortUrls = ShortUrl::where('user_id', auth()->id())
    //         ->latest()
    //         ->paginate(10, ['*'], 'urls_page');

    //     return view('member.dashboard', compact('shortUrls'));
    // }

    public function index(Request $request)
{
    $filter = $request->query('filter', 'all');

    $shortUrlsQuery = ShortUrl::where('user_id', auth()->id());

    switch ($filter) {
        case 'today':
            $shortUrlsQuery->whereDate('created_at', today());
            break;

        case 'last_week':
            $shortUrlsQuery->whereBetween('created_at', [
                now()->subDays(6)->startOfDay(),
                now()->endOfDay()
            ]);
            break;

        case 'this_month':
            $shortUrlsQuery->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
            break;

        case 'last_month':
            $shortUrlsQuery->whereBetween('created_at', [
                now()->subMonthNoOverflow()->startOfMonth(),
                now()->subMonthNoOverflow()->endOfMonth()
            ]);
            break;

        case 'all':
        default:
            $filter = 'all';
            break;
    }

    $shortUrls = $shortUrlsQuery
        ->latest()
        ->paginate(5, ['*'], 'urls_page')
        ->appends(['filter' => $filter]);

    return view('member.dashboard', compact('shortUrls', 'filter'));
}


public function downloadShortUrls(Request $request)
{
    $filter = $request->query('filter', 'all');

    $query = ShortUrl::where('user_id', auth()->id());

    switch ($filter) {
        case 'today':
            $query->whereDate('created_at', today());
            break;

        case 'last_week':
            $query->whereBetween('created_at', [
                now()->subDays(6)->startOfDay(),
                now()->endOfDay()
            ]);
            break;

        case 'this_month':
            $query->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
            break;

        case 'last_month':
            $query->whereBetween('created_at', [
                now()->subMonthNoOverflow()->startOfMonth(),
                now()->subMonthNoOverflow()->endOfMonth()
            ]);
            break;
    }

    $shortUrls = $query->latest()->get();

    $fileName = 'member-short-urls-' . now()->format('Y-m-d') . '.csv';

    return response()->streamDownload(function () use ($shortUrls) {

        $file = fopen('php://output', 'w');

        fputcsv($file, [
            'ID',
            'Original URL',
            'Short Code',
            'Short URL',
            'Hits',
            'Created At'
        ]);

        foreach ($shortUrls as $url) {
            fputcsv($file, [
                $url->id,
                $url->original_url,
                $url->short_code,
                //route('shorturl.redirect', $url->short_code),
                url('/' . $url->short_code),
                $url->hits,
                $url->created_at
            ]);
        }

        fclose($file);

    }, $fileName, [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        'Cache-Control' => 'no-store, no-cache',
    ]);
}

    // Generate Short URL
    public function generateShortUrl(Request $request)
    {
        $validated = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
        ]);

        do {
            $shortCode = Str::random(6);
        } while (ShortUrl::where('short_code', $shortCode)->exists());

        ShortUrl::create([
            'user_id' => auth()->id(),
            'original_url' => $validated['original_url'],
            'short_code' => $shortCode,
        ]);

        return redirect()
            ->route('member.dashboard')
            ->with('success', 'Short URL generated successfully.')
            ->with('short_url', url('/' . $shortCode));
    }
}
