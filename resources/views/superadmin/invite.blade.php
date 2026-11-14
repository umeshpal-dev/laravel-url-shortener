
@extends('layouts.app')

@section('title', 'Invite Admin')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Invite Admin</h4>
        </div>

        <div class="card-body">

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('superadmin.invite.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Enter Admin name"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        placeholder="Enter Admin email"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Invite Admin
                </button>

                <a href="{{ route('superadmin.dashboard') }}"
                   class="btn btn-secondary">
                    Back
                </a>
            </form>

        </div>
    </div>
</div>

@endsection
