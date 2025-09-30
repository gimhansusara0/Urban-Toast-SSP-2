<?php

namespace App\Actions\Fortify;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    /**
     * Reset the given customer's password.
     */
    public function reset($user, array $input): void
    {
        /** @var Customer $user */
        Validator::make($input, [
            'password' => ['required','string','confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
