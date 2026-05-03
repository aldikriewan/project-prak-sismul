# Project Prak Sismul - CodeIgniter 3 + React

Aplikasi web untuk mengelola cerita (stories) dengan admin panel dan tampilan publik. Backend menggunakan CodeIgniter 3, frontend menggunakan React + Vite.

---

## Struktur Project

```
project-prak-sismul/
├── backend/          # CodeIgniter 3 + API
│   ├── application/
│   │   ├── controllers/
│   │   │   └── Stori.php
│   │   ├── models/
│   │   │   └── Stori_model.php
│   │   └── config/
│   │       ├── database.php
│   │       ├── routes.php
│   │       └── autoload.php
│   ├── storage/images/      # Uploaded images
│   ├── uploads/             # Legacy (not used)
│   ├── index.php
│   ├── router.php
│   ├── .htaccess
│   └── database_setup.sql
├── frontend/         # React + Vite
│   ├── src/
│   │   ├── App.jsx
│   │   ├── pages/
│   │   │   ├── HomePage.jsx
│   │   │   ├── AdminPage.jsx
│   │   │   └── StoryDetailPage.jsx
│   │   └── components/
│   ├── public/
│   ├── package.json
│   └── vite.config.js
└── README.md
```

---

## Prerequisites

- **PHP** >= 7.4 (rekomendasi PHP 8.0+)
- **Composer** (untuk dependencies PHP jika diperlukan)
- **MySQL** / **MariaDB** database server
- **Node.js** >= 16.x + **npm**
- **Git** (opsional)

---

## 1. Setup Database (MySQL)

1. Buka phpMyAdmin atau terminal MySQL
2. Buat database baru:

```sql
CREATE DATABASE books_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. Import schema dari file:

```bash
# Via command line
mysql -u root -p books_backend < backend/database_setup.sql

# Atau import via phpMyAdmin:
# - Buka phpMyAdmin
# - Pilih database "books_backend"
# - Tab "Import"
# - Pilih file: backend/database_setup.sql
# - Klik "Go"
```

---

## 2. Configure Backend (CodeIgniter 3)

### Edit Database Configuration

Buka file: `backend/application/config/database.php`

```php
$db['default'] = array(
    'hostname' => '127.0.0.1',     // Jika MySQL di localhost
    'username' => 'root',           // Username MySQL Anda
    'password' => '',               // Password MySQL Anda
    'database' => 'books_backend',  // Nama database yang dibuat
    'dbdriver' => 'mysqli',
    // ... lainnya tetap sama
);
```

### Base URL (Optional)

Buka: `backend/application/config/config.php`

```php
$config['base_url'] = 'http://localhost:8000/';
```

---

## 3. Jalankan Backend Server

**Method A: PHP Built-in Server (Recommended untuk development)**

```bash
cd backend
php -S localhost:8000 router.php
```

Backend akan berjalan di: **http://localhost:8000**

**Method B: Apache / Nginx**

Jika menggunakan web server lain:
- Document root menuju folder `backend/`
- Pastikan `mod_rewrite` (Apache) aktif
- Pastikan `.htaccess` terbaca

---

## 4. Test API Backend

Setelah server jalan, test endpoints:

```bash
# GET all stories (harus returning empty array jika belum ada data)
curl http://localhost:8000/api/stori

# GET single story
curl http://localhost:8000/api/stori/1
```

Expected response:
```json
{
  "success": true,
  "data": []
}
```

---

## 5. Setup Frontend (React)

```bash
cd frontend

# Install dependencies
npm install

# Jalankan development server
npm run dev
```

Frontend akan berjalan di: **http://localhost:5173** (atau port lain yang ditampilkan di terminal)

---

## 6. Akses Aplikasi

| Halaman | URL | Deskripsi |
|---------|-----|-----------|
| Home | http://localhost:5173/ | Menampilkan daftar cerita (carousel + list) |
| Admin | http://localhost:5173/admin | Panel admin untuk tambah/edit/hapus cerita |
| Detail Story | http://localhost:5173/story/1 | Halaman detail cerita |

---

## 7. File Uploads

- **Upload path**: `backend/storage/images/`
- **Maksimal ukuran**: 2MB per file
- **Format yang diizinkan**: JPEG, JPG, PNG, GIF
- **Storage format**: File disimpan dengan nama terenkripsi (random)

Pastikan folder `backend/storage/images/` memiliki permission **write** (writable):

```bash
# Linux/Mac
chmod -R 755 backend/storage/images/

# Windows (PowerShell sebagai Administrator)
icacls "backend\storage\images" /grant "IIS_IUSRS:(OI)(CI)F"
```

---

## 8. API Endpoints

| Method | Endpoint | Deskripsi | Body Type |
|--------|----------|------------|-----------|
| GET | `/api/stori` | Ambil semua cerita | - |
| GET | `/api/stori/{id}` | Ambil detail cerita | - |
| POST | `/api/stori` | Tambah cerita baru | multipart/form-data |
| POST/PUT | `/api/stori/{id}` | Update cerita | multipart/form-data |
| DELETE | `/api/stori/{id}` | Hapus cerita | - |

### Request Format (POST/PUT)

Form Data:
- `title` (required, string, max:255)
- `author` (required, string, max:255)
- `description` (required, string)
- `story_detail` (required, string)
- `image` (optional, file, max:2MB)
- `background_image` (optional, file, max:2MB)

### Response Format

Success:
```json
{
  "success": true,
  "message": "Story successfully created",
  "data": { /* story object */ }
}
```

Error:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": { /* validation errors */ }
}
```

---

## 9. Troubleshooting

### CORS Issues
Backend sudah include CORS headers di controller `Stori.php`. Pastikan backend berjalan di port **8000**.

### Upload Tidak Berhasil
1. Cek folder `backend/storage/images/` exists dan writable
2. Cek `php.ini` setting:
   - `upload_max_filesize = 2M`
   - `post_max_size = 8M`
   - `file_uploads = On`

### Database Connection Error
- Pastikan MySQL service berjalan
- Cek kredensial di `application/config/database.php`
- Pastikan database `books_backend` sudah dibuat

### 404 Not Found di CI3
- Pastikan `.htaccess` di `backend/` aktif (Apache dengan mod_rewrite)
- Atau gunakan PHP built-in server dengan `router.php`

### Frontend Tidak Bisa Connects ke Backend
Frontend React menjalankan di port 5173, backend di 8000. Pastikan:
- Backend **sudah** berjalan sebelum frontend
- CORS headers sudah diset (sudah otomatis)
- URL di frontend `AdminPage.jsx` sesuai: `http://localhost:8000/api/stori`

---

## 10. Development Tips

### View Logs
- **CI3 Logs**: `backend/application/logs/` (jika `log_threshold` diset > 0)
- Set di `application/config/config.php`:
  ```php
  $config['log_threshold'] = 1; // Log errors only
  ```

### Reset Database
Hapus semua data di tabel `storis` atau drop & recreate table:

```sql
TRUNCATE TABLE storis;
```

### Test Manual dengan Postman/Insomnia
- **POST** `http://localhost:8000/api/stori`
- Body: `form-data`
- Tambahkan field: title, author, description, story_detail
- Tambahkan 2 file: image, background_image

---

## 11. Deployment (Production)

1. **Backend**:
   - Set `ENVIRONMENT` ke `'production'` di `index.php`
   - Set `$config['log_threshold'] = 0;` (matikan logging)
   - Ganti `$config['base_url']` dengan domain Anda
   - Disable `display_errors` di PHP
   - Gunakan Apache/Nginx, bukan PHP built-in server

2. **Frontend**:
   - Build: `npm run build`
   - Deploy folder `frontend/dist/` ke hosting static atau sama dengan backend

3. **Database**:
   - Import `backend/database_setup.sql` ke production DB
   - Update kredensial DB di production

4. **Permissions**:
   - `storage/images/` harus writable oleh web server

---

## 12. File Penting yang Telah Diubah

| File | Perubahan |
|------|-----------|
| `backend/application/controllers/Stori.php` | Controller utama API (CRUD + upload) |
| `backend/application/models/Stori_model.php` | Model untuk akses tabel `storis` |
| `backend/application/config/routes.php` | Routes untuk `/api/stori` |
| `backend/application/config/autoload.php` | Auto-load libraries: database, session, upload, form_validation |
| `backend/.htaccess` | Rewrite rule untuk clean URL |
| `frontend/src/pages/StoryDetailPage.jsx` | Update API endpoint ke CI3 |
| `frontend/src/pages/AdminPage.jsx` | Method update menggunakan POST dengan `_method=PUT` |

---

## 13. Quick Start Commands

```bash
# 1. Setup database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS books_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p books_backend < backend/database_setup.sql

# 2. Run backend (terminal 1)
cd backend
php -S localhost:8000 router.php

# 3. Run frontend (terminal 2)
cd frontend
npm install
npm run dev
```

---

## Notes

- Backend menggunakan **CodeIgniter 3.1.13** (ter-stabil)
- API menerima **multipart/form-data** untuk upload gambar
- CORS sudah di-enable (`*`) untuk development
- Tidak menggunakan authentication (bisa ditambahkan jika diperlukan)
- Frontend tetap **React** dengan API base URL `http://localhost:8000`

---

**Development selesai. Aplikasi siap digunakan.**
