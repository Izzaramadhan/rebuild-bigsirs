# Autentikasi API

Dokumen ini menjelaskan arsitektur dan endpoint autentikasi pada backend aplikasi BigSIRS.

## Arsitektur

Aplikasi frontend (Vue) dan backend (Laravel) diimplementasikan sebagai first-party Single Page Application (SPA).
Autentikasi menggunakan **Laravel Sanctum** dengan metode **Session/Cookie**.

* **Tidak ada bearer token** yang diterbitkan saat login.
* Keamanan mengandalkan proteksi HTTP-only cookie dan proteksi CSRF (Cross-Site Request Forgery) bawaan Laravel.
* Middleware yang memproteksi endpoint autentikasi adalah `auth:sanctum`.
* Frontend (Vue) diwajibkan untuk mengaktifkan fitur `withCredentials` pada HTTP Client (contoh: Axios) untuk mengirim dan menerima cookie `XSRF-TOKEN` dan `laravel_session`.

## Konfigurasi Local Development

Konfigurasi berikut diterapkan pada backend untuk memastikan SPA Authentication berjalan dengan baik pada lingkungan pengembangan:

```dotenv
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DOMAIN=
```

Konfigurasi **CORS** dikustomisasi (tidak menggunakan `*` untuk origin karena stateful/credentials aktif):
* `allowed_origins`: membaca nilai dari env `FRONTEND_URL`.
* `supports_credentials`: `true`

## Alur Autentikasi (SPA Authentication)

### 1. Inisialisasi CSRF Protection

Sebelum melakukan request login atau metode non-read, frontend harus mengambil cookie CSRF.

* **Endpoint:** `GET /sanctum/csrf-cookie`
* **Response:** `204 No Content`
* **Hasil:** Browser akan menyimpan HTTP cookie `XSRF-TOKEN`. Cookie ini perlu disertakan pada header `X-XSRF-TOKEN` untuk seluruh request `POST`, `PUT`, `DELETE`. Axios akan melakukan hal ini secara otomatis.

### 2. Login

Endpoint untuk memvalidasi kredensial pengguna dan mengawali sesi.

* **URL:** `/login`
* **Method:** `POST`
* **Middleware:** `guest`
* **Request Body:**
  ```json
  {
      "email": "user@example.com",
      "password": "password"
  }
  ```
* **Response Berhasil (200 OK):**
  ```json
  {
      "message": "Authenticated."
  }
  ```
  _Tidak ada bearer token yang dikembalikan._
* **Response Gagal:**
  * `422 Unprocessable Entity`: Kredensial tidak valid (email atau password salah), atau rate limiting aktif (terlalu banyak percobaan login yang gagal).
* **Keamanan:** Memiliki fitur rate-limiting bawaan (maksimal 5 percobaan, throttle 1 menit). Session ID diregenerasi setelah berhasil login untuk menghindari Session Fixation.

**Contoh Response Validasi (422):**
```json
{
    "message": "These credentials do not match our records.",
    "errors": {
        "email": [
            "These credentials do not match our records."
        ]
    }
}
```
Atau jika rate limiter aktif:
```json
{
    "message": "Too many login attempts. Please try again in 58 seconds.",
    "errors": {
        "email": [
            "Too many login attempts. Please try again in 58 seconds."
        ]
    }
}
```

### 3. Mengambil Data Current User

* **URL:** `/api/user`
* **Method:** `GET`
* **Middleware:** `auth:sanctum`
* **Response Berhasil (200 OK):**
  Mengembalikan representasi JSON dari model `User` yang sedang login.
  _Password dan `remember_token` tidak diekspos/hidden._
* **Response Gagal:**
  * `401 Unauthorized`: Jika request tidak menyertakan session cookie yang valid.
  * `419 Page Expired`: Jika request gagal validasi CSRF (token kadaluarsa atau tidak valid).

### 4. Logout

* **URL:** `/logout`
* **Method:** `POST`
* **Middleware:** `auth:sanctum`
* **Response Berhasil (200 OK):**
  ```json
  {
      "message": "Logged out."
  }
  ```
  Sesi di-invalidate dan CSRF token diregenerasi.
* **Response Gagal:**
  * `401 Unauthorized`: Jika pengguna belum login atau session tidak valid.

## Catatan Penempatan Route
* `POST /login` dan `POST /logout` ditempatkan di `routes/web.php` untuk memfasilitasi penggunaan native Session routing dan proteksi CSRF bawaan guard `web`.
* `GET /api/user` ditempatkan di `routes/api.php` karena spesifik untuk konsumsi data berformat API berbasis JSON. Middleware SPA stateful (`$middleware->statefulApi()`) diaktifkan untuk menerjemahkan cookies session pada prefix `/api`.
