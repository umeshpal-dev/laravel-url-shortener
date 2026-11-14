@extends('layouts.app')

@section('title', 'Generate Short URL')

@section('content')

<div class="container">

    <div class="card shadow-sm">

        <div class="card-header bg-white">
            <h5 class="mb-0">Generate Short URL</h5>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.shorturl.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Enter Original URL</label>

                    <input
                        type="url"
                        name="original_url"
                        class="form-control"
                        placeholder="https://example.com/your-long-url"
                        value="{{ old('original_url') }}"
                        required
                    >

                    @error('original_url')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    Generate Short URL
                </button>

                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    Back
                </a>

            </form>

        </div>

    </div>

</div>

@endsection