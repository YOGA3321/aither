@extends('layouts.app')

@section('page_heading', 'Manajemen Profil')

@section('content')
<div class="row">
    <!-- Update Data Diri -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label>Username (Tidak dapat diubah)</label>
                        <input type="text" value="{{ $user->username }}" class="form-control" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input type="text" name="NoTlp" value="{{ $user->NoTlp }}" class="form-control">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-icon-split">
                        <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                        <span class="text">Simpan Perubahan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Ganti Password -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ganti Password</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label>Password Lama</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-danger btn-icon-split">
                        <span class="icon text-white-50"><i class="fas fa-key"></i></span>
                        <span class="text">Ganti Password</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
@push('scripts')
<script>
    Swal.fire({
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        icon: 'success',
        confirmButtonText: 'OK'
    });
</script>
@endpush
@endif

@if($errors->any())
@push('scripts')
<script>
    Swal.fire({
        title: 'Gagal!',
        text: '{{ $errors->first() }}',
        icon: 'error',
        confirmButtonText: 'OK'
    });
</script>
@endpush
@endif
@endsection
