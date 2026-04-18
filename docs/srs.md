# Software Requirements Specification (SRS) Ringkas - Cookbook Application

## 1. Pendahuluan

### 1.1 Tujuan Dokumen
Dokumen ini bertujuan untuk merinci spesifikasi kebutuhan perangkat lunak untuk proyek "Cookbook Application". Dokumen ini akan menjadi acuan bagi tim pengembang dalam proses implementasi, pengujian, dan verifikasi fitur.

### 1.2 Ruang Lingkup
"Cookbook Application" adalah platform katalog resep digital berbasis web yang berfokus pada bahan lokal Indonesia. Sistem mencakup fitur manajemen akun, pembuatan resep, penayangan video kelas memasak, serta konsultasi masalah memasak menggunakan AI.

### 1.3 Definisi dan Akronim
* **SRS**: *Software Requirements Specification*  
* **FR**: *Functional Requirement*  
* **NFR**: *Non-Functional Requirement*  
* **Guest**: Pengguna yang belum login  
* **User**: Pengguna terdaftar  
* **Creator**: Chef terverifikasi yang dapat mengunggah video  
* **Admin**: Pengelola platform  

## 2. Deskripsi Umum

### 2.1 Perspektif Produk
Aplikasi ini merupakan solusi terpadu untuk masyarakat Indonesia yang ingin belajar memasak secara mandiri dengan panduan resep terstruktur dan bantuan AI.

### 2.2 Fungsi Produk
* Manajemen akun (Registrasi dan Login)  
* Eksplorasi resep bahan lokal tanpa harus login  
* Pembuatan dan publikasi resep oleh pengguna  
* Media pembelajaran melalui video kelas memasak  
* Asisten konsultasi berbasis AI untuk diagnosa kesalahan memasak  

### 2.3 Karakteristik Pengguna
* **Guest**: Ingin melihat resep sebelum memutuskan bergabung  
* **User**: Ingin belajar memasak, menyimpan koleksi resep, dan bertanya pada AI  
* **Creator**: Ingin berbagi keahlian memasak melalui resep teks dan video  
* **Admin**: Bertugas menjaga kredibilitas konten melalui verifikasi creator  

### 2.4 Batasan
* Fokus pada versi web untuk semester ini; aplikasi mobile native (iOS/Android) tidak dikerjakan  
* Hanya tersedia dalam Bahasa Indonesia  
* Ukuran file video maksimal adalah 500MB  

## 3. Kebutuhan Fungsional (FR)

| ID | Deskripsi Kebutuhan | Prioritas | Ref |
| :--- | :--- | :--- | :--- |
| **FR-01** | Sistem menyediakan fitur registrasi dan login menggunakan email valid serta kata sandi minimal 8 karakter. | **High** | US-02 |
| **FR-02** | Sistem menampilkan daftar resep publik (judul, foto, tingkat kesulitan) kepada Guest tanpa dialog login. | **High** | US-01 |
| **FR-03** | Sistem memungkinkan pengguna (User/Creator) membuat resep lengkap dengan alat, bahan, waktu, dan langkah masak. | **High** | US-06, US-08 |
| **FR-04** | Sistem memfasilitasi Admin untuk menyetujui atau menolak permohonan verifikasi akun Creator. | **High** | US-10 |
| **FR-05** | Sistem memfasilitasi Creator untuk mengunggah video kelas memasak berformat MP4. | **Medium** | US-09 |
| **FR-06** | Sistem menyediakan fitur konsultasi berbasis AI yang memberikan respons solusi terhadap masalah memasak pengguna. | **Low** | US-07 |

## 4. Kebutuhan Non-Fungsional (NFR)

| ID | Kategori | Deskripsi Kebutuhan |
| :--- | :--- | :--- |
| **NFR-01** | **Performance** | Halaman utama dan detail resep harus termuat dalam waktu kurang dari 3 detik pada koneksi 4G standar. |
| **NFR-02** | **Security** | Kata sandi pengguna harus di-hash menggunakan algoritma bcrypt sebelum disimpan di database. |
| **NFR-03** | **Usability** | Antarmuka aplikasi harus responsif dan dapat dioperasikan dengan baik pada perangkat mobile dengan lebar layar minimal 375px. |

## 5. Catatan dan Asumsi

* Asumsi: Pengguna memiliki koneksi internet yang stabil untuk menonton video kelas memasak tanpa gangguan  
* Asumsi: API AI untuk konsultasi tersedia dan dapat memberikan respons dalam domain kuliner secara akurat  
* Ketergantungan: Sistem bergantung pada layanan pihak ketiga untuk pengiriman email verifikasi registrasi  
