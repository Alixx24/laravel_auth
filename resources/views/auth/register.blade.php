<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .error { color: red; }
        form { max-width: 400px; }
        label { display: block; margin-top: 1rem; }
        input { width: 100%; padding: 0.5rem; margin-top: 0.25rem; }
        button { margin-top: 1.5rem; padding: 0.75rem; width: 100%; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .success { background-color: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
    </style>
</head>
<body>

    <h1>Register</h1>

    {{-- Show success message from session --}}
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    {{-- Show validation errors --}}
    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}">
        @csrf

        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />

        <label for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required />

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="#">Login here</a></p>

</body>
</html>
