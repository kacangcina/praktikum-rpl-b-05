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

### 1. Menjelajah Resep Tanpa Akun

**Role:** Guest  
**User Story:**  
Sebagai tamu, saya ingin menjelajah dan melihat daftar resep tanpa harus membuat akun terlebih dahulu, agar saya dapat menilai manfaat aplikasi sebelum memutuskan untuk mendaftar.

**Acceptance Criteria:**

- **a:**  
  Given saya belum login dan membuka halaman utama aplikasi,  
  When halaman selesai dimuat,  
  Then sistem menampilkan minimal 10 resep publik lengkap dengan judul, foto, dan tingkat kesulitan tanpa memunculkan dialog login.

- **b (edge case):**  
  Given saya belum login dan menekan tombol "Simpan ke Koleksi" pada sebuah resep,  
  When tombol tersebut ditekan,  
  Then sistem menampilkan pesan "Daftar atau masuk untuk menyimpan resep" dan menampilkan tombol menuju halaman registrasi, bukan langsung menyimpan resep.

---

### 2. Mendaftar Akun Baru

**Role:** Guest  
**User Story:**  
Sebagai tamu, saya ingin mendaftar akun baru menggunakan email dan kata sandi, agar saya dapat mengakses fitur lengkap aplikasi sebagai pengguna terdaftar.

**Acceptance Criteria:**

- **a:**  
  Given saya berada di halaman registrasi dan mengisi email valid, kata sandi minimal 8 karakter, serta nama lengkap,  
  When saya menekan tombol "Daftar",  
  Then sistem membuat akun baru, mengirimkan email verifikasi ke alamat yang didaftarkan, dan menampilkan pesan "Akun berhasil dibuat. Cek email kamu untuk verifikasi."

- **b (edge case):**  
  Given saya berada di halaman registrasi dan memasukkan email yang sudah terdaftar di sistem,  
  When saya menekan tombol "Daftar",  
  Then sistem menampilkan pesan "Email sudah digunakan. Silakan gunakan email lain atau masuk ke akun kamu." dan tidak membuat akun duplikat.

---

### 3. Mencari Resep Berdasarkan Nama atau Bahan

**Role:** User  
**User Story:**  
Sebagai pengguna, saya ingin mencari resep berdasarkan nama masakan atau bahan yang saya miliki, agar saya dapat dengan cepat menemukan resep yang sesuai tanpa harus menggulir seluruh daftar.

**Acceptance Criteria:**

- **a:**  
  Given saya sudah login dan berada di halaman pencarian,  
  When saya mengetikkan kata kunci nama masakan atau nama bahan lalu menekan tombol "Cari",  
  Then sistem menampilkan daftar resep yang relevan dalam waktu kurang dari 3 detik, diurutkan berdasarkan tingkat kesesuaian kata kunci.

- **b (edge case):**  
  Given saya sudah login dan memasukkan kata kunci yang tidak cocok dengan resep manapun,  
  When pencarian dijalankan,  
  Then sistem menampilkan pesan "Tidak ada resep yang ditemukan untuk '[kata kunci]'" dan menyarankan 3 resep populer sebagai alternatif.

---

### 4. Memfilter Resep Berdasarkan Tingkat Kesulitan

**Role:** User  
**User Story:**  
Sebagai pengguna, saya ingin memfilter resep berdasarkan tingkat kesulitan, agar saya hanya melihat resep yang sesuai dengan kemampuan memasak saya saat ini.

**Acceptance Criteria:**

- **a:**  
  Given saya sudah login dan berada di halaman daftar resep,  
  When saya memilih filter "Mudah" dari menu filter tingkat kesulitan,  
  Then sistem hanya menampilkan resep dengan label "Mudah" dan menyembunyikan resep berlabel "Sedang" dan "Sulit" selama filter aktif.

- **b (edge case):**  
  Given saya sudah mengaktifkan filter "Mudah" namun tidak ada resep berlabel tersebut di database,  
  When filter diterapkan,  
  Then sistem menampilkan pesan "Belum ada resep dengan tingkat kesulitan ini" dan menyarankan pengguna untuk menghapus filter.

---

### 5. Menyimpan Resep ke Koleksi Pribadi

**Role:** User  
**User Story:**  
Sebagai pengguna, saya ingin menyimpan resep ke dalam koleksi pribadi saya, agar saya dapat menemukan kembali resep favorit dengan mudah tanpa harus mencari ulang.

**Acceptance Criteria:**

- **a:**  
  Given saya sudah login dan sedang membuka halaman detail sebuah resep,  
  When saya menekan tombol "Simpan ke Koleksi" dan memilih nama koleksi yang sudah ada atau membuat koleksi baru,  
  Then resep tersimpan ke koleksi yang dipilih dan sistem menampilkan notifikasi "Resep berhasil disimpan ke [nama koleksi]".

- **b (edge case):**  
  Given saya sudah login dan mencoba menyimpan resep yang sudah ada di koleksi yang sama,  
  When tombol "Simpan ke Koleksi" ditekan dan koleksi yang sama dipilih,  
  Then sistem menampilkan pesan "Resep sudah ada di koleksi ini" dan tidak menambahkan entri duplikat ke koleksi tersebut.

---

### 6. Mengikuti Kelas Memasak Live

**Role:** User  
**User Story:**  
Sebagai pengguna, saya ingin mengikuti sesi kelas memasak live yang diselenggarakan oleh chef, agar saya dapat belajar memasak secara langsung dan mengajukan pertanyaan secara real-time.

**Acceptance Criteria:**

- **a:**  
  Given saya sudah login dan membuka halaman kelas live yang sedang berlangsung,  
  When saya menekan tombol "Ikuti Kelas",  
  Then sistem menampilkan jendela streaming live beserta kolom komentar aktif yang memungkinkan saya mengirim pertanyaan kepada chef.

- **b (edge case):**  
  Given saya sudah login dan mencoba mengakses halaman kelas live yang sudah berakhir,  
  When halaman tersebut dibuka,  
  Then sistem menampilkan pesan "Kelas sudah selesai" dan menampilkan tombol "Tonton Rekaman" jika rekaman tersedia, atau "Lihat Kelas Lainnya" jika tidak ada rekaman.

---

### 7. Membuat Resep Sendiri

**Role:** User  
**User Story:**  
Sebagai pengguna, saya ingin membuat dan mempublikasikan resep masakan saya sendiri, agar resep buatan saya dapat dibagikan dan dipelajari oleh pengguna lain di platform.

**Acceptance Criteria:**

- **a:**  
  Given saya sudah login sebagai user dan membuka halaman buat resep,  
  When saya mengisi judul, minimal 3 bahan, minimal 3 langkah masak, tingkat kesulitan, dan estimasi waktu lalu menekan "Kirim untuk Direview",  
  Then resep tersimpan dengan status "Menunggu Review" dan saya menerima notifikasi "Resep kamu sedang direview oleh admin."

- **b (edge case):**  
  Given saya sudah login sebagai user dan mencoba mengirim resep tanpa mengisi langkah masak,  
  When tombol "Kirim untuk Direview" ditekan,  
  Then sistem menampilkan pesan "Langkah masak wajib diisi" tepat di bawah kolom tersebut dan mencegah formulir terkirim hingga kolom tersebut dilengkapi.

---

### 8. Mempublikasikan Resep (Creator)

**Role:** Creator  
**User Story:**  
Sebagai creator, saya ingin mempublikasikan resep lengkap dengan bahan dan langkah memasaknya, agar resep saya dapat dipelajari oleh pengguna lain di platform.

**Acceptance Criteria:**

- **a:**  
  Given saya sudah login sebagai creator terverifikasi dan membuka halaman tambah resep,  
  When saya mengisi judul, minimal 3 bahan, minimal 3 langkah masak, tingkat kesulitan, dan estimasi waktu lalu menekan "Kirim untuk Direview",  
  Then resep tersimpan dengan status "Menunggu Review" dan saya menerima notifikasi "Resep kamu sedang direview oleh admin."

- **b (edge case):**  
  Given saya sudah login sebagai creator dan mencoba mempublikasikan resep tanpa mengisi kolom langkah masak,  
  When tombol "Kirim untuk Direview" ditekan,  
  Then sistem menampilkan pesan "Langkah masak wajib diisi" tepat di bawah kolom tersebut dan mencegah formulir terkirim.

---

### 9. Menyelenggarakan Sesi Live Kelas Memasak

**Role:** Creator  
**User Story:**  
Sebagai creator, saya ingin membuat dan memulai sesi live kelas memasak di platform, agar saya dapat mengajar dan berinteraksi langsung dengan pengguna yang ingin belajar memasak.

**Acceptance Criteria:**

- **a:**  
  Given saya sudah login sebagai creator terverifikasi dan mengisi judul sesi, deskripsi, dan jadwal live,  
  When saya menekan tombol "Mulai Live",  
  Then sesi live aktif, muncul di halaman utama dengan label "Sedang Live", dan pengguna yang sedang online menerima notifikasi push tentang sesi yang baru dimulai.

- **b (edge case):**  
  Given saya sudah login sebagai creator dan mencoba memulai sesi live tanpa mengisi judul,  
  When tombol "Mulai Live" ditekan,  
  Then sistem menampilkan pesan "Judul sesi wajib diisi" dan sesi tidak dimulai sampai kolom judul terisi.

---

### 10. Memverifikasi Akun Creator

**Role:** Admin  
**User Story:**  
Sebagai admin, saya ingin memverifikasi akun creator yang mengajukan permohonan, agar hanya chef yang kredibel dan memenuhi syarat yang dapat menyelenggarakan kelas live.

**Acceptance Criteria:**

- **AC-10a:**  
  Given saya sudah login sebagai admin dan membuka dashboard verifikasi yang menampilkan pengajuan baru,  
  When saya menekan tombol "Setujui" pada pengajuan creator yang dokumennya lengkap,  
  Then status akun berubah menjadi "Terverifikasi", lencana terverifikasi muncul di profil publik creator, dan creator menerima notifikasi "Akun kamu telah diverifikasi."

- **AC-10b (edge case):**  
  Given saya sudah login sebagai admin dan menemukan pengajuan creator dengan dokumen tidak lengkap,  
  When saya menekan tombol "Tolak" dan mengisi alasan penolakan,  
  Then status pengajuan berubah menjadi "Ditolak", creator menerima notifikasi berisi alasan penolakan dan instruksi untuk mengajukan ulang, dan akun creator tetap tidak dapat menyelenggarakan kelas live.

---

### 11. Memoderasi Resep yang Dikirimkan

**Role:** Admin  
**User Story:**  
Sebagai admin, saya ingin meninjau dan memoderasi resep yang dikirimkan oleh user maupun creator sebelum ditampilkan ke publik, agar seluruh konten yang ada di platform sesuai dengan standar kualitas dan pedoman komunitas.

**Acceptance Criteria:**

- **a:**  
  Given saya sudah login sebagai admin dan membuka dashboard moderasi yang menampilkan resep dengan status "Menunggu Review",  
  When saya menekan tombol "Setujui" pada sebuah resep,  
  Then status resep berubah menjadi "Dipublikasikan", resep langsung tampil dan dapat dicari oleh seluruh pengguna, serta pengirim menerima notifikasi "Resep kamu telah disetujui dan dipublikasikan."

- **b (edge case):**  
  Given saya sudah login sebagai admin dan menemukan resep yang tidak sesuai pedoman,  
  When saya menekan tombol "Tolak" dan mengisi catatan perbaikan,  
  Then status resep berubah menjadi "Ditolak", resep tidak tampil ke publik, dan pengirim menerima notifikasi berisi catatan perbaikan yang harus dilakukan sebelum mengajukan ulang.

---
