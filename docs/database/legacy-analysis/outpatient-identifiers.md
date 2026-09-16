# Identifier Bisnis Rawat Jalan Legacy

| Identifier Bisnis | Tabel | Kolom | Tipe | Constraint | Dipakai Oleh | Confidence | Catatan |
| ----------------- | ----- | ----- | ---- | ---------- | ------------ | ---------- | ------- |
| ID person internal | `master_person_index` | `id` | int | PK, auto increment | `ref_pasien`, `ref_dokter`, `ref_pegawai` (FK) | Tinggi | Bukan nomor RM |
| NIK | `master_person_index` | `no_ktp` | bigint | Tanpa index | Tidak dapat dibuktikan dari metadata | Tinggi | Kolom pencarian potensial; tidak unik/index |
| ID pasien | `ref_pasien` | `id` | int | PK, auto increment | `trx_admisi` (inferred) | Tinggi | Sumber relasi admisi diindeks |
| Nomor rekam medis | `ref_pasien` | `no_rm` | varchar(50) | Tanpa index | `trx_antrian.no_rm` (unresolved; tipe berbeda panjang) | Tinggi | Tidak ada unique index |
| ID admisi | `trx_admisi` | `id` | int | PK, auto increment | `trx_visit`, `trx_antrian` (inferred) | Tinggi | Kedua sumber relasi diindeks |
| ID kunjungan | `trx_visit` | `id` | int | PK, auto increment | `trx_antrian`, `tbl_bpjs_sep` (inferred) | Tinggi | Sumber antrean diindeks |
| ID poliklinik | `ref_poliklinik` | `id` | int | PK, auto increment | `trx_visit` (FK), antrean/jadwal/online (inferred) | Tinggi | FK hanya dari visit |
| ID dokter | `ref_dokter` | `id` | int | PK, auto increment | visit/jadwal/online/antrian (inferred) | Tinggi | Tidak semua pemakaian memiliki index/FK |
| ID penjamin | `ref_jenis_asuransi` | `id` | int | PK, auto increment | pasien/admisi (inferred) | Tinggi | Tidak ada FK |
| Nomor antrean | `trx_antrian` | `no_antrian` | varchar(10) | Tanpa index | Tidak dapat dibuktikan | Tinggi | Tidak unique secara struktural |
| Nomor antrean booking | `trx_pendaftaran_online` | `no_antrian` | varchar(5) | Tanpa index | Tidak dapat dibuktikan | Sedang | Perbedaan panjang dengan antrean utama |
