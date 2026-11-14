
@extends('layouts.app')

@section('title', 'SuperAdmin Dashboard')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">SuperAdmin Dashboard</h3>
            <p class="text-muted mb-0">
                Welcome, {{ auth()->user()->name }}
            </p>
        </div>

        <a href="{{ route('superadmin.invite') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Invite Admin
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Invited Admins Section --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Invited Admins</h5>

            <a href="{{ route('superadmin.invite') }}" class="btn btn-sm btn-outline-primary">
                Invite New Admin
            </a>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Admin Name</th>
                            <th>Email</th>
                            <th>Invited By</th>
                            <th>Invited On</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($invitations as $invitation)

                            <tr>
                                <td>
                                    {{ $invitations->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    {{ $invitation->invitedUser->name ?? $invitation->name }}
                                </td>

                                <td>
                                    {{ $invitation->invitedUser->email ?? $invitation->email }}
                                </td>

                                <td>
                                    {{ $invitation->inviter->name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $invitation->created_at->format('d M Y') }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No Admin invitations found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>

        @if($invitations->hasPages())
            <div class="card-footer bg-white">
                {{ $invitations->links() }}
            </div>
        @endif

    </div>


    {{-- Generated Short URLs Section --}}
    <div class="card shadow-sm">

        <div class="card-header bg-white">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <h5 class="mb-0">Generated Short URLs</h5>

                <div class="d-flex flex-wrap gap-2">

                    {{-- Date Filter --}}
                    
<form method="GET" action="{{ route('superadmin.dashboard') }}">

    <select name="filter"
            class="form-select form-select-sm"
            onchange="this.form.submit()">

        <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>
            All Time
        </option>

        <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>
            Today
        </option>

        <option value="last_week" {{ $filter == 'last_week' ? 'selected' : '' }}>
            Last 7 Days
        </option>

        <option value="this_month" {{ $filter == 'this_month' ? 'selected' : '' }}>
            This Month
        </option>

        <option value="last_month" {{ $filter == 'last_month' ? 'selected' : '' }}>
            Last Month
        </option>

    </select>

</form>


                    {{-- Download --}}
                    <a href="{{ route('superadmin.shorturls.download') }}"
                       class="btn btn-sm btn-success">
                        <i class="bi bi-download"></i> Download CSV
                    </a>

                    {{-- View All --}}
                    <a href="{{ route('superadmin.shorturls.index') }}"
                       class="btn btn-sm btn-primary">
                        View All
                    </a>

                </div>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>#</th>
                            <th>Short URL</th>
                            <th>Original URL</th>
                            <th>Hits</th>
                            <th>Created By</th>
                            <th>Role</th>
                            <th>Created On</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($shortUrls as $url)

                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <a href="{{ url('/' . $url->short_code) }}"
                                       target="_blank">
                                        {{ url('/' . $url->short_code) }}
                                    </a>
                                </td>

                                <td>
                                    
                                        {{ \Illuminate\Support\Str::limit($url->original_url, 40) }}
                                    
                                </td>

                                <td>{{ $url->hits }}</td>

                                <td>
                                    {{ $url->user->name ?? 'Unknown' }}
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($url->user->role ?? 'N/A') }}
                                    </span>
                                </td>

                                <td>
                                    {{ $url->created_at->format('d M Y') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No short URLs found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div class="card-footer bg-white d-flex justify-content-between align-items-center">

            <span class="text-muted">
                Showing {{ $shortUrls->count() }} of {{ $totalShortUrls }} short URLs
            </span>

            <a href="{{ route('superadmin.shorturls.index') }}"
               class="text-decoration-none">
                View All Short URLs →
            </a>

        </div>

    </div>

</div>

@endsection
