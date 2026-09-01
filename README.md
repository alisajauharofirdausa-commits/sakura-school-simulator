# Sistem Pengelolaan Inventory (REST API + Vanilla JS)

Arsitektur: PHP sebagai REST API (folder `controllers/`) yang mengembalikan JSON,
dikonsumsi oleh frontend `app.js` + halaman HTML (`login.html`, `index.html`).

## Struktur

```
pengelolahan_inventory/
├── config/
│   └── database.php          # Koneksi PDO + header CORS/JSON
├── models/
│   ├── AdminModel.php         # Login & data admin
│   ├── BarangModel.php        # CRUD inventory + join gudang/vendor
│   ├── GudangModel.php        # CRUD storage_unit
│   └── VendorModel.php        # CRUD vendor_supplier
├── controllers/
│   ├── auth.php               # POST=login, GET=cek status, DELETE=logout
│   ├── barang.php             # GET/POST/PUT/DELETE inventory
│   ├── gudang.php             # GET/POST/PUT/DELETE storage_unit
│   └── vendor.php             # GET/POST/PUT/DELETE vendor_supplier
├── views/
│   ├── login.html              # View halaman login
│   ├── index.html              # View dashboard (tab: Stok, Gudang, Vendor)
│   └── app.js                  # Logic frontend (fetch ke ../controllers/*.php)
├── database_inventory.sql     # Skema database (+ kolom password di ADMIN)
└── seed_admin.php             # Jalankan sekali untuk buat admin pertama
```

## Cara Menjalankan

1. Import `database_inventory.sql` ke MySQL (lewat phpMyAdmin atau CLI).
2. Sesuaikan kredensial di `config/database.php` jika perlu (default: `root` tanpa password).
3. Taruh folder ini di `htdocs` (XAMPP) atau `www` (Laragon).
4. Buka `http://localhost/pengelolahan_inventory/seed_admin.php` **sekali** di
   browser untuk membuat admin pertama:
   - Nomor ID: `ADM001`
   - Password: `admin123`
   Setelah itu, **hapus file `seed_admin.php`**.
5. Buka `http://localhost/pengelolahan_inventory/views/login.html`, login pakai
   kredensial di atas → akan diarahkan ke `views/index.html` (dashboard).

## Catatan Perbaikan dari Versi Sebelumnya

- `models/BarangModel.php` sebelumnya kosong — sudah diisi lengkap.
- `controllers/auth.php` sebelumnya query ke kolom `username`/`id_admin`/`nama_lengkap`
  yang **tidak ada** di skema (`ADMIN` cuma punya `nomor_id`, `nama`, `kontak`, `email`).
  Sekarang login memakai `nomor_id` + `password` (password ditambahkan sebagai kolom baru,
  disimpan ter-hash).
- `app.js` sebelumnya mengirim/membaca field `jenis`, `stok`, `nama_vendor`, `nama_lengkap`
  yang tidak sesuai dengan nama kolom asli di database (`jenis_barang`, `kuantitas_stok`,
  `nama`). Sudah diperbaiki semua agar konsisten end-to-end.
- Semua controller sekarang memanggil Model, tidak lagi query PDO langsung di controller
  (pemisahan tanggung jawab / prinsip MVC).
