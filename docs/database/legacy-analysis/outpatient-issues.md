# Isu Struktur Rawat Jalan Legacy

## Integritas data
- `ref_pasien.no_rm` dan `master_person_index.no_ktp` tidak memiliki unique index maupun index pencarian.
- Penjamin di pasien/admisi tidak memiliki FK ke `ref_jenis_asuransi`.

## Konsistensi tipe data
- Nomor antrean menggunakan `varchar(10)` di `trx_antrian` dan `varchar(5)` di `trx_pendaftaran_online`.
- `no_rm` pasien `varchar(50)`, sedangkan antrean `varchar(20)`; kecocokan penuh berisiko terpotong bila digunakan lintas tabel.

## Relasi
- Hanya tiga FK yang terkonfirmasi dalam lingkup: person→pasien, person→dokter, dan poli→visit.
- Relasi inti pasien→admisi dan admisi→visit bersifat inferred walau sumbernya diindeks.
- Dokter pada `trx_visit.id_dokter` tidak berindex atau ber-FK.

## Index dan pencarian
- Kolom pencarian bisnis utama (NIK, nomor RM, nomor antrean) tidak berindex dalam metadata yang diperiksa.

## Penamaan
- `master_person_index` mencampur person pasien/dokter/karyawan melalui `jenis_person`; perlu konfirmasi sumber resmi identitas pasien.
- `trx_bukti_daftar` ambigu dan tidak dapat dipastikan perannya.

## Audit trail
- Banyak tabel memiliki `deleted_at`, tetapi beberapa didefinisikan `NOT NULL`/default saat ini (`ref_pegawai`, `trx_visit`), sehingga indikasi soft delete perlu verifikasi.
- Kode status enum/integer tidak memiliki kamus arti pada metadata.

## Maintainability dan risiko migrasi
- Poliklinik, dokter, dan penjamin memiliki relasi implisit yang harus divalidasi sebelum migrasi untuk mencegah orphan mapping atau risiko duplikasi.
- `tbl_bpjs_sep` menyimpan beberapa atribut penjamin/poli sebagai teks; jangan diasumsikan sebagai master referensi.
