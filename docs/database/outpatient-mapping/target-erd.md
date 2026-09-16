# Target ERD (Rancangan)

```mermaid
erDiagram
    PATIENTS ||--o{ OUTPATIENT_REGISTRATIONS : has
    PATIENTS ||--o{ OUTPATIENT_ADMISSIONS : visits
    OUTPATIENT_REGISTRATIONS ||--o| OUTPATIENT_ADMISSIONS : leads_to
    POLYCLINICS ||--o{ OUTPATIENT_ADMISSIONS : receives
    MEDICAL_PERSONNEL ||--o{ OUTPATIENT_ADMISSIONS : attends
    GUARANTORS ||--o{ PATIENTS : defaults
    GUARANTORS ||--o{ OUTPATIENT_REGISTRATIONS : covers
    OUTPATIENT_ADMISSIONS ||--o{ OUTPATIENT_QUEUES : generates
    POLYCLINICS ||--o{ OUTPATIENT_QUEues : belongs_to
```

*Catatan*: Relasi di atas adalah rancangan target konseptual baru yang menormalisasi hubungan dari sistem lama (`simrs_lama`) untuk memisahkan entitas permanen pasien dari transaksi registrasi, admisi, dan antrean.
