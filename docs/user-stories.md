# User Stories & Acceptance Criteria

## Identifikasi Role / Aktor

| No | Role | Deskripsi |
|---|---|---|
| 1 | **Guest** | Pengguna yang belum login; dapat melihat semua konten publik (daftar resep, detail resep, komentar) tetapi tidak dapat berinteraksi, tidak bisa menonton video, menyimpan, membuat resep, berkomentar, memberi rating, maupun konsultasi AI |
| 2 | **User** | Pengguna terdaftar; dapat mengakses semua fitur termasuk menonton video, menyimpan resep, membuat resep, berkomentar, memberi rating, dan konsultasi AI |
| 3 | **Creator** | Chef yang sudah terverifikasi oleh admin; dapat mempublikasikan resep dan mengunggah video kelas memasak |
| 4 | **Admin** | Pengelola platform; dapat memverifikasi akun creator dan mengelola konten platform |

---

## User Stories

---

### US-01 — Melihat Konten Resep Tanpa Akun

**Role:** Guest  
**User Story:**  
As a guest, I want to browse recipes, view recipe details, and read comments without creating an account, so that I can evaluate the app's benefits before deciding to register.

**Acceptance Criteria:**

- **AC-01:**  
  Given pengguna belum login dan membuka halaman utama aplikasi,  
  When halaman selesai dimuat,  
  Then sistem menampilkan daftar resep publik lengkap dengan judul, foto, dan tingkat kesulitan tanpa memunculkan dialog login.

- **AC-01 (edge case):**  
  Given pengguna belum login dan menekan tombol fitur interaksi (tonton video, simpan resep, komentar, rating, atau konsultasi AI),  
  When tombol tersebut ditekan,  
  Then sistem menampilkan pesan "Daftar atau masuk untuk menggunakan fitur ini" beserta tombol menuju halaman registrasi, tanpa menjalankan aksi tersebut.

---

### US-02 — Mendaftar Akun Baru

**Role:** Guest  
**User Story:**  
As a guest, I want to register a new account using my email and password, so that I can access the full interactive features of the app as a registered user.

**Acceptance Criteria:**

- **AC-02:**  
  Given pengguna berada di halaman registrasi dan mengisi email valid, kata sandi minimal 8 karakter, serta username,  
  When pengguna menekan tombol "Daftar",  
  Then sistem membuat akun baru, mengirimkan email verifikasi ke alamat yang didaftarkan, dan menampilkan pesan "Akun berhasil dibuat. Cek email kamu untuk verifikasi."

- **AC-02 (edge case):**  
  Given pengguna berada di halaman registrasi dan memasukkan email yang sudah terdaftar di sistem,  
  When pengguna menekan tombol "Daftar",  
  Then sistem menampilkan pesan "Email sudah digunakan. Silakan gunakan email lain atau masuk ke akun kamu." dan tidak membuat akun duplikat.

---

### US-03 — Mencari Resep Berdasarkan Nama atau Bahan

**Role:** User  
**User Story:**  
As a user, I want to search for recipes by dish name or ingredients I have at home, so that I can quickly find a suitable recipe without scrolling through the entire list.

**Acceptance Criteria:**

- **AC-03:**  
  Given pengguna sudah login dan berada di halaman pencarian,  
  When pengguna mengetikkan kata kunci nama masakan atau nama bahan lalu menekan tombol "Cari",  
  Then sistem menampilkan daftar resep yang relevan diurutkan berdasarkan tingkat kesesuaian kata kunci.

- **AC-03 (edge case):**  
  Given pengguna sudah login dan memasukkan kata kunci yang tidak cocok dengan resep manapun di database,  
  When pencarian dijalankan,  
  Then sistem menampilkan pesan "Tidak ada resep yang ditemukan untuk '[kata kunci]'" dan menyarankan 3 resep populer sebagai alternatif.

---

### US-04 — Menyimpan Resep ke Koleksi Pribadi

**Role:** User  
**User Story:**  
As a user, I want to save recipes to my personal collection, so that I can easily find my favorite recipes again without having to search all over.

**Acceptance Criteria:**

- **AC-04:**  
  Given pengguna sudah login dan sedang membuka halaman detail sebuah resep,  
  When pengguna menekan tombol "Simpan ke Koleksi" dan memilih nama koleksi yang sudah ada atau membuat koleksi baru,  
  Then resep tersimpan ke koleksi yang dipilih dan sistem menampilkan notifikasi "Resep berhasil disimpan ke [nama koleksi]."

- **AC-04 (edge case):**  
  Given pengguna sudah login dan mencoba menyimpan resep yang sudah ada di koleksi yang sama,  
  When tombol "Simpan ke Koleksi" ditekan dan koleksi yang sama dipilih,  
  Then sistem menampilkan pesan "Resep sudah ada di koleksi ini" dan tidak menambahkan entri duplikat.

---

### US-05 — Menonton Video Kelas Memasak

**Role:** User  
**User Story:**  
As a user, I want to watch cooking class videos uploaded by a chef, so that I can learn cooking techniques at my own pace whenever I want.

**Acceptance Criteria:**

- **AC-05:**  
  Given pengguna sudah login dan membuka halaman video kelas memasak,  
  When pengguna menekan tombol "Tonton Video",  
  Then sistem memutar video dan menampilkan judul video, nama chef, serta kolom komentar di bawah pemutar video.

- **AC-05 (edge case):**  
  Given pengguna sudah login dan sedang menonton video namun koneksi internet terputus,  
  When koneksi terputus,  
  Then video berhenti otomatis dan sistem menampilkan pesan "Koneksi terputus. Periksa koneksi internet kamu." tanpa menutup halaman.

---

### US-06 — Membuat Resep Sendiri

**Role:** User  
**User Story:**  
As a user, I want to create and publish my own recipe complete with cooking tools, ingredients, estimated time, and cooking steps, so that my recipe can be shared and learned by other users on the platform.

**Acceptance Criteria:**

- **AC-06:**  
  Given pengguna sudah login dan membuka halaman buat resep,  
  When pengguna mengisi judul, alat masak, bahan, estimasi waktu memasak, tingkat kesulitan, dan langkah masak lalu menekan "Publikasikan",  
  Then resep langsung dipublikasikan dan dapat ditemukan oleh seluruh pengguna di platform.

- **AC-06 (edge case):**  
  Given pengguna sudah login dan mencoba mempublikasikan resep tanpa mengisi kolom bahan,  
  When tombol "Publikasikan" ditekan,  
  Then sistem menampilkan pesan "Bahan wajib diisi" tepat di bawah kolom tersebut dan mencegah formulir terkirim hingga kolom tersebut dilengkapi.

---

### US-07 — Konsultasi Masalah Memasak dengan AI

**Role:** User  
**User Story:**  
As a user, I want to consult the AI about problems I experience while cooking, so that I can understand the cause and fix my cooking results without waiting for answers from others.

**Acceptance Criteria:**

- **AC-07:**  
  Given pengguna sudah login dan membuka halaman konsultasi AI,  
  When pengguna mengetikkan pertanyaan seputar masalah memasak seperti "kenapa masakanku gosong padahal sudah ikut resep?" lalu menekan "Kirim",  
  Then AI memberikan respons yang relevan berisi penjelasan penyebab dan saran perbaikan.

- **AC-07 (edge case):**  
  Given pengguna sudah login dan mengirimkan pertanyaan yang tidak berhubungan dengan memasak,  
  When pertanyaan tersebut dikirim,  
  Then AI menampilkan pesan "Maaf, saya hanya dapat membantu pertanyaan seputar memasak dan resep." dan tidak memberikan jawaban di luar topik tersebut.

---

### US-08 — Mempublikasikan Resep (Creator)

**Role:** Creator  
**User Story:**  
As a creator, I want to publish a complete recipe with cooking tools, ingredients, estimated time, and cooking steps, so that my recipe can be learned by other users on the platform.

**Acceptance Criteria:**

- **AC-08:**  
  Given creator sudah login sebagai creator terverifikasi dan membuka halaman tambah resep,  
  When creator mengisi judul, alat masak, bahan, estimasi waktu memasak, tingkat kesulitan, dan langkah masak lalu menekan "Publikasikan",  
  Then resep langsung dipublikasikan dan dapat ditemukan oleh seluruh pengguna di platform.

- **AC-08 (edge case):**  
  Given creator sudah login dan mencoba mempublikasikan resep tanpa mengisi kolom langkah masak,  
  When tombol "Publikasikan" ditekan,  
  Then sistem menampilkan pesan "Langkah masak wajib diisi" tepat di bawah kolom tersebut dan mencegah formulir terkirim.

---

### US-09 — Mengunggah Video Kelas Memasak (Creator)

**Role:** Creator  
**User Story:**  
As a creator, I want to upload a cooking class video to the platform, so that users can learn cooking techniques from me at their own pace.

**Acceptance Criteria:**

- **AC-09:**  
  Given creator sudah login sebagai creator terverifikasi dan membuka halaman unggah video,  
  When creator mengisi judul video, deskripsi, tingkat kesulitan, dan mengunggah file video berformat MP4 lalu menekan "Unggah",  
  Then video langsung dipublikasikan dan dapat ditonton oleh seluruh pengguna di platform.

- **AC-09 (edge case):**  
  Given creator sudah login dan mencoba mengunggah file video berukuran lebih dari 500MB,  
  When file dipilih dan tombol "Unggah" ditekan,  
  Then sistem menampilkan pesan "Ukuran file melebihi batas 500MB. Kompres video sebelum mengunggah." dan membatalkan proses unggah.

---

### US-10 — Memverifikasi Akun Creator (Admin)

**Role:** Admin  
**User Story:**  
As an admin, I want to verify creator accounts that submit applications, so that only credible and qualified chefs can upload cooking class videos on the platform.

**Acceptance Criteria:**

- **AC-10:**  
  Given admin sudah login dan membuka dashboard verifikasi yang menampilkan pengajuan baru,  
  When admin menekan tombol "Setujui" pada pengajuan creator dengan dokumen lengkap,  
  Then status akun berubah menjadi "Terverifikasi", lencana terverifikasi muncul di profil publik creator, dan creator menerima notifikasi "Akun kamu telah diverifikasi."

- **AC-10 (edge case):**  
  Given admin sudah login dan menemukan pengajuan creator dengan dokumen tidak lengkap,  
  When admin menekan tombol "Tolak" dan mengisi alasan penolakan,  
  Then status pengajuan berubah menjadi "Ditolak", creator menerima notifikasi berisi alasan penolakan dan instruksi untuk mengajukan ulang, dan akun creator tetap tidak dapat mengunggah video.

---
