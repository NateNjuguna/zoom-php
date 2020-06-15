@extends('auth.template')

@section('reset')
<form action="@url('Auth@reset')@"method="POST">
    @csrf_field()
    <div>
        <label for="passwordInput">@('auth.input.label.password')@</label>
        <input type="password" autocomplete="new_password" id="passwordInput" name="password" placeholder="@('auth.input.placeholder.password')@" required />
    </div>
    <div>
        <label for="passwordInput2">@('auth.input.label.password2')@</label>
        <input type="password" id="passwordInput2" name="password_confirmation" placeholder="@('auth.input.placeholder.password2')@" required />
    </div>
    <div>
        <button type="submit" class="btn btn-primary">@('auth.button.reset')@</button>
    </div>
</form>
@endsection