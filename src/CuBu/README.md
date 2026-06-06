<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Ikon UI

Proyek memakai [Lucide Icons](https://lucide.dev/) dari dependency npm `lucide`.

1. Tambahkan nama ikon dan import-nya di `resources/js/app.js`.
2. Pakai ikon di Blade dengan format `<i data-lucide="nama-icon"></i>`.
3. Jalankan `npm install` setelah pull perubahan dependency, lalu `npm run dev` atau `npm run build`.

Dengan pola ini, tim tidak perlu mengirim atau menyimpan file SVG ikon satu per satu.

## Akun Development

Jalankan `php artisan db:seed`, lalu gunakan akun admin berikut untuk menguji dashboard verifikasi:

- Email: `admin@cubu.test`
- Password: `password`

Akun ini hanya untuk lingkungan development.

## Database Testing

PHPUnit memakai database `cubu_testing`, terpisah dari database aplikasi `cubu`.
Jangan mengubah `DB_DATABASE` pada `phpunit.xml` menjadi `cubu`, karena test dengan
`RefreshDatabase` akan mengosongkan data aplikasi dan akun development.

## Konfigurasi Upload Video

Video creator dibatasi maksimal 500 MB. Sesuaikan `php.ini` yang dipakai server:

```ini
upload_max_filesize = 512M
post_max_size = 520M
max_execution_time = 300
max_input_time = 300
```

Restart Apache atau server PHP setelah mengubah konfigurasi. Cek konfigurasi aktif dengan:

```powershell
php -r "echo ini_get('upload_max_filesize').PHP_EOL.ini_get('post_max_size');"
```
