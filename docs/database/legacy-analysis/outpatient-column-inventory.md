# Inventaris Kolom Rawat Jalan Legacy

Kolom yang memuat data pribadi hanya didokumentasikan namanya dan metadatanya; nilainya tidak diakses.

| Tabel | Kolom | Tipe | Nullable | Key/Index | Dugaan Makna | Confidence |
| ----- | ----- | ---- | -------- | --------- | ------------ | ---------- |
| `master_person_index` | `id` | int | Tidak | PK, AI | ID internal person | Tinggi |
| `master_person_index` | `no_ktp` | bigint | Ya | Tidak ada | NIK | Tinggi |
| `master_person_index` | `jenis_person` | enum | Ya | Tidak ada | Klasifikasi pasien/dokter/karyawan | Tinggi |
| `master_person_index` | `status`, `created_at`, `updated_at`, `deleted_at` | enum/datetime | Bervariasi | Tidak ada | Status dan audit/soft delete | Tinggi |
| `ref_pasien` | `id` | int | Tidak | PK, AI | ID pasien | Tinggi |
| `ref_pasien` | `id_mpi` | int | Ya | Index | Referensi person | Tinggi |
| `ref_pasien` | `no_rm` | varchar(50) | Ya | Tidak ada | Nomor rekam medis | Tinggi |
| `ref_pasien` | `id_jenis_asuransi` | int | Ya | Tidak ada | Jenis penjamin | Sedang |
| `ref_pasien` | `no_bpjs` | varchar(50) | Ya | Tidak ada | Nomor peserta BPJS | Tinggi |
| `trx_admisi` | `id` | int | Tidak | PK, AI | ID admisi | Tinggi |
| `trx_admisi` | `id_pasien` | int | Tidak | Index | Referensi pasien | Tinggi |
| `trx_admisi` | `id_jenis_asuransi` | int | Ya | Tidak ada | Penjamin per admisi | Sedang |
| `trx_admisi` | `waktu_admisi`, `waktu_keluar` | datetime | Ya | Tidak ada | Waktu proses admisi | Tinggi |
| `trx_visit` | `id` | int | Tidak | PK, AI | ID kunjungan | Tinggi |
| `trx_visit` | `id_admisi` | int | Tidak | Index | Referensi admisi | Tinggi |
| `trx_visit` | `id_poliklinik` | int | Ya | Index, FK | Poliklinik tujuan | Tinggi |
| `trx_visit` | `id_dokter` | int | Tidak | Tidak ada | Dokter layanan | Sedang |
| `trx_antrian` | `id` | int | Tidak | PK, AI | ID antrean | Tinggi |
| `trx_antrian` | `id_admisi`, `id_visit`, `id_poliklinik` | int | Tidak/tidak/tidak | Index | Referensi proses rawat jalan | Tinggi |
| `trx_antrian` | `no_antrian` | varchar(10) | Tidak | Tidak ada | Nomor antrean | Tinggi |
| `trx_pendaftaran_online` | `id_pasien`, `id_dokter`, `id_poliklinik` | int | Ya | Tidak ada | Referensi booking | Sedang |
| `ref_poliklinik` | `id`, `kode`, `nama`, `jenis` | int/varchar/enum | Bervariasi | PK pada `id` | Identitas dan jenis poli | Tinggi |
| `ref_dokter` | `id`, `id_mpi` | int | Tidak/ya | PK/index; FK `id_mpi` | Dokter dan person | Tinggi |
| `ref_jenis_asuransi` | `id`, `jenis_asuransi`, `type`, `kode` | int/varchar/enum | Bervariasi | PK pada `id` | Master penjamin | Tinggi |
