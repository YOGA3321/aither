@extends('layouts.app')

@section('page_heading', 'Manajemen Perangkat IoT')

@section('content')
<div class="row">
    <!-- Form Tambah Perangkat -->
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tambah Perangkat Baru</h6>
            </div>
            <div class="card-body">
                <p class="mb-4">Masukkan API Key dan Secret Key yang sudah terpasang (hardcode) pada perangkat ESP32 Anda.</p>
                
                <form action="{{ route('devices.store') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Nama Perangkat</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Sensor Kebun" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>API Key</label>
                            <input type="text" name="api_key" class="form-control" placeholder="Masukkan API Key" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Secret Key</label>
                            <input type="text" name="secret_key" class="form-control" placeholder="Masukkan Secret Key" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-icon-split">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Perangkat</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Perangkat -->
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Perangkat Saya</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>API Key</th>
                                <th>Tanggal Ditambahkan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devices as $device)
                                <tr>
                                    <td>{{ $device->name }}</td>
                                    <td><code>{{ $device->api_key }}</code></td>
                                    <td>{{ $device->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <form action="{{ route('devices.destroy', $device->id) }}" method="POST" onsubmit="return confirm('Hapus perangkat ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm btn-icon-split">
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                                <span class="text">Hapus</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada perangkat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
