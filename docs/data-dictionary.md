# Data Dictionary 

## Tabel: users

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik pengguna |
| username | VARCHAR(100) | NOT NULL | Nama pengguna untuk ditampilkan di platform |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email untuk login dan verifikasi akun |
| password | VARCHAR(255) | NOT NULL | Password yang telah di-hash menggunakan bcrypt |
| role | ENUM('guest','user','creator','admin') | NOT NULL, DEFAULT 'user' | Peran pengguna dalam sistem |
| is_verified | BOOLEAN | NOT NULL, DEFAULT FALSE | Status verifikasi email pengguna |
| created_at | TIMESTAMP | NOT NULL, DEFAULT NOW() | Waktu akun dibuat |
| updated_at | TIMESTAMP | NOT NULL, DEFAULT NOW() | Waktu data terakhir diperbarui |

---

## Tabel: recipes

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik resep |
| user_id | INT | FK → users.id, NOT NULL | ID pengguna yang membuat resep |
| title | VARCHAR(255) | NOT NULL | Judul resep |
| difficulty | ENUM('mudah','sedang','sulit') | NOT NULL | Tingkat kesulitan resep |
| estimated_time | INT | NOT NULL | Estimasi waktu memasak dalam menit |
| thumbnail | VARCHAR(500) | NULL | Path atau URL foto thumbnail resep |
| created_at | TIMESTAMP | NOT NULL, DEFAULT NOW() | Waktu resep dipublikasikan |
| updated_at | TIMESTAMP | NOT NULL, DEFAULT NOW() | Waktu data terakhir diperbarui |

---

## Tabel: recipe_tools

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik alat masak |
| recipe_id | INT | FK → recipes.id, NOT NULL | ID resep yang menggunakan alat ini |
| tool_name | VARCHAR(100) | NOT NULL | Nama alat masak yang dibutuhkan |

---

## Tabel: recipe_ingredients

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik bahan masakan |
| recipe_id | INT | FK → recipes.id, NOT NULL | ID resep yang menggunakan bahan ini |
| ingredient_name | VARCHAR(150) | NOT NULL | Nama bahan masakan |
| quantity | VARCHAR(100) | NOT NULL | Jumlah atau takaran bahan (contoh: 200 gram, 2 sdm) |

---

## Tabel: recipe_steps

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik langkah masak |
| recipe_id | INT | FK → recipes.id, NOT NULL | ID resep yang memiliki langkah ini |
| step_number | INT | NOT NULL | Urutan langkah memasak |
| description | TEXT | NOT NULL | Deskripsi detail langkah memasak |

---

## Tabel: collections

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik koleksi |
| user_id | INT | FK → users.id, NOT NULL | ID pengguna pemilik koleksi |
| name | VARCHAR(150) | NOT NULL | Nama koleksi pribadi |
| created_at | TIMESTAMP | NOT NULL, DEFAULT NOW() | Waktu koleksi dibuat |

---

## Tabel: collection_items

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik item koleksi |
| collection_id | INT | FK → collections.id, NOT NULL | ID koleksi tempat resep disimpan |
| recipe_id | INT | FK → recipes.id, NOT NULL | ID resep yang disimpan |
| saved_at | TIMESTAMP | NOT NULL, DEFAULT NOW() | Waktu resep disimpan ke koleksi |

> **Catatan:** Kombinasi (collection_id, recipe_id) harus UNIQUE untuk mencegah duplikasi resep dalam koleksi yang sama.

---

## Tabel: videos

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik video |
| user_id | INT | FK → users.id, NOT NULL | ID creator yang mengunggah video |
| title | VARCHAR(255) | NOT NULL | Judul video kelas memasak |
| description | TEXT | NULL | Deskripsi konten video |
| difficulty | ENUM('mudah','sedang','sulit') | NOT NULL | Tingkat kesulitan materi dalam video |
| file_path | VARCHAR(500) | NOT NULL | Path penyimpanan file video (format MP4, maks 500MB) |
| created_at | TIMESTAMP | NOT NULL, DEFAULT NOW() | Waktu video diunggah |

---

## Tabel: comments

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik komentar |
| user_id | INT | FK → users.id, NOT NULL | ID pengguna yang menulis komentar |
| recipe_id | INT | FK → recipes.id, NOT NULL | ID resep yang dikomentari |
| content | TEXT | NOT NULL | Isi komentar |
| created_at | TIMESTAMP | NOT NULL, DEFAULT NOW() | Waktu komentar ditulis |

---

## Tabel: ratings

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik rating |
| user_id | INT | FK → users.id, NOT NULL | ID pengguna yang memberi rating |
| recipe_id | INT | FK → recipes.id, NOT NULL | ID resep yang diberi rating |
| type | ENUM('upvote','downvote') | NOT NULL | Jenis penilaian yang diberikan |
| created_at | TIMESTAMP | NOT NULL, DEFAULT NOW() | Waktu rating diberikan |

> **Catatan:** Kombinasi (user_id, recipe_id) harus UNIQUE agar satu pengguna hanya dapat memberi satu rating per resep.

---

## Tabel: creator_verifications

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | INT | PK, AUTO_INCREMENT | ID unik pengajuan verifikasi |
| user_id | INT | FK → users.id, NOT NULL | ID pengguna yang mengajukan verifikasi sebagai creator |
| document_path | VARCHAR(500) | NOT NULL | Path file dokumen pendukung (KTP, portofolio) |
| status | ENUM('pending','approved','rejected') | NOT NULL, DEFAULT 'pending' | Status pengajuan verifikasi |
| rejection_reason | TEXT | NULL | Alasan penolakan dari admin (diisi jika status rejected) |
| submitted_at | TIMESTAMP | NOT NULL, DEFAULT NOW() | Waktu pengajuan verifikasi dikirimkan |
| reviewed_at | TIMESTAMP | NULL | Waktu admin meninjau pengajuan |

---
