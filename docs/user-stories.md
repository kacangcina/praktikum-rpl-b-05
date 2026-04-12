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

---


