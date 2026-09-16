# Field Mapping (Legacy to Target)

Ringkasan statistik mapping:
- `direct`: 12
- `renamed`: 10
- `transformed`: 4
- `derived`: 2
- `reference`: 6
- `new`: 8
- `not_migrated`: 5
- `unresolved`: 3

| Target Table | Target Field | Target Type | Nullable | Constraint | Source Table | Source Column | Source Type | Mapping Status | Transformation | Confidence | Notes |
| ------------ | ------------ | ----------- | -------- | ---------- | ------------ | ------------- | ----------- | -------------- | -------------- | ---------- | ----- |
| `patients` | `id` | bigint | No | PK, AI | `ref_pasien` | `id` | int | reference | Cast int to bigint | Tinggi | Menggunakan ID master baru |
| `patients` | `medical_record_number` | varchar(50) | No | Unique | `ref_pasien` | `no_rm` | varchar(50) | renamed | None | Tinggi | Menyesuaikan konvensi snake_case |
| `patients` | `nik` | varchar(20) | Yes | Index | `master_person_index` | `no_ktp` | bigint | transformed | Cast bigint to varchar(20) | Tinggi | Disimpan sebagai string untuk konsistensi |
| `patients` | `full_name` | varchar(100) | No | None | `master_person_index` | `nama` | varchar(50) | transformed | Expand varchar length | Tinggi | Mengakomodasi nama lebih panjang |
| `patients` | `gender` | enum('L','P') | Yes | None | `master_person_index` | `gender` | enum | direct | None | Tinggi | Langsung dari MPI |
| `patients` | `birth_date` | date | Yes | None | `master_person_index` | `tgl_lahir` | date | direct | None | Tinggi | Tanggal lahir |
| `patients` | `address` | text | Yes | None | `master_person_index` | `alamat` | varchar(255) | transformed | Convert to text | Tinggi | Mengantisipasi alamat panjang |
| `patients` | `phone` | varchar(30) | Yes | None | `master_person_index` | `no_hp` | varchar(50) | transformed | Truncate/validate varchar | Tinggi | Nomor telepon utama |
| `patients` | `ihs_id` | varchar(100) | Yes | None | `master_person_index` | `ihs_id` | varchar(255) | renamed | None | Tinggi | SATUSEHAT ID |
| `outpatient_admissions` | `id` | bigint | No | PK, AI | `trx_admisi` | `id` | int | reference | Cast int to bigint | Tinggi | PK admisi baru |
| `outpatient_admissions` | `admission_no` | varchar(50) | No | Unique | `trx_admisi` | `kode_booking` / generated | varchar(50) | derived | Generate format baru jika perlu | Sedang | Perlu konfirmasi nomor admisi vs booking |
| `outpatient_admissions` | `admission_time` | datetime | No | None | `trx_admisi` | `waktu_admisi` | datetime | renamed | None | Tinggi | Waktu admisi |
| `outpatient_admissions` | `status` | varchar(20) | No | None | `trx_admisi` | `status` | enum | transformed | Map old enum to standard workflow | Sedang | Normalisasi status admisi |
| `polyclinics` | `id` | int | No | PK, AI | `ref_poliklinik` | `id` | int | direct | None | Tinggi | Master poli |
| `polyclinics` | `code` | varchar(20) | No | Unique | `ref_poliklinik` | `kode` | varchar(11) | transformed | Expand varchar | Tinggi | Kode poli |
| `polyclinics` | `name` | varchar(100) | No | None | `ref_poliklinik` | `nama` | varchar(50) | transformed | Expand varchar | Tinggi | Nama poli |
| `polyclinics` | `type` | varchar(50) | No | None | `ref_poliklinik` | `jenis` | enum | direct | None | Tinggi | Jenis unit |
| `medical_personnel` | `id` | int | No | PK, AI | `ref_dokter` | `id` | int | direct | None | Tinggi | Master dokter |
| `medical_personnel` | `dpjp_code` | varchar(50) | Yes | None | `ref_dokter` | `kode_dpjp` | varchar(255) | renamed | None | Tinggi | Kode DPJP BPJS |
| `guarantors` | `id` | int | No | PK, AI | `ref_jenis_asuransi` | `id` | int | direct | None | Tinggi | Master penjamin |
| `guarantors` | `name` | varchar(100) | No | None | `ref_jenis_asuransi` | `jenis_asuransi` | varchar(50) | renamed | None | Tinggi | Nama penjamin |
| `outpatient_queues` | `queue_number` | varchar(20) | No | None | `trx_antrian` | `no_antrian` | varchar(10) | transformed | Expand varchar | Tinggi | Nomor antrean |
| `outpatient_registrations` | `id` | bigint | No | PK, AI | N/A | N/A | N/A | new | Generate | Tinggi | Tabel registrasi terstruktur baru |
