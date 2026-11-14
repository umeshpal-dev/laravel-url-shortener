
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="container-fluid">

    {{-- Dashboard Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

        <div>
            <h2 class="text-success">Admin Dashboard</h2>
            <p class="text-muted mb-0">
                Welcome, {{ auth()->user()->name }}!
            </p>
        </div>

        <a href="{{ route('admin.shorturl.create') }}" class="btn btn-primary">
            + Generate
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


    {{-- SECTION 1: Generated Short URLs --}}
    <div class="card shadow-sm mb-4">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

    <h5 class="mb-0">My Generated Short URLs</h5>

    <div class="d-flex align-items-center gap-2 flex-wrap">

        <form method="GET" action="{{ route('admin.dashboard') }}"
              class="d-flex gap-2">

            <select name="filter"
                    class="form-select form-select-sm"
                    onchange="this.form.submit()">

                <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>
                    All
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

        <a href="{{ route('admin.shorturls.download', ['filter' => $filter]) }}"
           class="btn btn-success btn-sm">
            Download CSV
        </a>

        <a href="{{ route('admin.shorturls.index') }}"
           class="btn btn-outline-primary btn-sm">
            View All
        </a>

    </div>
</div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Short URL</th>
                            <th>Original URL</th>
                            <th>Hits</th>
                            <th>Created On</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($shortUrls as $url)

                            <tr>
                                <td>{{ $shortUrls->firstItem() + $loop->index }}</td>

                                <td>
                                    <a href="{{ url('/' . $url->short_code) }}" target="_blank">
                                        {{ url('/' . $url->short_code) }}
                                    </a>
                                </td>

                                <td>
                                    
                                        {{ Str::limit($url->original_url, 50) }}
                                    
                                </td>

                                <td>{{ $url->hits }}</td>

                                <td>{{ $url->created_at->format('d M Y') }}</td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No short URLs available.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

             {{-- ADD PAGINATION HERE --}}
        @if($shortUrls->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $shortUrls->onEachSide(0)->links() }}
            </div>
        @endif

        </div>

    </div>


    {{-- SECTION 2: Invited Admins & Members --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">

            <h5 class="mb-0">Invited Admins & Members</h5>

            <div class="d-flex gap-2">

                <a href="{{ route('admin.invitations.index') }}" class="btn btn-sm btn-primary">
                    View All
                </a>

                <a href="{{ route('admin.invite') }}" class="btn btn-sm btn-success">
                    + Invite Admin / Member
                </a>

            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Invited On</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($invitations as $invitation)

                            <tr>

                                <td>{{ $invitations->firstItem() + $loop->index }}</td>

                                <td>{{ $invitation->invitedUser->name ?? $invitation->name }}</td>

                                <td>{{ $invitation->invitedUser->email ?? $invitation->email }}</td>

                                <td>
                                    @php
                                        $role = $invitation->invitedUser->role ?? null;
                                    @endphp

                                    @if($role)
                                        <span class="badge {{ $role === 'admin' ? 'bg-primary' : 'bg-success' }}">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>

                                <td>{{ $invitation->created_at->format('d M Y') }}</td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No Admins or Members invited yet.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Previous / Next Pagination --}}
            @if($invitations->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $invitations->onEachSide(0)->links() }}
                </div>
            @endif

        </div>

    </div>

</div>

@endsection
