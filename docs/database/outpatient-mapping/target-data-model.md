# Target Data Model (Rancangan)

Berikut adalah ringkasan tabel target untuk modul Outpatient (Rawat Jalan):

| Tabel | Tujuan | Primary Key | Business Key | Foreign Key | Catatan |
| ----- | ------ | ----------- | ------------ | ----------- | ------- |
| `patients` | Menyimpan identitas permanen pasien | `id` (bigint) | `medical_record_number`, `nik` | None | Dipisah dari transaksi kunjungan, tidak ada `default_guarantor_id` |
| `outpatient_registrations` | Pencatatan administratif pendaftaran/booking | `id` (bigint) | None | `patient_id` | Layer pendaftaran awal tanpa nomor registrasi dan tanpa guarantor |
| `outpatient_admissions` | Kunjungan/admisi pasien ke poliklinik | `id` (bigint) | `admission_no` | `registration_id`, `patient_id`, `polyclinic_id`, `doctor_id`, `guarantor_id` | Layer poli/pelayanan dengan nomor kunjungan tersimpan sebagai `admission_no` |
| `polyclinics` | Master unit/poliklinik | `id` (int) | `code` | None | Referensi unit layanan rawat jalan |
| `medical_personnel` | Master tenaga medis/dokter | `id` (int) | `code_dpjp` / `str` | None | Berisi data dokter dan atribut penunjang |
| `guarantors` | Master penjamin/asuransi/BPJS | `id` (int) | `code` | None | Sumber pembiayaan |
| `outpatient_queues` | Antrean pelayanan rawat jalan | `id` (bigint) | `queue_no` | `admission_id`, `polyclinic_id` | Manajemen nomor antrean |

## Definisi Field Usulan per Tabel

### 1. `patients`
- `id`: bigint, PK, auto increment
- `medical_record_number`: varchar(50), unique, not null (Nomor Rekam Medis, format baru 6 digit, legacy tetap dipertahankan)
- `nik`: varchar(20), unique index, nullable (Nomor KTP/NIK)
- `full_name`: varchar(100), not null
- `gender`: enum('L','P'), nullable
- `birth_date`: date, nullable
- `birth_place`: varchar(50), nullable
- `address`: text, nullable
- `phone`: varchar(30), nullable
- `email`: varchar(100), nullable
- `religion`: varchar(30), nullable
- `blood_type`: varchar(5), nullable
- `marital_status`: varchar(30), nullable
- `status`: enum('active','deleted'), not null, default 'active'
- `ihs_id`: varchar(100), nullable (SATUSEHAT ID)
- `created_at`, `updated_at`, `deleted_at`: timestamp/datetime

### 2. `outpatient_registrations`
- `id`: bigint, PK, auto increment
- `patient_id`: bigint, not null, FK to patients
- `registration_date`: datetime, not null
- `bpjs_number`: varchar(50), nullable
- `channel`: enum('offline','online'), default 'offline'
- `status`: enum('draft','registered','cancelled','deleted'), not null
- `created_at`, `updated_at`, `deleted_at`

### 3. `outpatient_admissions`
- `id`: bigint, PK, auto increment
- `admission_no`: varchar(50), unique, not null (Nomor kunjungan, format: RJ-YYYYMMDD-NNNN)
- `registration_id`: bigint, not null, FK to outpatient_registrations
- `patient_id`: bigint, not null, FK to patients
- `polyclinic_id`: int, not null, FK to polyclinics
- `doctor_id`: int, nullable, FK to medical_personnel
- `guarantor_id`: int, nullable, FK to guarantors
- `admission_time`: datetime, not null
- `discharge_time`: datetime, nullable
- `service_date`: date, not null (tanggal pelayanan untuk constraint)
- `entry_mode`: varchar(50), nullable (cara masuk)
- `status`: enum('waiting','admitted','in_service','completed','cancelled','deleted'), not null
- `created_at`, `updated_at`, `deleted_at`

**Composite Unique Constraint**: `registration_id` + `polyclinic_id` + `service_date`

Satu registration dapat memiliki nol atau banyak admissions. Satu admission dimiliki tepat satu registration.

### 4. `polyclinics`
- `id`: int, PK, auto increment
- `code`: varchar(20), unique, not null
- `name`: varchar(100), not null
- `type`: varchar(50), not null (misal: rawat-jalan)
- `bpjs_code`: varchar(50), nullable
- `is_active`: boolean, default true
- `created_at`, `updated_at`

### 5. `medical_personnel`
- `id`: int, PK, auto increment
- `name`: varchar(100), not null
- `str_number`: varchar(50), nullable
- `sip_number`: varchar(50), nullable
- `dpjp_code`: varchar(50), nullable
- `is_active`: boolean, default true
- `created_at`, `updated_at`

### 6. `guarantors`
- `id`: int, PK, auto increment
- `code`: varchar(50), nullable
- `name`: varchar(100), not null
- `type`: enum('UMUM','BPJS','PRIVATE'), not null
- `is_active`: boolean, default true
- `created_at`, `updated_at`

### 7. `outpatient_queues`
- `id`: bigint, PK, auto increment
- `admission_id`: bigint, nullable, FK to outpatient_admissions
- `polyclinic_id`: int, not null, FK to polyclinics
- `queue_number`: varchar(20), not null
- `queue_date`: date, not null
- `status`: int, default 0
- `created_at`, `updated_at`
