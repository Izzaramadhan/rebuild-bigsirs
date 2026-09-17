# Decision Log (Outpatient Foundation)

| Decision | Status | Evidence | Impact | Owner | Next Action |
| -------- | ------ | -------- | ------ | ----- | ----------- |
| Pemisahan tabel `patients`, `outpatient_registrations`, dan `outpatient_admissions` | approved | Supervisor decision 2026-09-17 | Mencegah pencampuran identitas dan transaksi kunjungan | Tim Arsitektur | Implementasi |
| Penggunaan `varchar(20)` untuk NIK | approved | Standar Dukcapil / best practice | Memungkinkan penyimpanan format NIK dengan aman | Tim Database | Implementasi |
| Normalisasi penjamin ke tabel `guarantors` | approved | Supervisor decision 2026-09-17 | Mempermudah manajemen master asuransi/BPJS | Tim Backend | Implementasi |
| Kardinalitas registration-admission (one-to-many) | approved | Supervisor decision 2026-09-17 | Satu registration dapat memiliki banyak admission | Tim Backend | Implementasi |
| Composite unique: registration_id + polyclinic_id + service_date | approved | Supervisor decision 2026-09-17 | Mencegah duplikasi admission ke poli sama di tanggal sama | Tim Database | Implementasi |
| Guarantor melekat pada admission | approved | Supervisor decision 2026-09-17 | Transaksi penjamin per kunjungan | Tim Backend | Hapus guarantor_id dari patients dan registrations |
| Format nomor RM pasien baru 6 digit | approved | Supervisor decision 2026-09-17 | Sequence global, legacy tetap dipertahankan | Tim Backend | Buat generator |
| Nomor registrasi pada admission: RJ-YYYYMMDD-0001 | approved | Supervisor decision 2026-09-17 | Sequence reset harian, unik global | Tim Backend | Buat generator |
| Soft delete dengan status `deleted` | approved | Supervisor decision 2026-09-17 | Audit trail, berbeda dari `cancelled` | Tim Backend | Implementasi |
