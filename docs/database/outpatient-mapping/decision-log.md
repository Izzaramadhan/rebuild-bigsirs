# Decision Log

| Decision | Status | Evidence | Impact | Owner | Next Action |
| -------- | ------ | -------- | ------ | ----- | ----------- |
| Pemisahan tabel `patients`, `outpatient_registrations`, dan `outpatient_admissions` | proposed | Best practice arsitektur REST API & normalisasi | Mencegah pencampuran identitas dan transaksi kunjungan | Tim Arsitektur | Konfirmasi dengan pembimbing/stakeholder |
| Penggunaan `varchar(20)` untuk NIK | proposed | Standar Dukcapil / best practice | Memungkinkan penyimpanan format NIK dengan aman | Tim Database | Validasi sampel data lama |
| Normalisasi penjamin ke tabel `guarantors` | proposed | Struktur `ref_jenis_asuransi` di `simrs_lama` | Mempermudah manajemen master asuransi/BPJS | Tim Backend | Verifikasi field penjamin di admisi |
