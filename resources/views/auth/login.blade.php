@extends('layouts.auth')

@section('login')
<div class="login-box">

    <!-- /.login-logo -->
    <div class="login-box-body animated fadeInDown" style="background-color: #f7f9fc; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);">
        <div class="login-logo">
            <a href="{{ url('/') }}">
                <img src="{{ url($setting->path_logo) }}" alt="logo.png" width="100" class="animated bounceIn" style="margin-bottom: 20px;">
            </a>
        </div>

        <form action="{{ route('login') }}" method="post" class="form-login">
            @csrf
            <div class="form-group has-feedback @error('email') has-error @enderror">
                <input type="email" name="email" class="form-control" placeholder="Email" required value="{{ old('email') }}" autofocus style="border-radius: 5px; padding: 10px;">
                <span class="glyphicon glyphicon-envelope form-control-feedback animated swing infinite" style="color: #007bff;"></span>
                @error('email')
                    <span class="help-block animated flash" style="color: red;">Email/Password Wrong</span>
                @else
                    <span class="help-block with-errors"></span>
                @enderror
            </div>
            <div class="form-group has-feedback @error('password') has-error @enderror">
                <input type="password" name="password" class="form-control" placeholder="Password" required style="border-radius: 5px; padding: 10px;">
                <span class="glyphicon glyphicon-lock form-control-feedback animated swing infinite" style="color: #007bff;"></span>
                @error('password')
                    <span class="help-block animated flash" style="color: red;">Email/Password Wrong</span>
                @else
                    <span class="help-block with-errors"></span>
                @enderror
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" style="margin-right: 5px;"> Remember Me
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat animated pulse" style="background-color: #007bff; border: none; border-radius: 5px;">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
@endsection
