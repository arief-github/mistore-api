<?php

if (! function_exists('moneyFormat')) {
    /**
     * money Format
     *
     */

    function moneyFormat($str) {
        return 'Rp. ' .number_format($str, '0', '', '.');
    }
}
