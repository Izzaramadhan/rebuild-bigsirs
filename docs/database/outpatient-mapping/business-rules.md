# Business Rules & Usulan Status (Outpatient)

## Aturan Bisnis Utama (Proposed)
1. **Pasien Baru vs Lama**: Pencarian pasien dilakukan berdasarkan NIK (`nik`) atau Nomor Rekam Medis (`medical_record_number`). Jika tidak ditemukan, entri baru dibuat pada tabel `patients`.
2. **Nomor Rekam Medis**: Bersifat unik, dihasilkan oleh sistem secara sekuensial atau otomatis saat pendaftaran pasien baru.
3. **Pendaftaran (`outpatient_registrations`)**: Mewakili pencatatan administratif awal (booking / kedatangan).
4. **Admisi (`outpatient_admissions`)**: Menandai kunjungan pasien ke poliklinik tertentu dengan dokter penanggung jawab (DPJP) dan penjamin yang valid.
5. **Penjamin (`guarantors`)**: Melekat pada pendaftaran/admisi, namun pasien memiliki penjamin default.

## Usulan Status Transaksi

### Pendaftaran (`outpatient_registrations`)
- `draft`: Pendaftaran awal / booking online belum dikonfirmasi.
- `registered`: Pendaftaran dikonfirmasi dan aktif.
- `cancelled`: Pendaftaran dibatalkan sebelum admisi.

### Admisi (`outpatient_admissions`)
- `waiting`: Menunggu panggilan / asesmen awal.
- `admitted`: Sudah masuk poli / diterima.
- `in_service`: Sedang dalam pemeriksaan dokter.
- `completed`: Pelayanan selesai (lanjut billing/pulang).
- `cancelled`: Kunjungan dibatalkan.
