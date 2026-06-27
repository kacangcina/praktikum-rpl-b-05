# AI-Usage Log

Dokumen ini mencatat penggunaan alat bantu AI selama pengerjaan proyek. Seluruh output AI harus diverifikasi, disesuaikan dengan konteks proyek, dan tidak boleh memuat credential, API key, password, token, atau data sensitif lain.

| No | Tanggal | Anggota | Tool AI | Ringkasan Prompt | Ringkasan Output | Modifikasi/Verifikasi |
|---:|---|---|---|---|---|---|
| 1 | 2026-06-27 | Naufal Farrell Budianto | OpenAI Codex | Meminta bantuan menyusun unit test minimal P10 dengan pola Arrange-Act-Assert. | Draft unit test untuk logika role pengguna dan parsing response service konsultasi AI. | Test disesuaikan dengan struktur Laravel proyek dan diverifikasi dengan PHPUnit: 8 tests, 13 assertions, semua pass. |
| 2 | 2026-06-27 | Naufal Farrell Budianto | OpenAI Codex | Meminta diagnosis mengapa fitur konsultasi AI tidak dapat digunakan. | Analisis log Laravel menunjukkan Gemini API mengembalikan status 503 karena model sedang high demand. | Konfigurasi `.env` dicek tanpa membuka credential. Error handling service diperbaiki agar response 503 menjadi pesan yang lebih ramah. |
| 3 | 2026-06-27 | Naufal Farrell Budianto | OpenAI Codex | Meminta bantuan pengerjaan artefak P11: README final, changelog, screenshot, dan AI log. | Draft README final, CHANGELOG v1.0.0, screenshot halaman home, dan AI-Usage Log. | Isi dokumentasi diverifikasi terhadap struktur repo, SRS, route Laravel, dependency web/mobile, dan screenshot lokal aplikasi. |

## Refleksi Singkat

Penggunaan AI membantu mempercepat penyusunan dokumentasi, identifikasi error, dan pembuatan test. Namun, hasil AI tetap perlu diperiksa karena beberapa detail harus disesuaikan dengan struktur folder, versi dependency, konfigurasi lokal, dan fitur yang benar-benar tersedia di repositori.

Tim tidak memasukkan credential, API key, password, token, atau isi lengkap file `.env` ke prompt AI. Setiap perubahan yang dihasilkan dengan bantuan AI wajib tetap direview oleh anggota tim sebelum di-commit atau dipublikasikan.
