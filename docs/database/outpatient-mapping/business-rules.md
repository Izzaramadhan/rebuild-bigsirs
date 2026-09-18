# Business Rules (Outpatient)

## Aturan Bisnis Utama (Approved by Supervisor 2026-09-17)
1. **Pasien Baru vs Lama**: Pencarian pasien dilakukan berdasarkan NIK (`nik`) atau Nomor Rekam Medis (`medical_record_number`). Jika tidak ditemukan, entri baru dibuat pada tabel `patients`.
2. **Nomor Rekam Medis**: Dibuat ketika pasien baru disimpan. Format pasien baru adalah enam digit berurutan (`000001`, `000002`, dan seterusnya). Nomor RM dari sistem lama dipertahankan apa adanya saat migrasi.
3. **Pendaftaran (`outpatient_registrations`)**: Mewakili pencatatan administratif awal (booking/kedatangan), tanpa nomor registrasi bisnis terpisah.
4. **Admisi (`outpatient_admissions`)**: Menandai kunjungan pasien ke poliklinik tertentu. Satu registration dapat memiliki lebih dari satu admission.
5. **Penjamin (`guarantors`)**: Melekat pada outpatient admission sebagai snapshot penjamin transaksi.
6. **Nomor Kunjungan**: Disimpan pada outpatient admission sebagai `admission_no` dengan format yang direkomendasikan `RJ-YYYYMMDD-NNNN`. Tidak ada nomor registrasi bisnis. Sequence dimulai kembali pada tanggal berikutnya, sedangkan tanggal membuat nilainya unik secara global.
7. **Soft Delete**: Pasien, registration, dan admission menggunakan soft delete. Penghapusan mengubah status menjadi `deleted` dan mengisi `deleted_at` dalam satu transaksi.

## Constraint Admission (Approved)

- Satu registration boleh masuk ke poliklinik berbeda pada tanggal pelayanan yang sama.
- Satu registration boleh masuk ke poliklinik yang sama pada tanggal pelayanan berbeda.
- Satu registration tidak boleh memiliki dua admission ke poliklinik yang sama pada tanggal pelayanan yang sama.
- Aturan duplikasi pasien lintas pendaftaran (registration berbeda) akan diperiksa pada level service/API.
- Constraint database: `registration_id` + `polyclinic_id` + `service_date`.
- Secara arsitektur, corrective migration (bukan edit schema) diperlukan karena migration lama sudah berstatus `Ran` pada database development.

## Status Transaksi (Approved)

### Pendaftaran (`outpatient_registrations`)
- `draft`: Pendaftaran awal / booking online belum dikonfirmasi.
- `registered`: Pendaftaran dikonfirmasi dan aktif.
- `cancelled`: Pendaftaran dibatalkan sebelum admisi.
- `deleted`: Record dihapus secara logis; berbeda dari `cancelled`.

### Admisi (`outpatient_admissions`)
- `waiting`: Menunggu panggilan / asesmen awal.
- `admitted`: Sudah masuk poli / diterima.
- `in_service`: Sedang dalam pemeriksaan dokter.
- `completed`: Pelayanan selesai (lanjut billing/pulang).
- `cancelled`: Kunjungan dibatalkan.
- `deleted`: Record dihapus secara logis; berbeda dari `cancelled`.

### Pasien (`patients`)
- `active`: Pasien aktif.
- `deleted`: Record dihapus secara logis.

## Penghapusan

- Flow normal tidak melakukan hard delete.
- Soft delete harus mengubah status menjadi `deleted` dan mengisi `deleted_at` secara atomik.
- Foreign key tidak boleh menghapus transaksi medis secara cascade tanpa keputusan eksplisit.
