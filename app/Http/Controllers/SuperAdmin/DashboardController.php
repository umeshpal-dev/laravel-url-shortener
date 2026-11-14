<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminInvitation;
use App\Models\ShortUrl;

class DashboardController extends Controller
{
    // SuperAdmin Dashboard
    
public function index(Request $request)
{
    // Show only Admins invited by the logged-in SuperAdmin
    $invitations = AdminInvitation::with(['invitedUser', 'inviter'])
        ->where('invited_by', auth()->id())
        ->latest()
        ->paginate(10, ['*'], 'invitations_page');

    // Get selected date filter
    $filter = $request->query('filter', 'all');

    // Only allow valid filter values
    if (!in_array($filter, [
        'all',
        'today',
        'last_week',
        'this_month',
        'last_month'
    ])) {
        $filter = 'all';
    }

    // Generated URLs query
    $shortUrlsQuery = ShortUrl::with('user');

    // Apply date filter
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
    }

    // Count matching URLs
    $totalShortUrls = (clone $shortUrlsQuery)->count();

    // Show latest 5 matching URLs
    $shortUrls = $shortUrlsQuery
        ->latest()
        ->take(5)
        ->get();

    return view('superadmin.dashboard', compact(
        'invitations',
        'shortUrls',
        'totalShortUrls',
        'filter'
    ));
}


    // Show invitation form
    public function invite()
    {
        return view('superadmin.invite');
    }

    // Invite existing Admin
    public function storeInvitation(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $admin = User::where('email', $validated['email'])
            ->where('name', $validated['name'])
            ->where('role', 'admin')
            ->first();

        if (!$admin) {
            return back()
                ->withInput()
                ->with('error', 'Admin not found. Please enter existing Admin name and email.');
        }

        $alreadyInvited = AdminInvitation::where('user_id', $admin->id)
            ->where('invited_by', auth()->id())
            ->exists();

        if ($alreadyInvited) {
            return back()
                ->withInput()
                ->with('error', 'You have already invited this Admin.');
        }

        AdminInvitation::create([
            'user_id' => $admin->id,
            'invited_by' => auth()->id(),
            'name' => $admin->name,
            'email' => $admin->email,
        ]);

        return redirect()
            ->route('superadmin.dashboard')
            ->with('success', 'Admin invited successfully.');
    }

    // View all generated short URLs
    public function viewAllShortUrls()
    {
        $shortUrls = ShortUrl::with('user')
            ->latest()
            ->paginate(15);

        return view('superadmin.short-urls', compact('shortUrls'));
    }

    // Download all generated short URLs as CSV
    public function downloadShortUrls()
    {
        $fileName = 'all-short-urls-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Short URL',
                'Original URL',
                'Hits',
                'Created By',
                'Role',
                'Created On'
            ]);

            ShortUrl::with('user')
                ->orderBy('id')
                ->chunk(500, function ($urls) use ($file) {

                    foreach ($urls as $url) {
                        fputcsv($file, [
                            $url->id,
                            url('/' . $url->short_code),
                            $url->original_url,
                            $url->hits,
                            $url->user->name ?? 'Unknown',
                            $url->user->role ?? 'N/A',
                            $url->created_at?->format('d M Y H:i'),
                        ]);
                    }
                });

            fclose($file);

        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
