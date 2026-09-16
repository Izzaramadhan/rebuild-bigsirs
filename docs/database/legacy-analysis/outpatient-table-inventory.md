# Inventaris Tabel Rawat Jalan Legacy

## Tujuan dan batasan
Analisis metadata read-only untuk modul Pendaftaran Rawat Jalan pada `simrs_lama`. Tidak ada baris data bisnis atau pasien yang diambil.

| Entitas | Tabel Kandidat | Fungsi | Primary Key | Kolom Penting | Perkiraan Baris | Confidence | Bukti |
| ------- | -------------- | ------ | ----------- | ------------- | --------------: | ---------- | ----- |
| Identitas orang | `master_person_index` | Indeks identitas lintas jenis person | `id` | `no_ktp`, `jenis_person`, audit/status | 201793 | Tinggi | `ref_pasien.id_mpi` dan `ref_dokter.id_mpi` ber-FK ke `id` |
| Pasien | `ref_pasien` | Profil pasien dan nomor RM/penjamin awal | `id` | `id_mpi`, `no_rm`, `id_jenis_asuransi`, `no_bpjs` | 364 | Tinggi | PK; indeks `id_mpi`; FK ke MPI |
| Admisi | `trx_admisi` | Penerimaan pasien sebelum kunjungan | `id` | `id_pasien`, `id_jenis_asuransi`, `waktu_admisi`, status | 536 | Tinggi | PK dan indeks `id_pasien`; nama/struktur transaksi |
| Kunjungan | `trx_visit` | Kunjungan pelayanan termasuk tujuan poli/dokter | `id` | `id_admisi`, `id_poliklinik`, `id_dokter`, status | 730 | Tinggi | PK; indeks `id_admisi`; FK poli |
| Poliklinik | `ref_poliklinik` | Master unit/poliklinik | `id` | `kode`, `nama`, `jenis`, `kode_antrian` | 142 | Tinggi | PK; `jenis` memuat `rawat-jalan` |
| Dokter | `ref_dokter` | Master dokter | `id` | `id_mpi`, `kode_dpjp`, `kode_antrian` | 27 | Tinggi | PK; FK `id_mpi` |
| Tenaga/pegawai | `ref_pegawai` | Master pegawai | `id` | `id_mpi`, `nip` | 87 | Sedang | PK; FK `id_mpi`; pemakaian pada kunjungan belum terkonfirmasi |
| Penjamin | `ref_jenis_asuransi` | Master jenis asuransi/pembayaran | `id` | `jenis_asuransi`, `type`, `kode` | 8 | Tinggi | PK; direferensikan secara implisit oleh kolom bernama sama |
| Antrean | `trx_antrian` | Antrean terkait poli, admisi, dan visit | `id` | `id_admisi`, `id_visit`, `id_poliklinik`, `no_antrian` | 294 | Tinggi | Indeks pada ketiga kolom relasi |
| Pendaftaran online | `trx_pendaftaran_online` | Booking/pendaftaran online | `id` | `id_pasien`, `id_dokter`, `id_poliklinik`, `no_antrian` | 20 | Sedang | Nama/kolom relasi, tanpa indeks/FK |

## Tabel pendukung
`trx_jadwal_dokter` memetakan jadwal dokter dan poliklinik secara implisit. `tbl_bpjs_sep` mengaitkan data SEP dengan `id_visit` secara implisit. `trx_detail_antrian_jkn` memiliki `id_antrian`, tetapi tanpa indeks/FK.

## Kandidat yang tidak dipakai sebagai tabel inti
`trx_bukti_daftar` tidak dipakai: hanya memiliki `id_mpi`, `nama`, dan status; perkiraan baris 0. `log_bpjs` tidak dipakai karena merupakan log integrasi. Tabel BPJS lain tidak dipakai sebagai sumber utama pendaftaran.
