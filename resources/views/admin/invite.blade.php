
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite User</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            max-width: 550px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        h2 {
            margin-top: 0;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        button, .back-btn {
            display: inline-block;
            padding: 11px 18px;
            border: 0;
            border-radius: 5px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 15px;
        }

        .back-btn {
            background: #6b7280;
            margin-left: 8px;
        }

        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 5px;
        }

        .alert {
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 5px;
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Invite Admin / Member</h2>

    @if(session('error'))
        <div class="alert">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.invite.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Enter existing user's name"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter existing user's email"
                required
            >
        </div>

        <div class="form-group">
            <label for="role">Select Role</label>
            <select id="role" name="role" required>
                <option value="">Select Role</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                    Admin
                </option>
                <option value="member" {{ old('role') == 'member' ? 'selected' : '' }}>
                    Member
                </option>
            </select>
        </div>

        <button type="submit">Send Invitation</button>

        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            Back
        </a>

    </form>

</div>

</body>
</html>
