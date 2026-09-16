# Mapping Rancangan Database Outpatient (Rawat Jalan)

## Tujuan
Membuat rancangan konseptual dan mapping field dari database lama (`simrs_lama`) ke rancangan struktur database baru (`bigsirs_dev`) untuk modul Pendaftaran dan Admisi Rawat Jalan.

## Sumber Analisis
- Inventarisasi metadata `docs/database/legacy-analysis/`
- Konteks alur bisnis BigSIRS

## Batasan
- Read-only terhadap `simrs_lama`
- Tanpa pembuatan migration, model, controller, endpoint, seeder, atau halaman Vue
- Rancangan berstatus **Draft / Proposed** dan membutuhkan konfirmasi bisnis

## Daftar Dokumen Mapping
1. `target-data-model.md`: Definisi tabel target
2. `field-mapping.md`: Pemetaan field lama ke baru beserta status transformasinya
3. `excluded-legacy-fields.md`: Kolom lama yang tidak dimigrasi
4. `business-rules.md`: Aturan dan status bisnis usulan
5. `target-erd.md`: ERD Mermaid rancangan baru
6. `migration-risks.md`: Risiko migrasi data
7. `decision-log.md`: Log keputusan desain
8. `questions-for-supervisor.md`: Daftar pertanyaan konfirmasi untuk pembimbing
