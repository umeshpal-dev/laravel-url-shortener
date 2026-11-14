
@extends('layouts.app')

@section('title', 'Member Dashboard')

@section('content')

<div class="container-fluid">

    {{-- Dashboard Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h2 class="text-success mb-1">Member Dashboard</h2>
            <p class="text-muted mb-0">
                Welcome, {{ auth()->user()->name }}!
            </p>
        </div>

        <a href="{{ route('member.shorturl.create') }}" class="btn btn-primary">
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

    {{-- Generated Short URLs --}}
    <div class="card shadow-sm border-0">

        {{-- Card Header --}}
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <h5 class="mb-0">My Generated Short URLs</h5>

                <div class="d-flex align-items-center flex-wrap gap-2">

                    {{-- Date Filter --}}
                    <form method="GET" action="{{ route('member.dashboard') }}">
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

                    {{-- Download CSV --}}
                    <a href="{{ route('member.shorturls.download', ['filter' => $filter]) }}"
                       class="btn btn-success btn-sm">
                        <i class="bi bi-download"></i> Download CSV
                    </a>

                    

                </div>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Short URL</th>
                            <th>Original URL</th>
                            <th width="100">Hits</th>
                            <th width="150">Created On</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($shortUrls as $url)

                            <tr>
                                <td>
                                    {{ $shortUrls->firstItem() + $loop->index }}
                                </td>

                                <td>
                                   

                                    <a href="{{ route('shorturl.redirect', ['short_code' => $url->short_code]) }}"
                                        target="_blank"
                                        class="text-primary text-decoration-none">
                                            {{ route('shorturl.redirect', ['short_code' => $url->short_code]) }}
                                        </a>
                                </td>

                                <td>
                                    <!-- <a href="{{ $url->original_url }}"
                                       target="_blank"
                                       class="text-dark text-decoration-none"> -->
                                        {{ Str::limit($url->original_url, 50) }}
                                    <!-- </a> -->
                                </td>

                                <td>{{ $url->hits }}</td>

                                <td>
                                    {{ $url->created_at->format('d M Y') }}
                                </td>
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

            {{-- Pagination --}}
            @if($shortUrls->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $shortUrls->onEachSide(0)->links() }}
                </div>
            @endif

        </div>
    </div>

</div>

@endsection
