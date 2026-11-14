<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminInvitation;
use App\Models\User;
use App\Models\ShortUrl;
use Illuminate\Support\Str;


class DashboardController extends Controller
{
    //
     // Admin Dashboard
   
public function index(Request $request)
{
    // Admin ke invited users
    $invitations = AdminInvitation::with('invitedUser')
        ->where('invited_by', auth()->id())
        ->latest()
        ->paginate(5, ['*'], 'invitations_page');

    // Admin ke generated URLs
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

    return view('admin.dashboard', compact(
        'invitations',
        'shortUrls',
        'filter'
    ));
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

        case 'all':
        default:
            $filter = 'all';
            break;
    }

    $shortUrls = $query->latest()->get();

    $fileName = 'my-short-urls-' . now()->format('Y-m-d') . '.csv';

    return response()->streamDownload(function () use ($shortUrls) {
        $file = fopen('php://output', 'w');

        // Excel compatibility
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

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
                url('/' . $url->short_code),
                $url->hits,
                $url->created_at
            ]);
        }

        fclose($file);
    }, $fileName, [
        'Content-Type' => 'text/csv',
    ]);
}


    // Invite Form
    public function invite()
    {
        return view('admin.invite');
    }


// View all generated URLs
public function viewAllShortUrls()
{
    $shortUrls = ShortUrl::where('user_id', auth()->id())
        ->latest()
        ->paginate(15);

    return view('admin.short-urls', compact('shortUrls'));
}


// View all invited Admins and Members
public function viewAllInvitations()
{
    $invitations = AdminInvitation::with('invitedUser')
        ->where('invited_by', auth()->id())
        ->latest()
        ->paginate(15);

    return view('admin.invitations', compact('invitations'));
}


    // Store Invitation
    public function storeInvitation(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:admin,member'],
        ]);

        $user = User::where('name', $validated['name'])
            ->where('email', $validated['email'])
            ->where('role', $validated['role'])
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->with('error', 'User not found. Please check name, email and role.');
        }

        $alreadyInvited = AdminInvitation::where('user_id', $user->id)
            ->where('invited_by', auth()->id())
            ->exists();

        if ($alreadyInvited) {
            return back()
                ->withInput()
                ->with('error', 'You have already invited this user.');
        }

        AdminInvitation::create([
            'user_id' => $user->id,
            'invited_by' => auth()->id(),
            'name' => $user->name,
            'email' => $user->email,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'User invited successfully.');
    }
    

    public function createShortUrl()
    {
        return view('admin.short-url');
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
            ->route('admin.dashboard')
            ->with('success', 'Short URL generated successfully.');
    }
}
