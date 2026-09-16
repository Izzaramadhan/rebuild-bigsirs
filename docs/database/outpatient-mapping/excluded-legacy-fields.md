# Excluded Legacy Fields

Field lama berikut tidak disarankan untuk dimasukkan langsung ke database baru karena redundansi, struktur tidak baku, atau tidak relevan dengan arsitektur baru.

| Tabel Lama | Kolom Lama | Alasan Tidak Digunakan | Risiko | Confidence |
| ---------- | ---------- | ---------------------- | ------ | ---------- |
| `trx_bukti_daftar` | All columns | Tabel kosong/tidak terpakai dalam alur aktif (perkiraan 0 baris) | Kehilangan data historis fiktif (tidak ada risiko nyata) | Tinggi |
| `master_person_index` | `foto`, `photo_path` | Penyimpanan file fisik sebaiknya dikelola storage service terpisah, bukan di kolom tabel database utama | Path file lama tidak valid | Tinggi |
| `master_person_index` | `parent_id` | Struktur hierarki person belum terbukti diperlukan untuk pasien rawat jalan standar | Salah relasi keluarga | Sedang |
| `trx_admisi` | Kolom kecelakaan (`no_polisi`, `tempat_kecelakaan`, dll.) | Detail spesifik kecelakaan sebaiknya dipisah ke tabel asesmen/trauma khusus jika diperlukan | Kolom kosong mendominasi tabel utama | Sedang |
| `log_bpjs` | All columns | Log komunikasi API sebaiknya disimpan di log storage / tabel log terpisah dari operasional inti | Ukuran database membengkak | Tinggi |
