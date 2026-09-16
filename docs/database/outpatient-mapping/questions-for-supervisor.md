# Questions for Supervisor

1. **Aturan Nomor Rekam Medis (RM)**: Apakah format RM baru akan melanjutkan penomoran dari sistem lama (`simrs_lama`), atau dimulai dari format baru untuk `bigsirs_dev`?
2. **Pemisahan Registrasi & Admisi**: Apakah alur bisnis operasional memerlukan dua ent テーブル terpisah (`outpatient_registrations` dan `outpatient_admissions`), atau cukup disatukan seperti struktur lama `trx_admisi`?
3. **Kardinalitas Pasien dan Penjamin**: Apakah penjamin wajib melekat pada setiap transaksi admisi, atau cukup diambil dari default penjamin pasien?
4. **Kewajiban Pemilihan Dokter**: Apakah pemilihan dokter (DPjp) wajib diisi saat admisi rawat jalan, atau boleh dikosongkan sampai pasien diperiksa di poliklinik?
5. **Penanganan Data Historis NIK Ganda**: Bagaimana strategi penanganan jika ditemukan nomor NIK yang sama untuk lebih dari satu pasien saat migrasi data historis?
