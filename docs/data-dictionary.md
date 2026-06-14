# Data Dictionary CuBu

## Notasi Constraint

| Notasi | Arti |
|---|---|
| PK | Primary key |
| FK | Foreign key |
| AI | Auto increment |
| NN | Not null |
| UQ | Unique |
| NULL | Nilai boleh kosong |

## Tabel `users`

Menyimpan akun, profil, peran, serta status pengguna.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik pengguna |
| username | VARCHAR(100) | NN, UQ | Nama pengguna unik yang ditampilkan pada aplikasi |
| name | VARCHAR(255) | NN | Nama lengkap pengguna |
| email | VARCHAR(255) | NN, UQ | Alamat email untuk autentikasi |
| email_verified_at | TIMESTAMP | NULL | Waktu verifikasi email |
| password | VARCHAR(255) | NN | Kata sandi yang telah di-hash |
| role | ENUM | NN, DEFAULT `user` | Peran pengguna: `guest`, `user`, `creator`, atau `admin` |
| is_verified | BOOLEAN | NN, DEFAULT `false` | Status verifikasi creator |
| suspended_at | TIMESTAMP | NULL, INDEX | Waktu akun ditangguhkan oleh admin |
| suspension_reason | TEXT | NULL | Alasan penangguhan akun |
| closed_at | TIMESTAMP | NULL, INDEX | Waktu akun ditutup |
| closure_reason | TEXT | NULL | Alasan penutupan akun |
| avatar | VARCHAR(500) | NULL | Path file foto profil |
| bio | TEXT | NULL | Deskripsi singkat profil |
| remember_token | VARCHAR(100) | NULL | Token fitur remember me |
| created_at | TIMESTAMP | NULL | Waktu akun dibuat |
| updated_at | TIMESTAMP | NULL | Waktu akun terakhir diperbarui |

## Tabel `recipes`

Menyimpan informasi utama resep yang dibuat pengguna.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik resep |
| user_id | BIGINT UNSIGNED | FK, NN | Pembuat resep, mengacu ke `users.id` |
| title | VARCHAR(255) | NN | Judul resep |
| description | TEXT | NULL | Deskripsi resep |
| difficulty | ENUM | NN, DEFAULT `mudah` | Tingkat kesulitan: `mudah`, `sedang`, atau `sulit` |
| estimated_time | SMALLINT UNSIGNED | NN | Estimasi waktu memasak dalam menit |
| thumbnail | VARCHAR(500) | NULL | Path lokal atau URL thumbnail resep |
| published_at | TIMESTAMP | NULL | Waktu resep dipublikasikan |
| moderation_status | VARCHAR(20) | NN, DEFAULT `published` | Status moderasi resep |
| moderation_reason | TEXT | NULL | Alasan tindakan moderasi |
| moderated_at | TIMESTAMP | NULL | Waktu resep dimoderasi |
| moderated_by | BIGINT UNSIGNED | FK, NULL | Admin yang melakukan moderasi, mengacu ke `users.id` |
| created_at | TIMESTAMP | NULL | Waktu data dibuat |
| updated_at | TIMESTAMP | NULL | Waktu data diperbarui |

## Tabel `recipe_tools`

Menyimpan daftar alat yang dibutuhkan setiap resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik alat resep |
| recipe_id | BIGINT UNSIGNED | FK, NN | Resep pemilik alat, mengacu ke `recipes.id` |
| tool_name | VARCHAR(100) | NN | Nama alat masak |
| created_at | TIMESTAMP | NULL | Waktu data dibuat |
| updated_at | TIMESTAMP | NULL | Waktu data diperbarui |

## Tabel `recipe_ingredients`

Menyimpan bahan dan takaran setiap resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik bahan resep |
| recipe_id | BIGINT UNSIGNED | FK, NN | Resep pemilik bahan, mengacu ke `recipes.id` |
| ingredient_name | VARCHAR(150) | NN | Nama bahan |
| quantity | VARCHAR(100) | NN | Jumlah atau takaran bahan |
| created_at | TIMESTAMP | NULL | Waktu data dibuat |
| updated_at | TIMESTAMP | NULL | Waktu data diperbarui |

## Tabel `recipe_steps`

Menyimpan urutan langkah memasak setiap resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik langkah |
| recipe_id | BIGINT UNSIGNED | FK, NN | Resep pemilik langkah, mengacu ke `recipes.id` |
| step_number | SMALLINT UNSIGNED | NN | Nomor urut langkah |
| title | VARCHAR(255) | NULL | Judul singkat langkah |
| description | TEXT | NN | Penjelasan langkah memasak |
| created_at | TIMESTAMP | NULL | Waktu data dibuat |
| updated_at | TIMESTAMP | NULL | Waktu data diperbarui |

## Tabel `collections`

Menyimpan koleksi resep milik pengguna.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik koleksi |
| user_id | BIGINT UNSIGNED | FK, NN | Pemilik koleksi, mengacu ke `users.id` |
| name | VARCHAR(150) | NN | Nama koleksi |
| created_at | TIMESTAMP | NULL | Waktu koleksi dibuat |
| updated_at | TIMESTAMP | NULL | Waktu koleksi diperbarui |

## Tabel `collection_items`

Tabel penghubung antara koleksi dan resep yang disimpan.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik item koleksi |
| collection_id | BIGINT UNSIGNED | FK, NN | Koleksi tujuan, mengacu ke `collections.id` |
| recipe_id | BIGINT UNSIGNED | FK, NN | Resep yang disimpan, mengacu ke `recipes.id` |
| saved_at | TIMESTAMP | NN, DEFAULT CURRENT_TIMESTAMP | Waktu resep disimpan |
| created_at | TIMESTAMP | NULL | Waktu data dibuat |
| updated_at | TIMESTAMP | NULL | Waktu data diperbarui |

## Tabel `videos`

Menyimpan video memasak yang diunggah creator dan dapat dikaitkan dengan resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik video |
| user_id | BIGINT UNSIGNED | FK, NN | Creator pengunggah, mengacu ke `users.id` |
| recipe_id | BIGINT UNSIGNED | FK, NULL, UQ | Resep terkait, mengacu ke `recipes.id` |
| title | VARCHAR(255) | NN | Judul video |
| description | TEXT | NULL | Deskripsi isi video |
| difficulty | ENUM | NN | Tingkat kesulitan: `mudah`, `sedang`, atau `sulit` |
| file_path | VARCHAR(500) | NN | Path file video MP4 |
| created_at | TIMESTAMP | NULL | Waktu video dibuat |
| updated_at | TIMESTAMP | NULL | Waktu video diperbarui |

## Tabel `creator_verifications`

Menyimpan pengajuan dan hasil verifikasi pengguna menjadi creator.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik pengajuan |
| user_id | BIGINT UNSIGNED | FK, NN | Pengaju verifikasi, mengacu ke `users.id` |
| document_path | VARCHAR(500) | NN | Path dokumen pendukung |
| portfolio_url | TEXT | NULL | URL portofolio pengaju |
| notes | TEXT | NULL | Catatan tambahan dari pengaju |
| status | ENUM | NN, DEFAULT `pending` | Status: `pending`, `approved`, atau `rejected` |
| rejection_reason | TEXT | NULL | Alasan penolakan pengajuan |
| submitted_at | TIMESTAMP | NN, DEFAULT CURRENT_TIMESTAMP | Waktu pengajuan dikirim |
| reviewed_at | TIMESTAMP | NULL | Waktu pengajuan diperiksa |
| reviewed_by | BIGINT UNSIGNED | FK, NULL | Admin pemeriksa, mengacu ke `users.id` |
| created_at | TIMESTAMP | NULL | Waktu data dibuat |
| updated_at | TIMESTAMP | NULL | Waktu data diperbarui |

## Tabel `recipe_reviews`

Menyimpan rating bintang dan ulasan pengguna terhadap resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik ulasan |
| recipe_id | BIGINT UNSIGNED | FK, NN | Resep yang diulas, mengacu ke `recipes.id` |
| user_id | BIGINT UNSIGNED | FK, NN | Penulis ulasan, mengacu ke `users.id` |
| rating | TINYINT UNSIGNED | NN | Nilai rating bintang |
| comment | TEXT | NN | Isi komentar ulasan |
| created_at | TIMESTAMP | NULL | Waktu ulasan dibuat |
| updated_at | TIMESTAMP | NULL | Waktu ulasan diperbarui |

## Tabel `comments`

Menyimpan komentar pada resep untuk fitur interaksi sederhana.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik komentar |
| user_id | BIGINT UNSIGNED | FK, NN | Penulis komentar, mengacu ke `users.id` |
| recipe_id | BIGINT UNSIGNED | FK, NN | Resep yang dikomentari, mengacu ke `recipes.id` |
| content | TEXT | NN | Isi komentar |
| created_at | TIMESTAMP | NN, DEFAULT CURRENT_TIMESTAMP | Waktu komentar dibuat |

## Tabel `ratings`

Menyimpan penilaian upvote atau downvote pada resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik rating |
| user_id | BIGINT UNSIGNED | FK, NN | Pemberi rating, mengacu ke `users.id` |
| recipe_id | BIGINT UNSIGNED | FK, NN | Resep yang dinilai, mengacu ke `recipes.id` |
| type | ENUM | NN | Jenis rating: `upvote` atau `downvote` |
| created_at | TIMESTAMP | NN, DEFAULT CURRENT_TIMESTAMP | Waktu rating diberikan |

## Tabel `notifications`

Menyimpan notifikasi Laravel untuk pengguna.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | CHAR(36) / UUID | PK | ID unik notifikasi |
| type | VARCHAR(255) | NN | Class atau jenis notifikasi |
| notifiable_type | VARCHAR(255) | NN, INDEX | Tipe model penerima |
| notifiable_id | BIGINT UNSIGNED | NN, INDEX | ID model penerima |
| data | TEXT | NN | Payload notifikasi dalam format JSON |
| read_at | TIMESTAMP | NULL | Waktu notifikasi dibaca |
| created_at | TIMESTAMP | NULL | Waktu notifikasi dibuat |
| updated_at | TIMESTAMP | NULL | Waktu notifikasi diperbarui |

## Tabel `system_settings`

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID unik pengaturan |
| key | VARCHAR(255) | NN, UQ | Nama unik pengaturan |
| value | LONGTEXT | NULL | Nilai pengaturan |
| updated_by | BIGINT UNSIGNED | FK, NULL | Admin terakhir yang mengubah, mengacu ke `users.id` |
| created_at | TIMESTAMP | NULL | Waktu pengaturan dibuat |
| updated_at | TIMESTAMP | NULL | Waktu pengaturan diperbarui |

## Relasi Utama

| Tabel Induk | Relasi | Tabel Anak |
|---|---|---|
| `users` | 1:N | `recipes` |
| `users` | 1:N | `collections` |
| `users` | 1:N | `videos` |
| `users` | 1:N | `creator_verifications` |
| `users` | 1:N | `recipe_reviews` |
| `recipes` | 1:N | `recipe_tools` |
| `recipes` | 1:N | `recipe_ingredients` |
| `recipes` | 1:N | `recipe_steps` |
| `recipes` | 1:0..1 | `videos` |
| `recipes` | 1:N | `recipe_reviews` |
| `collections` | N:M | `recipes` melalui `collection_items` |

## Tabel Pendukung Framework

| Tabel | Fungsi |
|---|---|
| `password_reset_tokens` | Menyimpan token reset kata sandi |
| `sessions` | Menyimpan sesi login berbasis database |
| `personal_access_tokens` | Menyimpan token autentikasi Laravel Sanctum |
| `cache` dan `cache_locks` | Menyimpan cache dan lock aplikasi |
| `jobs`, `job_batches`, dan `failed_jobs` | Mendukung antrean pekerjaan Laravel |
