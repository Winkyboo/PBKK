# PBKK: Framework CodeIgniter (Latihan 1 & 2)

| Nama | NRP | Mata Kuliah | Kelas |
| --- | --- | --- | --- |
| Willy Marcelius | 5025241096 | Pemrograman Berbasis Kerangka Kerja | D |

---

## Deskripsi

Pada latihan ini saya menggunakan **CodeIgniter 4.7.6** untuk membuat tiga aplikasi web dengan pola **Model–View–Controller (MVC)**. Latihan 1 berisi **Sistem Manajemen Studio Musik**, sedangkan Latihan 2 berisi **SIAKAD** dan **Animate Ikebukuro**, sebuah toko merchandise anime.

Setiap aplikasi ditempatkan di folder dan port yang berbeda. Ketiganya memakai **SQL Server** sebagai basis data, tetapi masing-masing mempunyai database sendiri agar data dan proses demo tidak bercampur.

| Latihan | Aplikasi | Folder | URL lokal | Database |
| --- | --- | --- | --- | --- |
| Latihan 1 | Sistem Manajemen Studio Musik | `studio-musik` | `http://localhost:8080` | `StudioMusikDB` |
| Latihan 2.1 | Sistem Informasi Akademik (SIAKAD) | `siakad` | `http://localhost:8081` | `SIAKADDB` |
| Latihan 2.2 | Animate Ikebukuro — Anime Merchandise Store | `animate-ikebukuro` | `http://localhost:8082` | `AnimateIkebukuroDB` |

## Daftar Isi

- [Sekilas CodeIgniter dan MVC](#sekilas-codeigniter-dan-mvc)
- [Lingkungan dan Persiapan](#lingkungan-dan-persiapan)
- [Menyiapkan Database](#menyiapkan-database)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Fitur Utama](#fitur-utama)
- [Akun Demo](#akun-demo)
- [Model Data](#model-data)
- [Dokumentasi Tampilan](#dokumentasi-tampilan)
- [Struktur Folder](#struktur-folder)

## Sekilas CodeIgniter dan MVC

CodeIgniter membagi tanggung jawab aplikasi menjadi tiga bagian. **Model** mengelola data dan interaksi dengan tabel. **View** menyusun tampilan yang dilihat pengguna. **Controller** menerima request, memanggil model, lalu memilih view atau proses berikutnya. Rute menghubungkan URL dengan controller, sedangkan filter membatasi halaman sesuai status login dan role pengguna.

Ketiga aplikasi mengikuti pola tersebut. Formulir POST juga menggunakan perlindungan CSRF dari CodeIgniter. Tampilan menggunakan Bootstrap yang disimpan lokal di folder `public/assets`, sehingga halaman tidak bergantung pada CDN.

## Lingkungan dan Persiapan

Perangkat yang diperlukan:

- PHP 8.2 atau lebih baru, Composer, dan ekstensi PHP untuk CodeIgniter seperti `intl` dan `mbstring`.
- Ekstensi PHP `sqlsrv` dan Microsoft ODBC Driver for SQL Server.
- SQL Server lokal yang berjalan dan dapat diakses melalui port `1433`.
- Visual Studio Code dengan extension **SQL Server (mssql)** untuk menjalankan skrip T-SQL.

Jika folder `vendor` belum tersedia, buka terminal pada masing-masing folder proyek dan jalankan:

```powershell
composer install
```

Setiap aplikasi memakai `.env` lokal yang tidak dimasukkan ke Git. Saat menyiapkan salinan baru, buat file `.env` dari contoh yang tersedia:

```powershell
# Studio Musik: jalankan dari folder studio-musik
Copy-Item env .env

# SIAKAD dan Animate Ikebukuro: jalankan dari folder masing-masing
Copy-Item .env.example .env
```

Periksa `app.baseURL`, nama database, host SQL Server, port, dan metode autentikasi pada `.env`. Konfigurasi proyek ini menggunakan SQL Server lokal dan port `1433`; username dan password dapat dibiarkan kosong jika koneksi SQL Server menggunakan Windows Authentication. File `.env` berisi konfigurasi lokal dan tidak boleh dibagikan. Ketiga aplikasi juga memakai nama cookie session yang berbeda dan menyimpan file session di direktori temporary sistem operasi, di luar folder proyek OneDrive.

## Menyiapkan Database

Buat tiga database berikut di SQL Server. Database dapat dibuat melalui Object Explorer extension MSSQL atau query editor:

```sql
CREATE DATABASE StudioMusikDB;
CREATE DATABASE SIAKADDB;
CREATE DATABASE AnimateIkebukuroDB;
```

### Studio Musik

Buka terminal di `studio-musik`, pastikan `.env` menunjuk ke `StudioMusikDB`, lalu jalankan migration dan data demo:

```powershell
php spark migrate
php spark db:seed DatabaseSeeder
```

Migration membuat tabel akun, studio, alat, dan booking. Seeder memasukkan akun demo serta contoh studio dan alat.

### SIAKAD

Buka `siakad/Database/siakad.sql` di VS Code. Sambungkan editor SQL ke server, pilih database `SIAKADDB` melalui **MS SQL: Change Database**, lalu jalankan seluruh skrip dengan `Ctrl+Shift+E`. Pastikan `SELECT DB_NAME();` menghasilkan `SIAKADDB` sebelum menjalankan skrip. Skrip membuat tabel dan data awal.

### Animate Ikebukuro

Buka `animate-ikebukuro/Database/animate_ikebukuro.sql`, pilih database `AnimateIkebukuroDB` pada editor MSSQL, lalu jalankan seluruh skrip. Skrip membuat tabel toko, kategori, produk, dan pesanan beserta data demo.

SIAKAD dan Animate Ikebukuro menggunakan skrip T-SQL langsung; folder migration keduanya belum digunakan untuk membuat skema. Karena itu, inisialisasi tabel untuk dua aplikasi tersebut dilakukan melalui file SQL di folder `Database`.

## Menjalankan Aplikasi

Jalankan perintah berikut dari terminal terpisah. Biarkan tiap server tetap aktif selama aplikasi dibuka di browser.

```powershell
cd "Latihan 5 - Pertemuan 9/studio-musik"
php spark serve --port 8080
```

```powershell
cd "Latihan 5 - Pertemuan 9/siakad"
php spark serve --port 8081
```

```powershell
cd "Latihan 5 - Pertemuan 9/animate-ikebukuro"
php spark serve --port 8082
```

Buka URL yang sesuai dari tabel aplikasi di bagian [Deskripsi](#deskripsi). Jika port sedang digunakan, hentikan server yang memakainya atau pilih port lain dan sesuaikan `app.baseURL` pada `.env`.

## Fitur Utama

### Latihan 1 — Sistem Manajemen Studio Musik

Aplikasi ini membantu pelanggan melihat studio, melakukan booking, dan memantau status pemesanan. Admin mengelola data studio, alat sewa, dan booking.

- Registrasi, login, dan akses berdasarkan role admin atau pelanggan.
- Beranda menampilkan pilihan studio beserta foto, harga, dan kapasitas; tersedia pula daftar alat sewa.
- Pelanggan membuat booking studio, mengunggah bukti pembayaran, melihat booking, atau membatalkan booking sesuai status.
- Admin mengelola studio dan peralatan melalui fitur CRUD, serta mengonfirmasi, menyelesaikan, atau membatalkan booking.
- Foto studio dan bukti pembayaran ditangani melalui upload file.
- Tampilan responsif dengan Bootstrap dan CSS kustom.

### Latihan 2.1 — SIAKAD

Aplikasi akademik memiliki tiga role: admin, dosen, dan mahasiswa.

- **Admin:** CRUD program studi, mahasiswa, dosen, mata kuliah, dan kelas; admin juga dapat mengatur dosen wali mahasiswa.
- **Dosen:** melihat kelas yang diampu, memasukkan nilai mahasiswa, dan melihat daftar mahasiswa perwalian.
- **Mahasiswa:** melihat profil, mengisi FRS dengan batas SKS, melihat nilai per semester dan IPK, serta mencetak FRS dan transkrip.
- Rute halaman dibatasi dengan filter role.

### Latihan 2.2 — Animate Ikebukuro

Toko merchandise anime dengan palet biru, putih, dan aksen oranye yang terinspirasi dari suasana *Blue Box*.

- Pengunjung melihat, mencari, dan memfilter produk berdasarkan kategori, lalu membuka detail produk.
- Pelanggan membuat akun, mengelola keranjang, checkout, dan melihat riwayat pesanan.
- Admin mengelola produk beserta foto, kategori, stok, dan status pesanan.
- Checkout menyimpan detail pesanan dan memperbarui stok.
- Halaman toko, login, daftar, keranjang, serta panel admin menggunakan komponen visual yang konsisten.

## Akun Demo

Gunakan akun berikut setelah menjalankan skrip atau seeder database yang sesuai.

| Aplikasi | Role | Email | Kata sandi |
| --- | --- | --- | --- |
| Studio Musik | Admin | `admin@studio.test` | `Admin123!` |
| Studio Musik | Pelanggan | `pelanggan@studio.test` | `Pelanggan123!` |
| SIAKAD | Admin | `admin@siakad.test` | `Admin123!` |
| SIAKAD | Dosen | `dosen@siakad.test` | `Dosen123!` |
| SIAKAD | Mahasiswa | `mahasiswa@siakad.test` | `Mahasiswa123!` |
| SIAKAD | Mahasiswa demo kedua | `mahasiswa2@siakad.test` | `Mahasiswa123!` |
| Animate Ikebukuro | Admin | `admin@animate.test` | `Admin123!` |
| Animate Ikebukuro | Pelanggan | `belanja@animate.test` | `Belanja123!` |

## Model Data

Hubungan utama antartabel pada tiap aplikasi:

```mermaid
erDiagram
    USERS ||--o{ BOOKINGS : membuat
    STUDIOS ||--o{ BOOKINGS : dipesan
    EQUIPMENTS {
        int id PK
        string name
        string category
        int stock
        decimal rental_price
    }
```

```mermaid
erDiagram
    PROGRAM_STUDI ||--o{ DOSEN : menaungi
    PROGRAM_STUDI ||--o{ MAHASISWA : menaungi
    DOSEN ||--o{ MAHASISWA : wali
    MATA_KULIAH ||--o{ KELAS : dibuka
    DOSEN ||--o{ KELAS : mengampu
    MAHASISWA ||--o{ FRS_DETAIL : mengambil
    KELAS ||--o{ FRS_DETAIL : dipilih
    USERS ||--o| DOSEN : akun
    USERS ||--o| MAHASISWA : akun
```

```mermaid
erDiagram
    USERS ||--o{ ORDERS : membuat
    CATEGORIES ||--o{ PRODUCTS : mengelompokkan
    ORDERS ||--|{ ORDER_ITEMS : berisi
    PRODUCTS ||--o{ ORDER_ITEMS : dipesan
```

Tabel `equipments` menyimpan stok dan tarif sewa alat. Pada SIAKAD, `frs_detail` menghubungkan mahasiswa dengan kelas dan menyimpan nilai. Pada toko, `order_items` menyimpan rincian produk dalam pesanan.

## Dokumentasi Tampilan

### Studio Musik

- **Beranda dan pilihan studio:** 

![beranda](Documentation/image.png)
![beranda-studiocard](Documentation/studiocard.png)
![beranda-rentitem](Documentation/rente.png)

- **Booking pelanggan:** 

![halamanBooking](Documentation/bookprocess.png)
![mengajukanBooking](Documentation/otwbook.png)
![bookselesai](Documentation/donebook.png)

- **Panel admin:** 

![dashboard](Documentation/dashboardmin.png)
![kelolastudio](Documentation/kelolastudio.png)
![rename studio a jadi studio 1](Documentation/renamestud.png)
![namastudio berhasil diedit](Documentation/studedited.png)
![memverifikasibooking](Documentation/setujuibooking.png)
![bookselesai](Documentation/bookdone.png)

### SIAKAD

- **Halaman Login:**

![login menu](Documentation/loginakad.png)

- **Halaman admin:** 

![tampilan dashboard admin](Documentation/dashboardminakad.png)
![menambah willy marcelius](Documentation/addstudent.png)
![mengganti nama dosen](Documentation/renamelecturer.png)
![menambah mata kuliah](Documentation/addmatkul.png)
![menambah prodi ti](Documentation/addmajor.png)
![menambah kuota kelas](Documentation/addquota.png)

- **Halaman dosen:** 

![tampilan dashboard dosen](Documentation/dashboardlecturer.png)
![tampilan kelas yang diampu](Documentation/class.png)
![kelola nilai](Documentation/grade.png)
![tampilan perwalian](Documentation/studedited.png)

- **Halaman mahasiswa:** 

![tampilan dashboard mahasiswa](Documentation/dashboardstudent.png)
![ambil matkul FRS](Documentation/frs.png)
![melihat IPK](Documentation/ipk.png)
![mencetak transkrip](Documentation/cetak.png)
![transkrip bisa dicetak](Documentation/printing.png)

### Animate Ikebukuro

- **Tampilan Awal dan Toko publik:** 

![beranda1](Documentation/beranda.png)
![beranda2](Documentation/beranda2.png)
![beranda3](Documentation/beranda3.png)
![beranda4](Documentation/beranda4.png)

- **Keranjang dan checkout:** 

![memilih barang](Documentation/choose.png)
![tampilan keranjang belanja](Documentation/keranjang.png)
![checkoutpesanan](Documentation/co.png)
![pesanan ditambahkan](Documentation/codone.png)

- **Panel admin:** 

![dashboard admin](Documentation/dash.png)
![mengelola produk](Documentation/product.png)
![mengganti nama produk](Documentation/updated.png)
![mengelola kategori produk](Documentation/category.png)
![melihat pesanan](Documentation/melihatpesanan.png)
![mengganti status pesanan](Documentation/status.png)
![status terganti](Documentation/done.png)

## Struktur Folder

```text
Latihan 5 - Pertemuan 9/
├── README.md
├── .gitignore
├── studio-musik/
│   ├── app/                 # MVC, konfigurasi, migration, dan seeder
│   ├── public/              # Entry point, CSS, gambar, dan upload publik
│   └── writable/            # Cache, log, session, dan file privat
├── siakad/
│   ├── app/                 # MVC dan konfigurasi CodeIgniter
│   ├── Database/siakad.sql  # Skema dan data awal SQL Server
│   └── public/              # Entry point dan aset tampilan
└── animate-ikebukuro/
    ├── app/                 # MVC dan konfigurasi CodeIgniter
    ├── Database/            # Skema dan data awal SQL Server
    └── public/              # Entry point, aset, dan gambar produk
```
