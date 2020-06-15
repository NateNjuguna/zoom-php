@extends('auth.template')

@section('content')
<form action="@url('Auth@forgot')@" method="POST">
    @csrf_field()
    <div>
        <label for="emailInput">@('auth.input.label.email')@</label>
        <input type="email" autocomplete="email" id="emailInput" name="email" placeholder="@('auth.input.placeholder.email')@" required />
            </div>
        </div>
    </div>
    <div>
        <button type="submit">@('auth.button.send_link')@</button>
    </div>
</form>
<p>
    @('auth.prompt.remembered', [
    ])@
    <a href="javascript:void(0);" class="auth-switch text-primary" show=".login">@('auth.link.sign_in')@</a>
</p>
@endsection
