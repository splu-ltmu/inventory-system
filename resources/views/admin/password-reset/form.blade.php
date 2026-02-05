@extends('layouts.admin')

@section('content')
<div class="container">
  <h2>Reset password for {{ $email }}</h2>

  <form method="POST" action="{{ route('password-reset.admin.reset') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3">
      <label for="password">New Password</label>
      <input id="password" type="password" name="password" required class="form-control">
    </div>

    <div class="mb-3">
      <label for="password_confirmation">Confirm Password</label>
      <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
    </div>

    <button class="btn btn-primary">Set Password</button>
  </form>
</div>
@endsection
