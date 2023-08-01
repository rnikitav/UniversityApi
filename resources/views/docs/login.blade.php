@extends('layouts.default')

@section('content')
<div class="mt-5 d-flex align-items-center flex-column">
    <h2>Войти в систему</h2>

    @error('failure')
        <h5 class="text-red">{{ $message }}</h5>
    @enderror

    <form action="{{ route('docs.login.post') }}" method="post" class="d-flex flex-column col-6">
        @csrf
        <div class="mb-3">
            <label for="login" class="form-label">Email</label>
            <input type="email" @class([
                    'form-control',
                    'is-invalid' => $errors->has('login')
                   ])
                   id="login"
                   name="login"
                   placeholder="name@example.com"
                   value="{{ old('login') }}"
            >
            @error('login')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password"
                   class="form-control @error('login') is-invalid @enderror"
                   id="password"
                   name="password"
                   placeholder="*****"
                   value="{{ old('password') }}"
            >
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="d-flex justify-content-center mb-3">
            <button type="submit" class="btn btn-primary">Войти</button>
        </div>
    </form>
</div>
@endsection
