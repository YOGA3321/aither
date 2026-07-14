<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign in & Sign up - IoT Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/stylee.css') }}" />
    <style>
        .input-field { overflow: hidden; }
        .input-field input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px #f0f0f0 inset !important;
        }
    </style>
    @vite(['resources/js/app.js'])
</head>
<body>
    <div class="container {{ (!empty($register) || session('register') || $errors->has('nama') || $errors->has('password')) && !$errors->has('username') ? 'sign-up-mode' : '' }}">
        <div class="forms-container">
            <div class="signin-signup">
                <form action="{{ route('login') }}" method="POST" class="sign-in-form">
                    @csrf
                    <h2 class="title">Sign in</h2>
                    
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="Username" name="username" required/>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input id="input_password" type="password" placeholder="Password" name="password" required/>
                    </div>
                    
                    <!-- Fitur show password -->
                    <div style="width: 380px; text-align: left; margin-top: 5px;">
                        <label for="show_password" style="font-size: 0.9rem; color: #666; cursor: pointer;">
                            <input id="show_password" type="checkbox" style="margin-right: 5px;" />
                            Tampilkan Password
                        </label>
                    </div>

                    <input type="submit" value="Login" class="btn solid" />
                </form>

                <form action="{{ route('register') }}" method="POST" class="sign-up-form">
                    @csrf
                    <h2 class="title">Sign up</h2>
                    
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="Nama" name="nama" required/>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="Username" name="username" required/>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input id="input_password2" type="password" placeholder="Password" name="password" required/>
                    </div>
                    
                    <div style="width: 380px; text-align: left; margin-top: 5px;">
                        <label for="show_password2" style="font-size: 0.9rem; color: #666; cursor: pointer;">
                            <input id="show_password2" type="checkbox" style="margin-right: 5px;" />
                            Tampilkan Password
                        </label>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-phone"></i>
                        <input type="text" inputmode="numeric" placeholder="Phone" name="NoTlp"/>
                    </div>
                    <input type="submit" class="btn" value="Sign up" />
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>New here ?</h3>
                    <p>Buat Akun Anda Agar Anda Dapat Memasuki Dashboard IoT.</p>
                    <button class="btn transparent" id="sign-up-btn">Sign up</button>
                </div>
                <img src="{{ asset('image/login.svg') }}" class="image" alt="" />
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>Already have one ?</h3>
                    <p>Silahkan Login Jika Anda Sudah Memiliki Akun Anda.</p>
                    <button class="btn transparent" id="sign-in-btn">Sign in</button>
                </div>
                <img src="{{ asset('image/registerr.svg') }}" class="image" alt="" />
            </div>
        </div>
    </div>

    <!-- Script toggle login/register -->
    <script src="{{ asset('js/login.js') }}"></script>
    
    <!-- Script show password -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputPassword = document.getElementById("input_password");
            const showPassword = document.getElementById("show_password");
            const inputPassword2 = document.getElementById("input_password2");
            const showPassword2 = document.getElementById("show_password2");
            
            if(showPassword) {
                showPassword.addEventListener("input", (e) => {
                    if(e.target.checked){
                        inputPassword.setAttribute("type", "text");
                    }else{
                        inputPassword.setAttribute("type", "password");
                    };
                });
            }
            
            if(showPassword2) {
                showPassword2.addEventListener("input", (e) => {
                    if(e.target.checked){
                        inputPassword2.setAttribute("type", "text");
                    }else{
                        inputPassword2.setAttribute("type", "password");
                    };
                });
            }

            // SweetAlert2 logic
            @if ($errors->any())
                window.Swal.fire({
                    title: 'Gagal!',
                    text: '{{ $errors->first() }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('success'))
                window.Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
</body>
</html>
