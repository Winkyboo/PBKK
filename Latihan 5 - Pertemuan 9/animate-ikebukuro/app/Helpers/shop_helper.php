<?php

if (! function_exists('rupiah')) {
    function rupiah(float|int|string $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}
