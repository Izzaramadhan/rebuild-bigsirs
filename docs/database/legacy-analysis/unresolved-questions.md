# Pertanyaan Belum Terjawab

- Tabel mana yang menjadi sumber resmi data pasien: `master_person_index` atau `ref_pasien`?
- Bagaimana nomor rekam medis dihasilkan, dan apakah `ref_pasien.no_rm` unik?
- Apakah satu pendaftaran/admisI dapat memiliki lebih dari satu kunjungan?
- Apa perbedaan nomor registrasi, nomor kunjungan, dan nomor admisi; tabel mana yang menyimpan nomor registrasi?
- Apakah dokter wajib dipilih saat admisi atau baru saat visit?
- Apakah penjamin melekat pada pasien, admisi, atau keduanya; mana yang berlaku untuk billing?
- Kapan nomor antrean dibuat dan bagaimana hubungan `trx_pendaftaran_online.no_antrian` dengan `trx_antrian.no_antrian`?
- Bagaimana transaksi dibatalkan: hard delete, `deleted_at`, atau kode status?
- Apakah general consent disimpan pada database, dokumen, atau hanya dicetak?
- Apa arti nilai setiap status pada `trx_admisi`, `trx_visit`, dan `trx_antrian`?
- Mengapa `trx_bukti_daftar` tidak memiliki audit dan hanya perkiraan 0 baris?
- Apakah `id_poliklinik_previous` dan `id_perawat` pada visit mereferensikan master tertentu?
