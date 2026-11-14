
@extends('layouts.app')

@section('title', 'All Invited Admins & Members')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>All Invited Admins & Members</h3>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            Back to Dashboard
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">

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
                                <td colspan="5" class="text-center py-4">
                                    No invitations found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        @if($invitations->hasPages())
            <div class="card-footer">
                {{ $invitations->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
