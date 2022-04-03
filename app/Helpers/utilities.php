<?php


use Illuminate\Support\Str;

/**
 * Generate random codes
 *
 * @param integer $length Length of code
 * @param boolean $numeric alpha or num
 * @return string
 */
if (!function_exists('generate_code')) {
    function generate_code(int $length, bool $numeric = false)
    {
        if ($numeric) {
            $code = mt_rand(str_repeat(1, $length), str_repeat(9, $length));
        } else {
            $code = Str::random($length);
            if (strlen($code) < 6) {
                generate_code(6);
            }
        }

        return $code;
    }
}

if (!function_exists('generate_reference')) {
    function generate_reference()
    {
        return uniqid('fincra-');
    }
}
