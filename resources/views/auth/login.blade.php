@extends('layouts.auth')

@section('login')
<div class="login-box" style="margin-top: 50px; position: relative; z-index: 1;">
    <!-- Latar Belakang -->
    <div class="background-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-image: url('{{ url('path/to/your/background.jpg') }}'); background-size: cover; background-position: center; filter: blur(8px); z-index: -1;"></div>
    
    <!-- Login Box Body -->
    <div class="login-box-body animated fadeInDown" style="background-color: rgba(255, 255, 255, 0.85); padding: 40px; border-radius: 10px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3); backdrop-filter: blur(10px);">
        
        <!-- Logo -->
        <div class="login-logo">
            <a href="{{ url('/') }}">
                <img src="{{ url($setting->path_logo) }}" alt="logo.png" width="120" class="animated zoomIn" style="margin-bottom: 25px;">
            </a>
        </div>

        <!-- Form Login -->
        <form action="{{ route('login') }}" method="post" class="form-login">
            @csrf
            
            <!-- Email Field -->
            <div class="form-group has-feedback @error('email') has-error @enderror">
                <input type="email" name="email" class="form-control" placeholder="Email" required value="{{ old('email') }}" autofocus style="border-radius: 30px; padding: 15px; border: 1px solid #ced4da;">
                <span class="glyphicon glyphicon-envelope form-control-feedback animated bounceIn" style="color: #007bff;"></span>
                @error('email')
                    <span class="help-block animated shake" style="color: red;">Email/Password Wrong</span>
                @else
                    <span class="help-block with-errors"></span>
                @enderror
            </div>
            
            <!-- Password Field -->
            <div class="form-group has-feedback @error('password') has-error @enderror">
                <input type="password" name="password" class="form-control" placeholder="Password" required style="border-radius: 30px; padding: 15px; border: 1px solid #ced4da;">
                <span class="glyphicon glyphicon-lock form-control-feedback animated bounceIn" style="color: #007bff;"></span>
                @error('password')
                    <span class="help-block animated shake" style="color: red;">Email/Password Wrong</span>
                @else
                    <span class="help-block with-errors"></span>
                @enderror
            </div>
            
            <!-- Remember Me and Submit Button -->
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" style="margin-right: 5px;"> Remember Me
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat animated pulse" style="background-color: #007bff; border: none; border-radius: 30px; padding: 12px;">Sign In</button>
                </div>
            </div>
        </form>
        
    </div>
</div>

<!-- Footer -->
<footer style="text-align: center; padding: 20px; position: relative; bottom: 0; width: 100%; color: gray;">
    <p>&copy; {{ date('Y') }} <a href="/">{{ $setting->nama_perusahaan }}</a>. All Rights Reserved.</p>
</footer>
@endsection
