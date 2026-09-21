<?php

if (!function_exists('format_status')) {
    function format_status($string)
    {
        // Ganti underscore dengan spasi, lalu jadikan huruf besar di awal kata
        return ucfirst(str_replace('_', ' ', $string));
    }
}
