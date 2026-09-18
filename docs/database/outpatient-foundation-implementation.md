# Implementasi Fondasi Outpatient

Status: **terverifikasi pada `bigsirs_test`**. Migration bisnis belum diterapkan ke `bigsirs_dev`.

## Scope

Fondasi backend Rawat Jalan mencakup tabel target, model Eloquent, enum, factory, seeder master, dan feature test untuk:

- `patients`
- `outpatient_registrations`
- `outpatient_admissions`
- `outpatient_queues`
- `polyclinics`
- `medical_personnel`
- `guarantors`

Tidak ada endpoint API bisnis, controller, FormRequest, service, autentikasi, atau integrasi frontend dalam tahap ini.

## Migration

Migration bisnis berada di `backend/database/migrations/2026_09_16_100001_create_guarantors_table.php` sampai `backend/database/migrations/2026_09_16_100007_create_outpatient_queues_table.php`.

Constraint utama yang diterapkan:

- `patients.medical_record_number` unique.
- `patients.nik` nullable dan unique jika diisi.
- `guarantors.code` nullable dan unique jika diisi.
- `polyclinics.code` unique.
- `medical_personnel.str_number` dan `medical_personnel.dpjp_code` nullable dan unique jika diisi.
- `outpatient_admissions.admission_no` unique (format rencana: `RJ-YYYYMMDD-NNNN`).
- Constraint database `registration_id`, `polyclinic_id`, `service_date` pada admisi memastikan tidak ada duplikasi poli pada tanggal yang sama untuk satu pendaftaran. Aturan lintas pendaftaran akan diperiksa pada service/API.

## Relasi dan Delete Behavior

- `outpatient_registrations.patient_id` -> `patients.id`, `restrictOnDelete`.
- `outpatient_admissions.registration_id` -> `outpatient_registrations.id`, `restrictOnDelete`.
- `outpatient_admissions.patient_id` -> `patients.id`, `restrictOnDelete`.
- `outpatient_admissions.polyclinic_id` -> `polyclinics.id`, `restrictOnDelete`.
- `outpatient_admissions.doctor_id` -> `medical_personnel.id`, `nullOnDelete`.
- `outpatient_admissions.guarantor_id` -> `guarantors.id`, `nullOnDelete`.
- `outpatient_queues.admission_id` -> `outpatient_admissions.id`, `nullOnDelete`.
- `outpatient_queues.polyclinic_id` -> `polyclinics.id`, `restrictOnDelete`.

Soft delete diterapkan pada `patients`, `outpatient_registrations`, dan `outpatient_admissions` sesuai rancangan target yang memuat `deleted_at`.

## Model dan Enum

Model berada di `backend/app/Models/`:

- `Patient`
- `OutpatientRegistration`
- `OutpatientAdmission`
- `OutpatientQueue`
- `Polyclinic`
- `MedicalPersonnel`
- `Guarantor`

Enum berada di `backend/app/Enums/`:

- `RegistrationStatus`: `draft`, `registered`, `cancelled`
- `AdmissionStatus`: `waiting`, `admitted`, `in_service`, `completed`, `cancelled`
- `GuarantorType`: `UMUM`, `BPJS`, `PRIVATE`

Enum digunakan sebagai cast pada model terkait. Tanggal dan boolean juga dicast sesuai field target.

## Factory dan Seeder

Factory sintetis tersedia untuk seluruh model fondasi. Factory tidak mengambil data dari `simrs_lama`.

Seeder master tersedia untuk:

- `GuarantorSeeder`
- `PolyclinicSeeder`
- `MedicalPersonnelSeeder`

Seeder belum otomatis dipanggil oleh `DatabaseSeeder` agar tidak mengubah isi database tanpa keputusan eksplisit.

## Feature Test

Feature test bisnis berada di `backend/tests/Feature/OutpatientFoundationTest.php`.

Cakupan test:

- Ketersediaan tabel dan kolom inti.
- Unique constraint untuk nomor RM, NIK, kode master, nomor registrasi, dan nomor admisi.
- Factory sintetis pasien, master data, registration, admission, dan queue.
- Cast tanggal, boolean, dan enum.
- Relasi patient-registration, registration-admission, admission-polyclinic, admission-guarantor, dan queue.
- Soft delete pada `patients`, `outpatient_registrations`, dan `outpatient_admissions`.
- Delete behavior `nullOnDelete` untuk guarantor.

## Hasil Verifikasi

Verifikasi database testing:

```bash
php artisan tinker --env=testing --execute="dump(\Illuminate\Support\Facades\DB::connection()->getDatabaseName());"
```

Hasil: `bigsirs_test`.

Migration testing:

```bash
php artisan migrate:fresh --env=testing
php artisan migrate:status --env=testing
```

Hasil: seluruh migration default Laravel dan migration fondasi outpatient berstatus `Ran` pada `bigsirs_test`.

Test:

```bash
php artisan test --env=testing
```

Hasil: `13 passed`, `34 assertions`.

Format:

```bash
vendor\bin\pint --test
```

Hasil: passed.

SQL preview development:

```bash
php artisan tinker --execute="dump(\Illuminate\Support\Facades\DB::connection()->getDatabaseName());"
php artisan migrate --pretend
```

Hasil koneksi default: `bigsirs_dev`. SQL preview hanya membuat atau mengubah tabel target fondasi outpatient dan tabel default Laravel yang masih pending; tidak menyentuh `simrs_lama`.

## Keputusan Mapping yang Diterapkan

- Nomor rekam medis disimpan sebagai `patients.medical_record_number`, wajib dan unique (format baru direkomendasikan 6 digit, legacy dipertahankan saat migrasi).
- NIK disimpan sebagai `varchar(20)`, nullable, dan unique jika diisi.
- Registration dan admission dipisah menjadi dua tabel dengan relasi 1:N (Satu registration bisa memiliki banyak admission).
- Tidak ada nomor pendaftaran. Nomor kunjungan menggunakan `admission_no` dengan format rencana `RJ-YYYYMMDD-NNNN`.
- Penjamin (Guarantor) hanya melekat pada admisi (tidak ada di pasien atau pendaftaran).
- Dokter pada admission bersifat opsional.
- Status registration dan admission mengikuti business rules.
- Aturan lintas pendaftaran (pasien yang sama, poli sama, tanggal sama) harus ditangani di service/API.
- Antrean outpatient diimplementasikan sebagai tabel terpisah.
- Soft delete digunakan pada pasien dan transaksi pendaftaran/admisi.
- Corrective migration digunakan karena migration lama sudah pernah dijalankan.

## Keputusan yang Masih Terbuka

- Format final nomor rekam medis: melanjutkan legacy atau format baru.
- Format final nomor registrasi dan nomor admisi.
- Strategi migrasi data historis jika ditemukan NIK atau nomor RM duplikat.
- Apakah master seeder boleh otomatis dipanggil oleh `DatabaseSeeder` pada environment development.

## Cara Menjalankan Migration Testing

Gunakan hanya environment testing:

```bash
cd backend
php artisan tinker --env=testing --execute="dump(\Illuminate\Support\Facades\DB::connection()->getDatabaseName());"
php artisan migrate:fresh --env=testing
php artisan test --env=testing
```

Jalankan `migrate:fresh` hanya jika hasil verifikasi database adalah `bigsirs_test`.

## Cara Menerapkan ke Development Setelah Disetujui

Setelah supervisor menyetujui mapping dan aturan nomor bisnis:

```bash
cd backend
php artisan tinker --execute="dump(\Illuminate\Support\Facades\DB::connection()->getDatabaseName());"
php artisan migrate --pretend
php artisan migrate
```

Jalankan `php artisan migrate` hanya jika koneksi default terverifikasi `bigsirs_dev` dan SQL preview sudah ditinjau.

## Cara Rollback

Rollback migration terakhir di database yang benar:

```bash
cd backend
php artisan migrate:rollback --step=7
```

Jangan menjalankan rollback, wipe, atau fresh pada `simrs_lama`.
