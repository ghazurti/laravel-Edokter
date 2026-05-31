<?php

namespace App\Helpers;

class Terbilang
{
    private static $satuan = [
        '', 'satu', 'dua', 'tiga', 'empat', 'lima',
        'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas',
    ];

    public static function make($angka): string
    {
        $angka = (int) abs($angka);
        return trim(self::konversi($angka)) ?: 'nol';
    }

    private static function konversi($n): string
    {
        if ($n < 12) {
            return self::$satuan[$n];
        }
        if ($n < 20) {
            return self::konversi($n - 10) . ' belas';
        }
        if ($n < 100) {
            return self::konversi(intdiv($n, 10)) . ' puluh ' . self::konversi($n % 10);
        }
        if ($n < 200) {
            return 'seratus ' . self::konversi($n - 100);
        }
        if ($n < 1000) {
            return self::konversi(intdiv($n, 100)) . ' ratus ' . self::konversi($n % 100);
        }
        if ($n < 2000) {
            return 'seribu ' . self::konversi($n - 1000);
        }
        if ($n < 1000000) {
            return self::konversi(intdiv($n, 1000)) . ' ribu ' . self::konversi($n % 1000);
        }
        if ($n < 1000000000) {
            return self::konversi(intdiv($n, 1000000)) . ' juta ' . self::konversi($n % 1000000);
        }
        return (string) $n;
    }
}
