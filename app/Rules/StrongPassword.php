<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Untuk local development dan testing, minimal 6 karakter
        if (app()->environment('local', 'testing')) {
            return is_string($value) && strlen($value) >= 6;
        }

        // Minimum 8 karakter
        if (strlen($value) < 8) {
            return false;
        }

        // Harus mengandung setidaknya satu huruf kecil
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }

        // Harus mengandung setidaknya satu huruf besar
        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }

        // Harus mengandung setidaknya satu angka
        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (app()->environment('local', 'testing')) {
            return 'Password minimal 6 karakter.';
        }
        return 'Password harus minimal 8 karakter dan mengandung huruf besar, huruf kecil, serta angka.';
    }
}

