# First Install

1. Create PostgreSQL with database pspa_scm
2. Copy .env.example to .env, and change DB
3. `composer update & composer install`
4. `php artisan migrate` to generate default User table
5. `php artisan db:seed` to seed default value
6. `php artisan key:generate` generate application key
7. `npm update & npm install` and make sure nodejs has been installed
8. `php artisan serve` to check if routing to Laravel default web page
9. `npm run dev` in separate terminal

# Add Submodule

1. Add Submodule `git submodule add https://eoads@dev.azure.com/eoads/pspa-scm/_git/pspa-scm-material-proc Modules/MaterialProc`
2. Run `composer dump-autoload`
3. Run `php artisan optimize:clear`
4. Ensure module is enabled `php artisan module:list`
5. Migrate module `php artisan module:migrate MaterialProc`
6. Seed module `php artisan module:seed MaterialProc`
7. Refresh Submodule `git submodule update --remote –merge`

---

### LDAP Login

1. Configure in _auth.php_ guards and providers
2. Test LDAP `php artisan ldap:test`
3. Import users `php artisan ldap:import ldap_users ` based on auth.php 'providers'

### XAMPP

Configure php.ini

1. Open XAMPP
2. Go to Apache > Config > php.ini
3. Remove semi-colon from extension=ldap, extension=gd, extension=zip, extension=pdo_pgsql, extension=pgsql

### Unit Test

Run unit tests in main module, but only specific file

`php artisan test Tests/Unit/PlantRepositoryTest.php`

Run unit tests for specific module

`php artisan test Modules/MaterialProc/Tests`

Run tests with coverage
`php artisan test --coverage-html=coverage`
`start coverage/index.html`
