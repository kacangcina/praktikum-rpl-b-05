# Changelog

Format changelog ini mengikuti prinsip Keep a Changelog. Versi proyek menggunakan Semantic Versioning.

## [1.0.0] - 2026-06-27

### Added

- Menambahkan fitur autentikasi pengguna untuk registrasi, login, logout, dan manajemen sesi.
- Menambahkan katalog resep publik dengan detail bahan, alat, langkah memasak, tingkat kesulitan, dan estimasi waktu.
- Menambahkan fitur pencarian resep berdasarkan judul dan bahan.
- Menambahkan fitur pembuatan, pembaruan, dan penghapusan resep oleh pengguna yang berwenang.
- Menambahkan fitur koleksi pribadi untuk menyimpan dan menghapus resep favorit.
- Menambahkan fitur upload dan penayangan video kelas memasak untuk creator terverifikasi.
- Menambahkan alur pengajuan dan verifikasi creator oleh admin.
- Menambahkan dashboard admin untuk moderasi pengguna, resep, pengajuan creator, dan pengaturan AI.
- Menambahkan fitur review atau penilaian resep oleh pengguna terautentikasi.
- Menambahkan fitur konsultasi memasak berbasis AI dengan pembatasan topik seputar resep dan memasak.
- Menambahkan aplikasi mobile Android sebagai klien pendukung Cookbook.
- Menambahkan unit test minimal dengan pola Arrange-Act-Assert untuk kebutuhan praktikum P10.

### Changed

- Menyusun ulang dokumentasi proyek agar mencakup deskripsi, fitur, screenshot, instalasi, cara menjalankan, dan pengujian.
- Menyesuaikan pesan error konsultasi AI agar lebih ramah saat layanan pihak ketiga sedang sibuk.

### Fixed

- Memperbaiki validasi akses agar hanya creator terverifikasi yang dapat mengunggah video.
- Memperbaiki alur penyimpanan resep agar koleksi pengguna tidak menyimpan data duplikat.
- Memperbaiki handling error konsultasi AI ketika Gemini API mengembalikan status 503.
- Memperbaiki dokumentasi instalasi agar mencakup migration, seeding, storage link, dan Vite dev server.

### Removed

- Tidak ada fitur utama yang dihapus pada rilis pertama.
