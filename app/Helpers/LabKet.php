<?php

namespace App\Helpers;

class LabKet
{
    /**
     * Deteksi otomatis H/L/N dari nilai + nilai_rujukan.
     * Format nilai_rujukan yang disupport:
     *   "10-20", "10 - 20", "10.5 -  20.0"
     *   "<5", "> 100", "<= 5", ">=100"
     *   "10:20" (Khanza kadang pakai)
     *
     * @return string  'H' | 'L' | 'N' | '' (kalau tidak bisa diparse)
     */
    public static function auto($nilai, $rujukan): string
    {
        $n = is_numeric($nilai) ? (float) $nilai : null;
        if ($n === null) return '';

        $r = trim((string) $rujukan);
        if ($r === '' || $r === '-') return '';

        // Format batas atas saja: "<5", "<=5"
        if (preg_match('/^<=?\s*([0-9.,]+)/', $r, $m)) {
            $max = (float) str_replace(',', '.', $m[1]);
            if ($n > $max) return 'H';
            return 'N';
        }

        // Format batas bawah saja: ">5", ">=5"
        if (preg_match('/^>=?\s*([0-9.,]+)/', $r, $m)) {
            $min = (float) str_replace(',', '.', $m[1]);
            if ($n < $min) return 'L';
            return 'N';
        }

        // Format range: "10-20" / "10:20" / "10 sd 20"
        if (preg_match('/([0-9.,]+)\s*(?:-|:|sd|s\/d|\.\.|to)\s*([0-9.,]+)/i', $r, $m)) {
            $min = (float) str_replace(',', '.', $m[1]);
            $max = (float) str_replace(',', '.', $m[2]);
            if ($n < $min) return 'L';
            if ($n > $max) return 'H';
            return 'N';
        }

        return '';
    }
}
