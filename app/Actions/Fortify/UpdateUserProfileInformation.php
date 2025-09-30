<?php

namespace App\Actions\Fortify;

use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given customer's profile information.
     */
    public function update($user, array $input): void
    {
        /** @var Customer $user */
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required','email','max:255',
                Rule::unique('customers','email')->ignore($user->id),
            ],
            'address' => ['nullable','string','max:255'],
        ])->validate();

        $user->forceFill([
            'name'    => $input['name'],
            'email'   => $input['email'],
            'address' => $input['address'] ?? $user->address,
        ])->save();
    }
}
