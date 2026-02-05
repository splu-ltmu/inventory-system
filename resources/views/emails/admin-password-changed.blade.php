<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Password Changed</title>
</head>
<body>
  <p>Hello {{ $user->name }},</p>
  <p>An administrator has changed your account password. If you did not request or expect this change, please contact support immediately.</p>
  <p>For security, the new password is not included in this email.</p>
  <p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
