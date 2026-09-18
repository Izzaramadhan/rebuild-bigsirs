<?php

namespace App\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Generator concurrency-safe untuk nomor rekam medis pasien baru.
 *
 * Nomor output selalu enam digit (000001, 000002, dst.).
 * Nomor legacy dari sistem lama dipertahankan apa adanya; generator
 * hanya menghasilkan nomor untuk pasien baru dan tidak membatasi
 * kolom `medical_record_number` menjadi enam digit.
 *
 * Overflow setelah 999999 menghentikan generator dan melempar exception
 * domain yang jelas. Sinkronisasi sequence legacy dilakukan secara
 * terpisah sebelum pasien baru diaktifkan.
 */
class MedicalRecordNumberGenerator
{
    private const KEY = 'medical-record';

    private const MAX = 999999;

    private const WIDTH = 6;

    public function generate(): string
    {
        return DB::transaction(function (): string {
            $row = DB::table('number_sequences')
                ->where('key', self::KEY)
                ->lockForUpdate()
                ->first();

            if ($row === null) {
                try {
                    DB::table('number_sequences')->insert([
                        'key' => self::KEY,
                        'last_number' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    return str_pad((string) 1, self::WIDTH, '0', STR_PAD_LEFT);
                } catch (QueryException $e) {
                    if ($e->errorInfo[1] === 1062) { // Duplicate entry
                        // Lock and read again since another transaction just created it
                        $row = DB::table('number_sequences')
                            ->where('key', self::KEY)
                            ->lockForUpdate()
                            ->first();

                        if ($row === null) {
                            throw $e; // Should not happen
                        }
                    } else {
                        throw $e;
                    }
                }
            }

            $next = $row->last_number + 1;

            if ($next > self::MAX) {
                throw new \RuntimeException(
                    'Medical record number sequence has reached the maximum allowed value of '.self::MAX.'.'
                );
            }

            DB::table('number_sequences')
                ->where('key', self::KEY)
                ->update(['last_number' => $next, 'updated_at' => now()]);

            return str_pad((string) $next, self::WIDTH, '0', STR_PAD_LEFT);
        }, 5);
    }

    public function syncFromExisting(int $maxExisting): void
    {
        if ($maxExisting > self::MAX) {
            throw new \RuntimeException(
                'Cannot synchronize sequence: existing maximum medical record number exceeds allowed limit of '.self::MAX.'.'
            );
        }

        DB::table('number_sequences')->updateOrInsert(
            ['key' => self::KEY],
            ['last_number' => max($maxExisting, 1), 'updated_at' => now()]
        );
    }

    public function current(): int
    {
        return (int) (DB::table('number_sequences')
            ->where('key', self::KEY)
            ->value('last_number') ?? 0);
    }

    public function reset(): void
    {
        DB::table('number_sequences')->where('key', self::KEY)->delete();
    }
}
