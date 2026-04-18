# Software Requirements Specification (SRS) 

## 1. Pendahuluan

### 1.1 Tujuan Dokumen

Dokumen ini merupakan Software Requirements Specification (SRS) ringkas untuk aplikasi Cubu (Cookbook Application). Dokumen ini bertujuan untuk merinci spesifikasi kebutuhan perangkat lunak, termasuk kebutuhan fungsional dan non-fungsional sistem, yang akan menjadi acuan bagi tim pengembang dalam proses implementasi, pengujian, verifikasi fitur, dan penerimaan produk. SRS ini disusun berdasarkan artefak P2 yang telah divalidasi, meliputi problem statement, user stories, dan product backlog.

### 1.2 Ruang Lingkup

Cubu (Cookbook Application) adalah platform katalog resep digital dan belajar memasak berbasis web yang berfokus pada bahan lokal Indonesia, menghubungkan pengguna dengan panduan resep terstruktur serta video kelas memasak dari chef terverifikasi. Sistem mencakup fitur manajemen akun (autentikasi, verifikasi creator oleh admin), pembuatan/publikasi resep, pencarian dan pengelolaan koleksi resep pribadi, penayangan video kelas memasak, serta konsultasi masalah memasak menggunakan AI. Sistem tidak mencakup fitur transaksi jual beli, pemesanan katering, atau pembayaran berbayar pada iterasi pertama ini.

### 1.3 Definisi dan Akronim

| Istilah | Definisi |
|---|---|
| SRS | Software Requirements Specification — dokumen spesifikasi kebutuhan perangkat lunak |
| FR | Functional Requirement — kebutuhan fungsional sistem |
| NFR | Non-Functional Requirement — kebutuhan non-fungsional sistem |
| Guest | Pengguna yang belum login; hanya dapat melihat konten publik |
| User | Pengguna terdaftar dengan akun gratis |
| Creator | Chef terverifikasi yang dapat mempublikasikan resep dan mengunggah video |
| Admin | Pengelola platform yang memverifikasi creator dan mengelola konten |
| MVP | Minimum Viable Product — versi produk paling dasar yang dapat digunakan |
| AI | Artificial Intelligence — kecerdasan buatan untuk fitur konsultasi memasak |

---

## 2. Deskripsi Umum

### 2.1 Perspektif Produk

Aplikasi Cubu merupakan solusi terpadu untuk masyarakat Indonesia yang ingin belajar memasak secara mandiri melalui platform web digital, dengan panduan resep terstruktur, bantuan AI eksternal untuk konsultasi memasak, serta beroperasi sebagai ekosistem dua sisi: pengguna pembelajar di satu sisi dan chef terverifikasi sebagai penyedia konten di sisi lain.

### 2.2 Fungsi Utama

| No | Fungsi | Deskripsi Singkat |
|---|---|---|
| 1 | Autentikasi | Registrasi dan login pengguna dengan validasi email |
| 2 | Manajemen Resep | Publikasi, pencarian, dan pengelolaan resep dengan detail lengkap |
| 3 | Koleksi Pribadi | Penyimpanan resep favorit ke dalam koleksi yang dapat dikelola |
| 4 | Video Kelas Memasak | Pengunggahan dan penayangan video kelas dari creator terverifikasi |
| 5 | Konsultasi AI | Tanya jawab seputar masalah memasak berbasis kecerdasan buatan |
| 6 | Verifikasi Creator | Proses persetujuan akun chef oleh admin sebelum dapat mempublikasikan konten |

### 2.3 Karakteristik Pengguna

| Role | Karakteristik |
|---|---|
| Guest | Pengguna umum yang belum memiliki akun; dapat mengakses konten publik tanpa interaksi |
| User | Masyarakat Indonesia yang baru belajar memasak; melek teknologi dasar; menggunakan perangkat mobile maupun desktop |
| Creator | Chef profesional atau pengajar memasak terverifikasi; memiliki kemampuan membuat konten resep dan video |
| Admin | Pengelola platform internal; bertanggung jawab menjaga kualitas konten dan keamanan platform |

### 2.4 Batasan Sistem

- Aplikasi hanya tersedia dalam Bahasa Indonesia dan ditujukan untuk pengguna di Indonesia.
- Pada iterasi pertama, sistem hanya dikembangkan dalam versi web; aplikasi mobile native belum termasuk dalam scope.
- Fitur pembayaran dan subscription berbayar tidak termasuk dalam scope semester ini.
- Fitur pemesanan katering dan transaksi jual beli tidak termasuk dalam scope aplikasi.
- Konten video dibatasi maksimal 500MB per file dalam format MP4.
- Sistem membutuhkan koneksi internet aktif untuk seluruh fitur utamanya.

---

## 3. Kebutuhan Fungsional

FR-01: Sistem menyediakan fitur registrasi akun baru dengan validasi keunikan email dan pengiriman email verifikasi ke alamat yang didaftarkan.  
Prioritas: High | Ref: US-02

---

FR-02: Sistem memungkinkan pengguna yang belum login untuk menjelajah daftar resep publik beserta detail resep (alat masak, bahan, estimasi waktu, dan langkah masak) tanpa memerlukan autentikasi.  
Prioritas: High | Ref: US-01

---

FR-03: Sistem membatasi akses fitur interaksi (tonton video, simpan resep, komentar, rating, konsultasi AI) hanya untuk pengguna yang sudah login, dan menampilkan pesan ajakan registrasi ketika guest mencoba mengakses fitur tersebut.  
Prioritas: High | Ref: US-01

---

FR-04: Sistem memungkinkan creator yang telah terverifikasi untuk mempublikasikan resep lengkap yang mencakup judul, alat masak, bahan, estimasi waktu memasak, tingkat kesulitan, dan langkah masak, dengan validasi bahwa seluruh kolom wajib telah terisi sebelum resep dipublikasikan.  
Prioritas: High | Ref: US-08

---

FR-05: Sistem memungkinkan pengguna terdaftar untuk membuat dan mempublikasikan resep pribadi dengan kolom yang sama seperti creator, sehingga resep tersebut dapat ditemukan dan dipelajari oleh pengguna lain di platform.  
Prioritas: Medium | Ref: US-06

---

FR-06: Sistem menyediakan fitur pencarian resep berdasarkan kata kunci nama masakan atau nama bahan, dan menampilkan hasil yang diurutkan berdasarkan relevansi kata kunci serta menyarankan resep populer apabila tidak ada hasil yang ditemukan.  
Prioritas: Medium | Ref: US-03

---

FR-07: Sistem memungkinkan pengguna terdaftar untuk menyimpan resep ke dalam koleksi pribadi yang dapat diberi nama, dan mencegah penambahan resep duplikat ke dalam koleksi yang sama.  
Prioritas: Medium | Ref: US-04

---

FR-08: Sistem memungkinkan creator terverifikasi untuk mengunggah video kelas memasak dalam format MP4 dengan batas ukuran 500MB, dan menampilkan video beserta judul, nama chef, serta kolom komentar kepada pengguna yang sudah login.  
Prioritas: Medium | Ref: US-09

---

FR-09: Sistem menyediakan fitur konsultasi berbasis AI yang hanya menerima dan menjawab pertanyaan seputar memasak dan resep, serta menampilkan pesan penolakan apabila pertanyaan di luar topik tersebut.  
Prioritas: Low | Ref: US-07

---

FR-10: Sistem menyediakan dashboard verifikasi bagi admin untuk menyetujui atau menolak pengajuan akun creator, mengirimkan notifikasi hasil verifikasi kepada pemohon, dan mengaktifkan atau menonaktifkan hak akses publikasi konten berdasarkan keputusan admin.  
Prioritas: Medium | Ref: US-10

---

## 4. Kebutuhan Non-Fungsional

NFR-01 (Performance): Halaman utama dan halaman daftar resep harus termuat dalam waktu kurang dari 3 detik pada koneksi broadband 10 Mbps, diukur menggunakan alat seperti Google Lighthouse atau browser DevTools.

---

NFR-02 (Security): Kata sandi pengguna harus disimpan dalam bentuk hash menggunakan algoritma bcrypt dengan cost factor minimal 10, dan tidak boleh disimpan atau dikirimkan dalam bentuk plain text di seluruh lapisan sistem.

---

NFR-03 (Usability): Antarmuka aplikasi harus responsif dan dapat digunakan pada perangkat dengan lebar layar minimal 375px (setara smartphone standar), diverifikasi melalui pengujian di mode mobile pada browser Chrome dan Firefox.

---

NFR-04 (Reliability): Sistem harus tersedia minimal 99% waktu dalam sebulan, yang setara dengan maksimal downtime 7 jam per bulan, diukur menggunakan layanan monitoring uptime.

---

NFR-05 (Security): Seluruh komunikasi antara klien dan server harus menggunakan protokol HTTPS dengan sertifikat SSL/TLS yang valid, sehingga tidak ada data pengguna yang dikirimkan melalui koneksi tidak terenkripsi.

---

## 5. Catatan dan Asumsi

### Asumsi

- Pengguna diasumsikan memiliki akses internet yang memadai untuk menggunakan aplikasi secara penuh.
- Creator diasumsikan sudah memiliki kemampuan dasar dalam pembuatan video dan penulisan resep sebelum mendaftar sebagai creator.
- Layanan AI pihak ketiga (API) diasumsikan tersedia dan dapat diakses oleh sistem selama operasional berlangsung.
- Seluruh konten resep dan video diasumsikan berbahasa Indonesia dan menggunakan bahan-bahan yang tersedia di pasar lokal Indonesia.

### Batasan Teknis

- Fitur konsultasi AI bergantung pada ketersediaan dan kebijakan layanan API pihak ketiga yang digunakan.
- Fitur pengunggahan video membutuhkan kapasitas penyimpanan server yang memadai dan dapat meningkat seiring bertambahnya konten.
- Sistem autentikasi menggunakan verifikasi email sehingga bergantung pada ketersediaan layanan email transaksional.

### Di Luar Lingkup (Out of Scope)

- Fitur notifikasi push pada perangkat mobile native.
- Sistem rekomendasi resep berbasis AI (direncanakan pada iterasi berikutnya).
- Dukungan multibahasa selain Bahasa Indonesia.
- Fitur pembayaran, subscription, dan transaksi komersial.
