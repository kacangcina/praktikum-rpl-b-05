# User Stories & Acceptance Criteria

## Identifikasi Role / Aktor
1. **User**: Pengguna terdaftar yang menggunakan fitur AI dan mengelola resep pribadi[cite: 252].
2. **Creator**: Pengguna yang berkontribusi mengunggah resep berkualitas untuk komunitas.
3. **Admin**: Pengelola sistem yang memoderasi konten resep agar tetap sesuai standar[cite: 250].

---

## Daftar User Story

### US-01 — Generate Resep dengan AI Gemini (Fitur Utama)
**As a User**, I want to **input my available ingredients**, so that **Gemini AI can generate a unique recipe for me**.
- **AC-01**: Given saya berada di halaman "AI Cook", When saya memasukkan bahan "Telur, Tomat" dan klik "Generate", Then sistem menampilkan resep buatan AI yang mencakup takaran dan langkah memasak[cite: 29, 277].
- **AC-01 (Edge Case)**: Given input bahan kosong, When tombol "Generate" diklik, Then sistem menampilkan pesan error "Mohon masukkan minimal satu bahan"[cite: 50, 403].

### US-02 — Upload Resep Manual
**As a User**, I want to **manually upload my own recipe**, so that **I can share my cooking techniques with the community**.
- **AC-02**: Given saya berada di halaman "Tambah Resep", When saya mengisi semua field wajib dan klik "Publish", Then resep tersimpan dengan status "Pending Review"[cite: 277].

### US-03 — Mencari Resep Komunitas
**As a User**, I want to **search for recipes by name**, so that **I can find specific dishes quickly**.
- **AC-03**: Given saya di halaman utama, When saya mengetik "Opor Ayam", Then sistem menampilkan daftar resep yang relevan[cite: 277].

### US-04 — Menyimpan Favorit (Bookmark)
**As a User**, I want to **bookmark a recipe**, so that **I can access it later without searching**.
- **AC-04**: Given saya membuka detail resep, When saya menekan ikon "Bookmark", Then resep tersebut muncul di halaman "Koleksi Saya"[cite: 277].

### US-05 — Memberikan Rating
**As a User**, I want to **give a star rating to a recipe**, so that **other users know the quality of the dish**.
- **AC-05**: Given saya sudah login, When saya memilih 5 bintang pada resep, Then rating rata-rata resep diperbarui secara otomatis[cite: 277].

### US-06 — Update Resep Pribadi
**As a Creator**, I want to **edit my previously published recipes**, so that **I can fix errors in the cooking steps**.
- **AC-06**: Given saya di halaman profil, When saya mengubah instruksi pada resep milik sendiri, Then sistem menyimpan perubahan tersebut[cite: 277].

### US-07 — Moderasi Resep (Admin)
**As an Admin**, I want to **approve or reject user recipes**, so that **the platform content remains high quality**.
- **AC-07**: Given saya di dashboard admin, When saya klik "Approve" pada resep pending, Then resep tersebut tampil di publik[cite: 277].

### US-08 — Pendaftaran Akun
**As a Guest**, I want to **register for an account**, so that **I can access AI generation features**.
- **AC-08**: Given saya di halaman registrasi, When saya mengisi email dan password valid, Then akun berhasil dibuat[cite: 277].