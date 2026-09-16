# Migration Risks

1. **Perbedaan Tipe Data**: Kolom NIK di sistem lama menggunakan `bigint`, sedangkan standar baru menggunakan `varchar(20)` untuk mengantisipasi leading zero atau format khusus; transformasi tipe wajib diuji.
2. **Ketiadaan Foreign Key di Sistem Lama**: Sebagian besar relasi di `simrs_lama` bersifat *inferred* (tanpa FK fisik). Saat migrasi data historis, risiko *orphan records* sangat tinggi jika validasi ID tidak ketat.
3. **Duplikasi Data Pasien**: Sistem lama menggunakan `master_person_index` tanpa unique constraint ketat pada NIK, berisiko melahirkan data pasien ganda saat migrasi.
4. **Konsistensi Nomor Rekam Medis**: `no_rm` di sistem lama tidak memiliki unique index di database asal, sehingga perlu deduplikasi sebelum masuk ke kolom `medical_record_number` yang bernilai `UNIQUE`.
5. **Perubahan Struktur Transaksi**: Pemisahan tegas antara registrasi (`outpatient_registrations`) dan admisi (`outpatient_admissions`) membutuhkan skrip migrasi yang mampu memetakan ulang data `trx_admisi` lama ke dua tabel target tersebut.
