
@extends('layouts.app')

@section('title', 'All My Short URLs')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>All My Generated Short URLs</h3>

        <div class="d-flex gap-2">
        <a href="{{ route('admin.shorturls.download', ['filter' => 'all']) }}"
           class="btn btn-success">
            Download CSV
        </a>

        <a href="{{ route('admin.dashboard') }}"
           class="btn btn-secondary">
            Back to Dashboard
        </a>
    </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">

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
                                    <a href="{{ $url->original_url }}" target="_blank">
                                        {{ Str::limit($url->original_url, 50) }}
                                    </a>
                                </td>

                                <td>{{ $url->hits }}</td>
                                <td>{{ $url->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    No short URLs available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        @if($shortUrls->hasPages())
            <div class="card-footer">
                {{ $shortUrls->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
