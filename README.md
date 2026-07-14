# IoT Aither Dashboard

Dashboard Web untuk monitoring sensor lingkungan (CO2, O2, PM2.5) menggunakan **Laravel 11**, **MQTT**, **Chart.js**, dan **Alpine.js**. Aplikasi ini dirancang agar dapat diintegrasikan dengan perangkat IoT berbasis ESP32 secara realtime dan dapat dengan mudah dihosting di *Shared Hosting*.

## 🚀 Fitur Utama
- **Realtime Dashboard**: Menerima data sensor dari protokol MQTT dan langsung merender grafiknya secara langsung.
- **Sensor yang didukung**: Carbon Dioxide (CO2), Oxygen (O2), Particulate Matter (PM2.5).
- **Sistem Autentikasi**: Login dan Register menggunakan perlindungan bawaan Laravel.
- **Manajemen Perangkat IoT**: Simpan kredensial perangkat (API Key & Secret Key) milik Anda di dalam database.
- **UI Responsif**: Mengadopsi tata letak ala Bootstrap 4 SB Admin 2.

---

## 🛠️ Persyaratan Sistem
Untuk menjalankan aplikasi ini di server atau komputer lokal:
- PHP 8.2 atau lebih baru
- Composer
- Database MySQL atau MariaDB
- Node.js & NPM (Hanya jika Anda ingin mem-build ulang asetnya)

---

## ⚙️ Cara Instalasi (Lokal / Server)

1. **Clone repositori ini** (atau unggah ZIP ke Shared Hosting Anda):
   ```bash
   git clone git@github.com:YOGA3321/aither.git
   cd aither
   ```

2. **Install dependensi PHP**:
   ```bash
   composer install
   ```
   *(Jika Anda di Shared Hosting dan tidak punya akses terminal, Anda bisa upload folder `vendor` dari komputer lokal Anda, meskipun clone Github lebih disarankan).*

3. **Konfigurasi Environment**:
   Salin file konfigurasi lalu buat yang baru:
   ```bash
   cp .env.example .env
   ```
   Sesuaikan bagian database pada file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=username_database_anda
   DB_PASSWORD=password_database_anda
   ```

4. **Generate App Key & Migrate Database**:
   ```bash
   php artisan key:generate
   php artisan migrate:fresh
   ```

5. **(Opsional) Build Aset Frontend**:
   *Catatan: Kami telah menghapus pengecualian folder `public/build` dari `.gitignore`, sehingga jika Anda menarik kode dari Github ke Shared Hosting, aset (JS/CSS) yang sudah dibuild akan ikut tertarik. Anda tidak perlu repot-repot menggunakan NPM di hosting!*
   Namun jika Anda mengembangkan kode di komputer lokal, jalankan:
   ```bash
   npm install
   npm run build
   ```

---

## 📡 Konfigurasi MQTT Broker

Secara default, dashboard ini dikonfigurasi untuk terhubung ke broker publik `broker.emqx.io` dengan koneksi **WebSockets (ws)**. 
Untuk penggunaan produksi, **sangat disarankan** untuk memiliki broker MQTT sendiri.

1. Buka file `resources/views/dashboard.blade.php`.
2. Cari kode di baris bawah bagian Alpine.js:
   ```javascript
   const brokerUrl = 'ws://broker.emqx.io:8083/mqtt';
   const topic = 'aither/sensor/data';
   ```
3. Ubah `brokerUrl` ke IP server broker milik Anda (Pastikan menggunakan port WebSockets broker Anda, biasanya 8083, bukan 1883).
4. Ubah `topic` sesuai keinginan.

---

## 🔌 Integrasi Perangkat Keras (ESP32)

Saat membuat kode `.ino` untuk ESP32 Arduino IDE, Anda harus memastikan bahwa perangkat keras Anda:
1. Terhubung ke WiFi.
2. Terhubung ke broker MQTT yang sama (misal `broker.emqx.io` pada port **1883** untuk koneksi TCP ESP32).
3. Melakukan *publish* pesan JSON ke topik MQTT yang sama (misal `aither/sensor/data`).

### Format Payload JSON yang Diharapkan
ESP32 Anda harus mem-publish string JSON dengan struktur berikut:
```json
{
  "co2": 450.5,
  "o2": 20.9,
  "pm25": 12.4
}
```

### Kredensial Perangkat (Hardcode)
Di dalam aplikasi ini terdapat menu **Manajemen Perangkat IoT**.
Sesuai rancangan aplikasi, fitur tersebut digunakan hanya untuk *mencatat* nama, API Key, dan Secret Key perangkat yang Anda miliki. 

Anda (sebagai admin) **harus menghardcode** API Key dan Secret Key yang sama ke dalam *source code* (kodingan `.ino`) ESP32 Anda sebelum *flashing*. Aplikasi web ini tidak akan secara remote mengubah konfigurasi rahasia tersebut ke dalam mikrokontroler demi alasan keamanan.

---

## 🔒 Manajemen Login & Pengguna

- **Registrasi Baru**: Anda dapat membuat akun baru melalui halaman pendaftaran (`/register`).
- **Ganti Profil**: Setelah login, pengguna dapat mengubah Nama Lengkap dan Nomor Telepon di halaman Profil (`/profile`). *Username tidak dapat diubah*.

---

*Dikembangkan oleh Arcalis Team & YOGA3321 (2026).*
