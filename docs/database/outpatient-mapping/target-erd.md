# Target ERD (Rancangan)

```mermaid
erDiagram
    PATIENTS ||--o{ OUTPATIENT_REGISTRATIONS : has
    PATIENTS ||--o{ OUTPATIENT_ADMISSIONS : visits
    OUTPATIENT_REGISTRATIONS ||--o{ OUTPATIENT_ADMISSIONS : leads_to
    POLYCLINICS ||--o{ OUTPATIENT_ADMISSIONS : receives
    MEDICAL_PERSONNEL ||--o{ OUTPATIENT_ADMISSIONS : attends
    GUARANTORS ||--o{ OUTPATIENT_ADMISSIONS : covers
    OUTPATIENT_ADMISSIONS ||--o{ OUTPATIENT_QUEUES : generates
    POLYCLINICS ||--o{ OUTPATIENT_QUEUES : belongs_to
```

**Kardinalitas (Approved by Supervisor 2026-09-17)**:
- Satu outpatient registration dapat memiliki nol atau banyak outpatient admissions.
- Satu outpatient admission hanya dimiliki satu outpatient registration.
- Composite unique: `registration_id` + `polyclinic_id` + `service_date` pada admissions.
- Guarantor melekat pada admission, bukan pada patient atau registration.
