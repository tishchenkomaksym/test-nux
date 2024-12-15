<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('register') }}" method="POST">
    @csrf
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="phonenumber">Phonenumber:</label>
    <input type="text" id="phonenumber" name="phonenumber" required><br>

    <button type="submit">Register</button>
</form>
</body>
</html>
