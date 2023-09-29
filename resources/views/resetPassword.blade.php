
@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <form action="" method="POST">
        @csrf
        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}" />
        <input type="password" name="password" id="password" placeholder="New Password" />
        <br /> <br />
        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="New Confirm Password" />
        <br /> <br />
        <input type="submit">
    </form>
</body>
</html>