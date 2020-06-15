@extends('auth.template')

@section('content')
<form action="@url('AuthController@postLogin')@" method="POST">
    @csrf_field()
    <div>
        <label for="emailInput">@('auth.input.label.email')@</label>
        <input type="email" autocomplete="email" id="emailInput" name="email" placeholder="@('auth.input.placeholder.email')@" required />
    </div>
    <div>
        <label for="passwordInput">@('auth.input.label.password')@</label>
        <input type="password" autocomplete="current_password" id="passwordInput" name="password" placeholder="@('auth.input.placeholder.password')@" required />
    </div>
    <div>
        <label>
            <input type="checkbox" name="remember" checked />
            @('auth.input.label.remember')@
        </label>
    </div>
    <div>
        <a href="@url('AuthController@forgot')@">@('auth.link.send_link')@</a>
        <button type="submit">@('auth.button.sign_in')@</button>
    </div>
</form>
@endsection
