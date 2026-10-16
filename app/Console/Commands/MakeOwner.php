<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Creates the single admin account, or resets its password. There is no
 * registration form, so this is the only way to get a login.
 */
class MakeOwner extends Command
{
    protected $signature = 'make:owner {--name=} {--email=} {--password=}';

    protected $description = 'Create the site owner account or reset its password';

    public function handle(): int
    {
        $existing = User::query()->first();

        $name = $this->option('name') ?? text('Name', default: $existing->name ?? '', required: true);
        $email = $this->option('email') ?? text('Email', default: $existing->email ?? '', required: true);
        $password = $this->option('password') ?? password('Password (min. 12 characters)', required: true);

        $validator = Validator::make(compact('name', 'email', 'password'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:12'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        // One owner only: update the existing account instead of adding a second one.
        $user = $existing ?? new User;
        $user->fill(compact('name', 'email', 'password'))->save();

        $this->info(($existing ? 'Updated' : 'Created')." owner account for {$email}.");

        return self::SUCCESS;
    }
}
