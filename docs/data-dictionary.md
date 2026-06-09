# Data Dictionary CuBu

Dokumen ini disusun berdasarkan data dictionary resmi dan ERD CuBu.

## Tabel `users`

Menyimpan data akun pengguna.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik pengguna |
| username | VARCHAR(100) | UNIQUE, NOT NULL | Nama pengguna yang ditampilkan pada platform |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email untuk login dan verifikasi akun |
| password | VARCHAR(255) | NOT NULL | Password yang telah di-hash |
| role | ENUM('guest','user','creator','admin') | NOT NULL, DEFAULT 'user' | Peran pengguna dalam sistem |
| is_verified | BOOLEAN | NOT NULL, DEFAULT FALSE | Status verifikasi pengguna sebagai creator |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu akun dibuat |
| updated_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu akun terakhir diperbarui |

## Tabel `recipes`

Menyimpan informasi utama resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik resep |
| user_id | INT | FK -> users.id, NOT NULL | Pengguna yang membuat resep |
| title | VARCHAR(255) | NOT NULL | Judul resep |
| difficulty | ENUM('mudah','sedang','sulit') | NOT NULL | Tingkat kesulitan resep |
| estimated_time | INT | NOT NULL | Estimasi waktu memasak dalam menit |
| thumbnail | VARCHAR(500) | NULL | Path atau URL thumbnail resep |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu resep dibuat atau dipublikasikan |
| updated_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu resep terakhir diperbarui |

## Tabel `recipe_tools`

Menyimpan daftar alat yang diperlukan oleh suatu resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik alat resep |
| recipe_id | INT | FK -> recipes.id, NOT NULL | Resep yang menggunakan alat |
| tool_name | VARCHAR(100) | NOT NULL | Nama alat masak |

## Tabel `recipe_ingredients`

Menyimpan bahan dan takaran yang diperlukan oleh suatu resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik bahan resep |
| recipe_id | INT | FK -> recipes.id, NOT NULL | Resep yang menggunakan bahan |
| ingredient_name | VARCHAR(150) | NOT NULL | Nama bahan masakan |
| quantity | VARCHAR(100) | NOT NULL | Jumlah atau takaran bahan |

## Tabel `recipe_steps`

Menyimpan urutan langkah memasak suatu resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik langkah memasak |
| recipe_id | INT | FK -> recipes.id, NOT NULL | Resep yang memiliki langkah |
| step_number | INT | NOT NULL | Nomor urut langkah |
| description | TEXT | NOT NULL | Deskripsi langkah memasak |

## Tabel `collections`

Menyimpan koleksi resep milik pengguna.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik koleksi |
| user_id | INT | FK -> users.id, NOT NULL | Pengguna pemilik koleksi |
| name | VARCHAR(150) | NOT NULL | Nama koleksi |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu koleksi dibuat |

## Tabel `collection_items`

Menyimpan resep yang dimasukkan ke dalam koleksi.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik item koleksi |
| collection_id | INT | FK -> collections.id, NOT NULL | Koleksi tempat resep disimpan |
| recipe_id | INT | FK -> recipes.id, NOT NULL | Resep yang disimpan |
| saved_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu resep disimpan |

Constraint unik:

`UNIQUE(collection_id, recipe_id)`

## Tabel `videos`

Menyimpan video kelas memasak yang diunggah oleh creator.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik video |
| user_id | INT | FK -> users.id, NOT NULL | Creator yang mengunggah video |
| title | VARCHAR(255) | NOT NULL | Judul video kelas memasak |
| description | TEXT | NULL | Deskripsi konten video |
| difficulty | ENUM('mudah','sedang','sulit') | NOT NULL | Tingkat kesulitan materi |
| file_path | VARCHAR(500) | NOT NULL | Path file video berformat MP4 |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu video diunggah |

Ukuran file video dibatasi maksimal 500 MB pada validasi aplikasi.

## Tabel `comments`

Menyimpan komentar pengguna pada resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik komentar |
| user_id | INT | FK -> users.id, NOT NULL | Pengguna yang menulis komentar |
| recipe_id | INT | FK -> recipes.id, NOT NULL | Resep yang dikomentari |
| content | TEXT | NOT NULL | Isi komentar |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu komentar ditulis |

## Tabel `ratings`

Menyimpan penilaian pengguna terhadap resep.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik rating |
| user_id | INT | FK -> users.id, NOT NULL | Pengguna yang memberikan rating |
| recipe_id | INT | FK -> recipes.id, NOT NULL | Resep yang diberi rating |
| type | ENUM('upvote','downvote') | NOT NULL | Jenis penilaian |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu rating diberikan |

Constraint unik:

`UNIQUE(user_id, recipe_id)`

## Tabel `creator_verifications`

Menyimpan pengajuan verifikasi pengguna menjadi creator.

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik pengajuan verifikasi |
| user_id | INT | FK -> users.id, NOT NULL | Pengguna yang mengajukan verifikasi |
| document_path | VARCHAR(500) | NOT NULL | Path dokumen pendukung |
| status | ENUM('pending','approved','rejected') | NOT NULL, DEFAULT 'pending' | Status pengajuan |
| rejection_reason | TEXT | NULL | Alasan penolakan |
| submitted_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu pengajuan dikirim |
| reviewed_at | TIMESTAMP | NULL | Waktu pengajuan ditinjau admin |

## Ringkasan Relasi

| Entitas Asal | Relasi | Entitas Tujuan |
|---|---|---|
| users | 1:N | recipes |
| users | 1:N | videos |
| users | 1:N | collections |
| users | 1:N | comments |
| users | 1:N | ratings |
| users | 1:N | creator_verifications |
| recipes | 1:N | recipe_tools |
| recipes | 1:N | recipe_ingredients |
| recipes | 1:N | recipe_steps |
| recipes | 1:N | comments |
| recipes | 1:N | ratings |
| collections | 1:N | collection_items |
| recipes | 1:N | collection_items |

