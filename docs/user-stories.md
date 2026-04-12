# User Stories & Acceptance Criteria

## Identifikasi Role / Aktor

| No | Role | Deskripsi |
|---|---|---|
| 1 | **Guest** | Pengguna yang belum login; hanya dapat melihat konten publik seperti daftar resep dan preview kelas tanpa bisa menyimpan atau berinteraksi |
| 2 | **User** | Pengguna terdaftar; dapat mengakses seluruh fitur belajar memasak, menyimpan resep, mengikuti kelas live, serta membuat dan mempublikasikan resep pribadi — tetapi tidak dapat menyelenggarakan kelas live |
| 3 | **Creator** | Chef yang sudah terverifikasi oleh admin; dapat mempublikasikan resep dan menyelenggarakan sesi live kelas memasak |
| 4 | **Admin** | Pengelola platform; dapat memverifikasi akun creator, memoderasi resep, dan mengelola konten yang tampil di platform |

---

## User Stories

---

### US-01 — Menjelajah Resep Tanpa Akun

**Role:** Guest  
**User Story:**  
As a Guest, I want to browse and view the recipe list without creating an account, so that I can evaluate the app's benefits before deciding to register.

**Acceptance Criteria:**

- **AC-01a:**  
  Given saya belum login dan membuka halaman utama aplikasi,  
  When halaman selesai dimuat,  
  Then sistem menampilkan minimal 10 resep publik lengkap dengan judul, foto, dan tingkat kesulitan tanpa memunculkan dialog login.

- **AC-01b (edge case):**  
  Given saya belum login dan menekan tombol "Simpan ke Koleksi" pada sebuah resep,  
  When tombol tersebut ditekan,  
  Then sistem menampilkan pesan "Daftar atau masuk untuk menyimpan resep" dan menampilkan tombol menuju halaman registrasi, bukan langsung menyimpan resep.

---

### US-02 — Mendaftar Akun Baru

**Role:** Guest  
**User Story:**  
As a Guest, I want to register for a new account using my email and password, so that I can access the full features of the application as a registered user.

**Acceptance Criteria:**

- **AC-02:**  
  Given saya berada di halaman registrasi dan mengisi email valid, kata sandi minimal 8 karakter, serta nama lengkap,  
  When saya menekan tombol "Daftar",  
  Then sistem membuat akun baru, mengirimkan email verifikasi ke alamat yang didaftarkan, dan menampilkan pesan "Akun berhasil dibuat. Cek email kamu untuk verifikasi."

- **AC-02 (edge case):**  
  Given saya berada di halaman registrasi dan memasukkan email yang sudah terdaftar di sistem,  
  When saya menekan tombol "Daftar",  
  Then sistem menampilkan pesan "Email sudah digunakan. Silakan gunakan email lain atau masuk ke akun kamu." dan tidak membuat akun duplikat.

---

### US-03 — Mencari Resep Berdasarkan Nama atau Bahan

**Role:** User  
**User Story:**  
As a User, I want to search for recipes based on the dish name or ingredients I have, so that I can quickly find suitable recipes without scrolling through the entire list.

**Acceptance Criteria:**

- **AC-03:**  
  Given saya sudah login dan berada di halaman pencarian,  
  When saya mengetikkan kata kunci nama masakan atau nama bahan lalu menekan tombol "Cari",  
  Then sistem menampilkan daftar resep yang relevan dalam waktu kurang dari 3 detik, diurutkan berdasarkan tingkat kesesuaian kata kunci.

- **AC-03 (edge case):**  
  Given saya sudah login dan memasukkan kata kunci yang tidak cocok dengan resep manapun,  
  When pencarian dijalankan,  
  Then sistem menampilkan pesan "Tidak ada resep yang ditemukan untuk '[kata kunci]'" dan menyarankan 3 resep populer sebagai alternatif.

---

### US-04 — Memfilter Resep Berdasarkan Tingkat Kesulitan

**Role:** User  
**User Story:**  
As a User, I want to filter recipes by difficulty level, so that I only see recipes that match my current cooking skills.

**Acceptance Criteria:**

- **AC-04:**  
  Given saya sudah login dan berada di halaman daftar resep,  
  When saya memilih filter "Mudah" dari menu filter tingkat kesulitan,  
  Then sistem hanya menampilkan resep dengan label "Mudah" dan menyembunyikan resep berlabel "Sedang" dan "Sulit" selama filter aktif.

- **AC-04 (edge case):**  
  Given saya sudah mengaktifkan filter "Mudah" namun tidak ada resep berlabel tersebut di database,  
  When filter diterapkan,  
  Then sistem menampilkan pesan "Belum ada resep dengan tingkat kesulitan ini" dan menyarankan pengguna untuk menghapus filter.

---

### US-05 — Menyimpan Resep ke Koleksi Pribadi

**Role:** User  
**User Story:**  
As a User, I want to save recipes to my personal collection, so that I can easily find my favorite recipes again without searching from scratch..

**Acceptance Criteria:**

- **AC-05:**  
  Given saya sudah login dan sedang membuka halaman detail sebuah resep,  
  When saya menekan tombol "Simpan ke Koleksi" dan memilih nama koleksi yang sudah ada atau membuat koleksi baru,  
  Then resep tersimpan ke koleksi yang dipilih dan sistem menampilkan notifikasi "Resep berhasil disimpan ke [nama koleksi]".

- **AC-05 (edge case):**  
  Given saya sudah login dan mencoba menyimpan resep yang sudah ada di koleksi yang sama,  
  When tombol "Simpan ke Koleksi" ditekan dan koleksi yang sama dipilih,  
  Then sistem menampilkan pesan "Resep sudah ada di koleksi ini" dan tidak menambahkan entri duplikat ke koleksi tersebut.

---

### US-06 — Mengikuti Kelas Memasak Live

**Role:** User  
**User Story:**  
As a User, I want to join live cooking classes hosted by chefs, so that I can learn directly and ask questions in real-time.

**Acceptance Criteria:**

- **AC-06:**  
  Given saya sudah login dan membuka halaman kelas live yang sedang berlangsung,  
  When saya menekan tombol "Ikuti Kelas",  
  Then sistem menampilkan jendela streaming live beserta kolom komentar aktif yang memungkinkan saya mengirim pertanyaan kepada chef.

- **AC-06 (edge case):**  
  Given saya sudah login dan mencoba mengakses halaman kelas live yang sudah berakhir,  
  When halaman tersebut dibuka,  
  Then sistem menampilkan pesan "Kelas sudah selesai" dan menampilkan tombol "Tonton Rekaman" jika rekaman tersedia, atau "Lihat Kelas Lainnya" jika tidak ada rekaman.

---

### US-07 — Membuat Resep Sendiri

**Role:** User  
**User Story:**  
As a User, I want to create and publish my own recipes, so that my recipes can be shared and learned by other users on the platform.

**Acceptance Criteria:**

- **AC-07:**  
  Given saya sudah login sebagai user dan membuka halaman buat resep,  
  When saya mengisi judul, minimal 3 bahan, minimal 3 langkah masak, tingkat kesulitan, dan estimasi waktu lalu menekan "Kirim untuk Direview",  
  Then resep tersimpan dengan status "Menunggu Review" dan saya menerima notifikasi "Resep kamu sedang direview oleh admin."

- **AC-07 (edge case):**  
  Given saya sudah login sebagai user dan mencoba mengirim resep tanpa mengisi langkah masak,  
  When tombol "Kirim untuk Direview" ditekan,  
  Then sistem menampilkan pesan "Langkah masak wajib diisi" tepat di bawah kolom tersebut dan mencegah formulir terkirim hingga kolom tersebut dilengkapi.

---

### US-08 — Mempublikasikan Resep (Creator)

**Role:** Creator  
**User Story:**  
As a Creator, I want to publish complete recipes with ingredients and cooking steps, so that my recipes can be accurately followed by other users.

**Acceptance Criteria:**

- **AC-08:**  
  Given saya sudah login sebagai creator terverifikasi dan membuka halaman tambah resep,  
  When saya mengisi judul, minimal 3 bahan, minimal 3 langkah masak, tingkat kesulitan, dan estimasi waktu lalu menekan "Kirim untuk Direview",  
  Then resep tersimpan dengan status "Menunggu Review" dan saya menerima notifikasi "Resep kamu sedang direview oleh admin."

- **AC-08 (edge case):**  
  Given saya sudah login sebagai creator dan mencoba mempublikasikan resep tanpa mengisi kolom langkah masak,  
  When tombol "Kirim untuk Direview" ditekan,  
  Then sistem menampilkan pesan "Langkah masak wajib diisi" tepat di bawah kolom tersebut dan mencegah formulir terkirim.

---

### US-09 — Menyelenggarakan Sesi Live Kelas Memasak

**Role:** Creator  
**User Story:**  
As a Creator, I want to start a live cooking class session, so that I can teach and interact directly with users who want to learn.

**Acceptance Criteria:**

- **AC-09:**  
  Given saya sudah login sebagai creator terverifikasi dan mengisi judul sesi, deskripsi, dan jadwal live,  
  When saya menekan tombol "Mulai Live",  
  Then sesi live aktif, muncul di halaman utama dengan label "Sedang Live", dan pengguna yang sedang online menerima notifikasi push tentang sesi yang baru dimulai.

- **AC-09 (edge case):**  
  Given saya sudah login sebagai creator dan mencoba memulai sesi live tanpa mengisi judul,  
  When tombol "Mulai Live" ditekan,  
  Then sistem menampilkan pesan "Judul sesi wajib diisi" dan sesi tidak dimulai sampai kolom judul terisi.

---

### US-10 — Memverifikasi Akun Creator

**Role:** Admin  
**User Story:**  
As an Admin, I want to verify applicant creator accounts, so that only credible and qualified chefs can host live classes.
**Acceptance Criteria:**

- **AC-10:**  
  Given saya sudah login sebagai admin dan membuka dashboard verifikasi yang menampilkan pengajuan baru,  
  When saya menekan tombol "Setujui" pada pengajuan creator yang dokumennya lengkap,  
  Then status akun berubah menjadi "Terverifikasi", lencana terverifikasi muncul di profil publik creator, dan creator menerima notifikasi "Akun kamu telah diverifikasi."

- **AC-10 (edge case):**  
  Given saya sudah login sebagai admin dan menemukan pengajuan creator dengan dokumen tidak lengkap,  
  When saya menekan tombol "Tolak" dan mengisi alasan penolakan,  
  Then status pengajuan berubah menjadi "Ditolak", creator menerima notifikasi berisi alasan penolakan dan instruksi untuk mengajukan ulang, dan akun creator tetap tidak dapat menyelenggarakan kelas live.

---

### US-11 — Memoderasi Resep yang Dikirimkan

**Role:** Admin  
**User Story:**  
As an Admin, I want to verify applicant creator accounts, so that only credible and qualified chefs can host live classes.

**Acceptance Criteria:**

- **AC-11:**  
  Given saya sudah login sebagai admin dan membuka dashboard moderasi yang menampilkan resep dengan status "Menunggu Review",  
  When saya menekan tombol "Setujui" pada sebuah resep,  
  Then status resep berubah menjadi "Dipublikasikan", resep langsung tampil dan dapat dicari oleh seluruh pengguna, serta pengirim menerima notifikasi "Resep kamu telah disetujui dan dipublikasikan."

- **AC-11 (edge case):**  
  Given saya sudah login sebagai admin dan menemukan resep yang tidak sesuai pedoman,  
  When saya menekan tombol "Tolak" dan mengisi catatan perbaikan,  
  Then status resep berubah menjadi "Ditolak", resep tidak tampil ke publik, dan pengirim menerima notifikasi berisi catatan perbaikan yang harus dilakukan sebelum mengajukan ulang.

---
