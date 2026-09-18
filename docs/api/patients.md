# Patient API

Semua endpoint untuk pasien memerlukan autentikasi melalui middleware `auth:sanctum`.

**Base URL**: `/api/patients`

---

## 1. Daftar dan Pencarian Pasien
Mengambil daftar pasien yang aktif (bukan soft-deleted). Mendukung pencarian dan pagination.

**Endpoint**: `GET /api/patients`

### Query Parameters
| Parameter | Tipe | Wajib | Keterangan |
| --------- | ---- | ----- | ---------- |
| `q` | string | Tidak | Kata kunci pencarian. Mencari di Nomor RM, NIK, dan Nama Pasien. |
| `per_page`| int | Tidak | Jumlah item per halaman (Default: 15, Maksimal: 100). |
| `page` | int | Tidak | Halaman yang ingin dituju (Default: 1). |

### Response Berhasil (200 OK)
```json
{
  "success": true,
  "message": "Daftar pasien berhasil diambil.",
  "data": {
    "data": [
      {
        "id": 1,
        "medical_record_number": "000001",
        "nik": "3333444455556666",
        "full_name": "Pasien Baru",
        "gender": "L",
        "birth_date": "1990-01-01",
        "birth_place": "Jakarta",
        "address": "Jalan Kebon Jeruk",
        "phone": "081234567890",
        "email": null,
        "religion": "Islam",
        "blood_type": "O",
        "marital_status": "Single",
        "ihs_id": null,
        "status": "active",
        "created_at": "2026-09-18T10:00:00.000000Z",
        "updated_at": "2026-09-18T10:00:00.000000Z"
      }
    ],
    "links": {
      "first": "...",
      "last": "...",
      "prev": null,
      "next": null
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 1,
      "path": "...",
      "per_page": 15,
      "to": 1,
      "total": 1
    }
  }
}
```

---

## 2. Tambah Pasien Baru
Menambahkan data pasien baru ke dalam sistem. Nomor Rekam Medis (RM) akan di-generate secara otomatis oleh sistem (6 digit, cth: `000001`).

**Endpoint**: `POST /api/patients`

### Aturan Data
- **Nomor RM**: Otomatis di-generate. Client **dilarang** mengirim/menentukan nilai ini.
- **NIK**: Boleh kosong (nullable). Jika diisi, nilainya harus unik (maksimal 20 karakter).

### Request Body (JSON)
```json
{
  "nik": "3333444455556666",
  "full_name": "Pasien Baru",
  "gender": "L",
  "birth_date": "1990-01-01",
  "birth_place": "Jakarta",
  "address": "Jalan Merdeka No. 1",
  "phone": "08123456789",
  "email": "pasien@example.com",
  "religion": "Islam",
  "blood_type": "O",
  "marital_status": "Single",
  "ihs_id": null
}
```

### Response Berhasil (201 Created)
```json
{
  "success": true,
  "message": "Data pasien berhasil disimpan.",
  "data": {
    "id": 2,
    "medical_record_number": "000002",
    "nik": "3333444455556666",
    "full_name": "Pasien Baru",
    ...
  }
}
```

### Response Gagal Validasi (422 Unprocessable Entity)
```json
{
  "success": false,
  "message": "Validasi data gagal.",
  "errors": {
    "nik": [
      "The nik field must be 16 digits."
    ],
    "full_name": [
      "The full name field is required."
    ]
  }
}
```

---

## 3. Detail Pasien
Mengambil data detail seorang pasien. Pasien dengan status `deleted` tidak akan ditemukan.

**Endpoint**: `GET /api/patients/{patient}`

### Response Berhasil (200 OK)
```json
{
  "success": true,
  "message": "Detail pasien berhasil diambil.",
  "data": {
    "id": 1,
    "medical_record_number": "000001",
    "nik": "3333444455556666",
    ...
  }
}
```

### Response Tidak Ditemukan (404 Not Found)
Bila pasien tidak ada atau sudah terhapus (soft-delete).
```json
{
  "message": "No query results for model [App\\Models\\Patient] 1"
}
```

---

## 4. Edit Data Pasien
Memperbarui data seorang pasien. Nomor Rekam Medis (RM) tidak dapat diubah oleh endpoint ini.

**Endpoint**: `PATCH /api/patients/{patient}`

### Request Body (JSON)
Sama dengan endpoint POST.
```json
{
  "full_name": "Pasien Edit Nama",
  "nik": "3333444455556666",
  "address": "Alamat baru"
}
```

### Response Berhasil (200 OK)
```json
{
  "success": true,
  "message": "Data pasien berhasil diperbarui.",
  "data": {
    "id": 1,
    "medical_record_number": "000001",
    "nik": "3333444455556666",
    "full_name": "Pasien Edit Nama",
    "address": "Alamat baru",
    ...
  }
}
```

---

## Catatan Tambahan
- Endpoint **Delete** (`DELETE /api/patients/{patient}`) **belum tersedia** sesuai dengan ruang lingkup saat ini.
- Bila pengguna (client) yang belum terautentikasi (guest) mencoba mengakses endpoint-endpoint di atas, akan mendapatkan respon gagal **401 Unauthorized**.
