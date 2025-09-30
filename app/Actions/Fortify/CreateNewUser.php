<?php

namespace App\Actions\Fortify;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Validation\Rules\Password;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered customer.
     */
    public function create(array $input): Customer
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],
            'password' => $this->passwordRules(),
            // address is optional for now; make it required if you want
            'address' => ['nullable', 'string', 'max:255'],
        ])->validate();

        return Customer::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
            'address'  => $input['address'] ?? null,
        ]);
    }

    /**
     * Default password rules (Jetstream helper).
     */
    protected function passwordRules(): array
    {
        return [Password::defaults()];
    }
}
