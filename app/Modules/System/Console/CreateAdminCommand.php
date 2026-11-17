<?php

namespace App\Modules\System\Console;

use App\Models\User;
use App\Modules\Users\Permissions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/** Legt das erste Administratorkonto an (per SSH auf dem Server ausführen). */
class CreateAdminCommand extends Command
{
    protected $signature = 'immov:create-admin {email?} {--name=}';

    protected $description = 'Administratorkonto anlegen oder bestehendes Konto zum Administrator machen';

    public function handle(): int
    {
        Permissions::sync();

        $email = Str::lower($this->argument('email') ?? $this->ask('E-Mail'));
        $user = User::where('email', $email)->first();

        if ($user === null) {
            $name = $this->option('name') ?? $this->ask('Name');
            $password = $this->secret('Passwort (mind. 10 Zeichen, Buchstaben und Ziffern)');
            $confirm = $this->secret('Passwort wiederholen');

            $validator = Validator::make(
                ['email' => $email, 'name' => $name, 'password' => $password, 'password_confirmation' => $confirm],
                ['email' => ['required', 'email'], 'name' => ['required', 'string', 'max:255'], 'password' => ['required', 'confirmed', Password::defaults()]],
            );
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $this->error($error);
                }

                return self::FAILURE;
            }

            $user = User::create(['name' => $name, 'email' => $email, 'password' => $password, 'locale' => 'de', 'is_active' => true]);
            $this->info("Konto {$email} angelegt.");
        }

        $user->syncRoles([Permissions::ADMIN_ROLE]);
        $user->forceFill(['is_active' => true])->save();
        $this->info("{$email} ist Administrator.");

        return self::SUCCESS;
    }
}
