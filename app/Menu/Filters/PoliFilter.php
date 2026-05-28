<?php

namespace App\Menu\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class PoliFilter implements FilterInterface
{
    public function transform($item)
    {
        if (!isset($item['poli'])) {
            return $item;
        }

        $isIgd = session('kd_poli') === 'IGDK';

        if ($item['poli'] === 'IGDK' && !$isIgd) {
            $item['restricted'] = true;
        }

        if ($item['poli'] === 'NON_IGD' && $isIgd) {
            $item['restricted'] = true;
        }

        return $item;
    }
}
