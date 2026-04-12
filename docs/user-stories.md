# User Stories & Acceptance Criteria

## Identifikasi Role / Aktor

| No | Role | Deskripsi |
|---|---|---|
| 1 | **Guest** | Pengguna yang belum login; dapat menjelajah resep dan melihat konten detail seperti User, namun tidak bisa melakukan aksi interaksi (simpan, komentar, konsultasi, atau posting). |
| 2 | **User** | Pengguna terdaftar; dapat mengakses fitur simpan resep, menonton video kelas, menggunakan AI, serta mempublikasikan resep pribadi secara langsung. |
| 3 | **Creator** | Chef terverifikasi; dapat mempublikasikan resep profesional dan mengunggah konten video kelas memasak tanpa melalui moderasi konten. |
| 4 | **Admin** | Pengelola platform; memiliki otoritas khusus untuk memverifikasi akun creator untuk menjaga kualitas pengajar di platform. |

---

## User Stories & Acceptance Criteria

### US-01 — Menjelajah Resep Tanpa Akun
**Role:** Guest  
**User Story:** *As a Guest, I want to browse and view the recipe list just like a registered user, so that I can see the available content before deciding to register.*

**Acceptance Criteria:**
- **AC-01:** Given saya belum login dan membuka halaman utama, When halaman dimuat, Then sistem menampilkan daftar resep lengkap dengan judul, foto, tingkat kesulitan, alat masak, bahan, estimasi waktu, dan langkah masak.
- **AC-01 (interaction block):** Given saya belum login dan mencoba menekan tombol "Simpan ke Koleksi" atau "Konsultasi AI", When tombol tersebut ditekan, Then sistem menampilkan pesan "Silakan daftar atau masuk untuk menggunakan fitur ini."

---

### US-02 — Mendaftar Akun Baru
**Role:** Guest  
**User Story:** *As a Guest, I want to register for a new account using my email and password, so that I can access interactive features like saving recipes and AI consultation.*

**Acceptance Criteria:**
- **AC-02:** Given saya berada di halaman registrasi dan mengisi email serta password valid, When saya menekan tombol "Daftar", Then sistem membuat akun baru dan mengarahkan saya ke halaman utama.

---

### US-03 — Mencari Resep
**Role:** User  
**User Story:** *As a User, I want to search for recipes based on the dish name or ingredients, so that I can find what I want to cook quickly.*

**Acceptance Criteria:**
- **AC-03:** Given saya berada di kolom pencarian, When saya mengetikkan nama masakan atau bahan tertentu, Then sistem menampilkan daftar resep yang relevan dengan kata kunci tersebut.

---

### US-04 — Menyimpan Resep ke Koleksi Pribadi
**Role:** User  
**User Story:** *As a User, I want to save recipes to my personal collection, so that I can easily find my favorite recipes again.*

**Acceptance Criteria:**
- **AC-04:** Given saya membuka halaman detail resep, When saya menekan tombol "Simpan ke Koleksi", Then resep tersebut otomatis tersimpan di daftar koleksi pribadi saya.

---

### US-05 — Menonton Video Kelas Memasak
**Role:** User  
**User Story:** *As a User, I want to watch cooking video classes hosted by chefs, so that I can learn cooking techniques visually.*

**Acceptance Criteria:**
- **AC-05:** Given saya membuka halaman kelas video, When saya memilih salah satu video, Then sistem memutar video tersebut dan menampilkan langkah-langkah pendukung di bawah video.

---

### US-06 — Membuat Resep Sendiri (User)
**Role:** User  
**User Story:** *As a User, I want to create and publish my own recipes, so that I can share my cooking results with others.*

**Acceptance Criteria:**
- **AC-06:** Given saya membuka halaman "Buat Resep", When saya mengisi judul, alat masak, bahan, estimasi waktu, serta langkah masak dan menekan "Publikasikan", Then resep langsung terbit di platform dan dapat dilihat oleh pengguna lain tanpa moderasi.

---

### US-07 — Mempublikasikan Resep Profesional (Creator)
**Role:** Creator  
**User Story:** *As a Creator, I want to publish complete recipes with professional details, so that users can follow my instructions accurately.*

**Acceptance Criteria:**
- **AC-07:** Given saya login sebagai Creator, When saya mengisi formulir resep (judul, alat, bahan, estimasi waktu, langkah) dan menekan "Publikasikan", Then resep langsung tersedia di profil saya dengan label "Verified Creator" tanpa melalui moderasi.

---

### US-08 — Mengunggah Video Kelas Memasak (Creator)
**Role:** Creator  
**User Story:** *As a Creator, I want to upload cooking video classes, so that I can provide more in-depth learning for the users.*

**Acceptance Criteria:**
- **AC-08:** Given saya berada di dashboard Creator, When saya mengunggah file video dan mengisi deskripsi, lalu menekan "Upload", Then video tersebut muncul di daftar kelas dan bisa diakses oleh pengguna.

---

### US-09 — Konsultasi Masalah Memasak dengan AI
**Role:** User  
**User Story:** *As a User, I want to consult with an AI assistant regarding cooking issues, so that I can fix my mistakes immediately.*

**Acceptance Criteria:**
- **AC-09:** Given saya membuka fitur Chat AI, When saya menanyakan masalah seperti "kenapa masakan saya gosong?", Then AI memberikan penjelasan mengenai penyebab teknis dan cara memperbaikinya saat itu juga.

---

### US-10 — Memverifikasi Akun Creator
**Role:** Admin  
**User Story:** *As an Admin, I want to verify applicant creator accounts, so that only qualified chefs can post as Creators.*

**Acceptance Criteria:**
- **AC-10:** Given saya membuka dashboard verifikasi, When saya meninjau profil pendaftar dan menekan "Setujui", Then akun pendaftar mendapatkan status Creator dan akses untuk mengunggah video kelas.
=======
1. **User**: Pengguna terdaftar yang menggunakan fitur AI dan mengelola resep pribadi.
2. **Creator**: Pengguna yang berkontribusi mengunggah resep berkualitas untuk komunitas.
3. **Admin**: Pengelola sistem yang memoderasi konten resep agar tetap sesuai standar.

---

## Daftar User Story

### US-01 — Generate Resep dengan AI Gemini (Fitur Utama)
**As a User**, I want to **input my available ingredients**, so that **Gemini AI can generate a unique recipe for me**.
- **AC-01**: Given saya berada di halaman "AI Cook", When saya memasukkan bahan "Telur, Tomat" dan klik "Generate", Then sistem menampilkan resep buatan AI yang mencakup takaran dan langkah memasak.
- **AC-01 (Edge Case)**: Given input bahan kosong, When tombol "Generate" diklik, Then sistem menampilkan pesan error "Mohon masukkan minimal satu bahan".

### US-02 — Upload Resep Manual
**As a User**, I want to **manually upload my own recipe**, so that **I can share my cooking techniques with the community**.
- **AC-02**: Given saya berada di halaman "Tambah Resep", When saya mengisi semua field wajib dan klik "Publish", Then resep tersimpan dengan status "Pending Review".

### US-03 — Mencari Resep Komunitas
**As a User**, I want to **search for recipes by name**, so that **I can find specific dishes quickly**.
- **AC-03**: Given saya di halaman utama, When saya mengetik "Opor Ayam", Then sistem menampilkan daftar resep yang relevan.

### US-04 — Menyimpan Favorit (Bookmark)
**As a User**, I want to **bookmark a recipe**, so that **I can access it later without searching**.
- **AC-04**: Given saya membuka detail resep, When saya menekan ikon "Bookmark", Then resep tersebut muncul di halaman "Koleksi Saya".

### US-05 — Memberikan Rating
**As a User**, I want to **give a star rating to a recipe**, so that **other users know the quality of the dish**.
- **AC-05**: Given saya sudah login, When saya memilih 5 bintang pada resep, Then rating rata-rata resep diperbarui secara otomatis.

### US-06 — Update Resep Pribadi
**As a Creator**, I want to **edit my previously published recipes**, so that **I can fix errors in the cooking steps**.
- **AC-06**: Given saya di halaman profil, When saya mengubah instruksi pada resep milik sendiri, Then sistem menyimpan perubahan tersebut.

### US-07 — Moderasi Resep (Admin)
**As an Admin**, I want to **approve or reject user recipes**, so that **the platform content remains high quality**.
- **AC-07**: Given saya di dashboard admin, When saya klik "Approve" pada resep pending, Then resep tersebut tampil di publik.

### US-08 — Pendaftaran Akun
**As a Guest**, I want to **register for an account**, so that **I can access AI generation features**.
- **AC-08**: Given saya di halaman registrasi, When saya mengisi email dan password valid, Then akun berhasil dibuat.
