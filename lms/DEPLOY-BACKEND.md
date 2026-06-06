# DSA LMS API — deploy

1. Upload **`lms-api-deploy.zip`** and extract.
2. Document root → **`lms-api/public/`**
3. cPanel → **MultiPHP Manager** → set domain to **PHP 8.4**
4. Open **`https://YOUR-DOMAIN/setup.php`**

Requires **PHP 8.4.1+** (Laravel 13 / Symfony 8).

Recreate zip: `./package-backend-deploy.sh`
