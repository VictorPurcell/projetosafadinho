<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Cnpj implements Rule
{
    public function passes($attribute, $value)
    {
        $value = preg_replace('/[^0-9]/', '', $value);

        if (strlen($value) != 14) return false;

        // Validação do CNPJ
        for ($t = 12; $t < 14; $t++) {
            $d = 0;
            $c = 0;
            for ($m = $t - 7; $m >= 2; $m--, $c++) {
                $d += $value[$c] * $m;
            }
            for ($m = 9; $m >= 2; $m--, $c++) {
                $d += $value[$c] * $m;
            }
            $d = (10 * $d) % 11 % 10;
            if ($value[$t] != $d) return false;
        }
        return true;
    }

    public function message()
    {
        return 'O CNPJ informado é inválido';
    }
}