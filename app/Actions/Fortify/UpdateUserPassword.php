<?php

namespace App\Actions\Fortify;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    /**
     * Validate and update the customer's password.
     */
    public function update($user, array $input): void
    {
        /** @var Customer $user */
        Validator::make($input, [
            'current_password' => ['required', 'current_password'],
            'password' => ['required','string','confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
