# 🌍 RiskRadar

> **Sistem Monitoring Risiko Rantai Pasok Global Berbasis Web**

RiskRadar merupakan aplikasi web yang mengintegrasikan data cuaca, ekonomi, nilai tukar mata uang, serta sentimen berita dari berbagai API eksternal untuk menghasilkan **Supply Chain Risk Score** yang mudah dipahami bagi setiap negara.

Aplikasi ini membantu pengguna memantau kondisi global yang berpotensi memengaruhi rantai pasok melalui satu dashboard terintegrasi.


# ✨ Fitur Utama

## 👤 User

Pengguna umum memiliki akses ke berbagai fitur monitoring, antara lain:

* Dashboard ringkasan

  * Cuaca pada empat kota pelabuhan utama
  * Kurs enam mata uang
  * Berita terbaru
  * Statistik pelabuhan

* Halaman informasi:

  * Weather
  * Economy
  * Country
  * Global Country (250+ negara)
  * Currency
  * Port
  * News

* **Risk Score**

  * Menghasilkan skor risiko berdasarkan kondisi cuaca, inflasi, nilai tukar, dan sentimen berita.

* **Compare**

  * Membandingkan tingkat risiko dua atau lebih negara secara berdampingan.

* **Watchlist**

  * Menyimpan negara favorit berbasis session tanpa memerlukan akun tambahan.

* **Profile**

  * Mengelola informasi akun pengguna.

---

## 👨‍💼 Admin

Administrator memiliki seluruh hak akses pengguna ditambah fitur manajemen sistem.

Fitur yang tersedia:

* Dashboard Admin
* Kelola Pengguna (CRUD)
* Kelola Artikel (CRUD)
* Kelola Pelabuhan (CRUD)
* API Monitor
* Audit Log
* Pengaturan Sistem (Key-Value)

---

# 🛠 Teknologi yang Digunakan

| Layer              | Teknologi                        |
| ------------------ | -------------------------------- |
| Bahasa Pemrograman | PHP 8.3                          |
| Framework          | Laravel 13.x                     |
| Authentication     | Laravel Breeze                   |
| Frontend           | Blade, Tailwind CSS 3, Alpine.js |
| Build Tool         | Vite                             |
| Database           | SQLite (Development)             |
| Version Control    | Git                              |
| Testing            | PHPUnit                          |
| Code Style         | Laravel Pint                     |

---

# 🌐 Integrasi API Eksternal

RiskRadar memanfaatkan tujuh layanan eksternal.

| No | API                    | Fungsi                 | Digunakan Pada                  |
| -- | ---------------------- | ---------------------- | ------------------------------- |
| 1  | Open-Meteo API         | Data cuaca real-time   | Dashboard, Weather, Risk Score  |
| 2  | World Bank API         | GDP, Inflasi, Populasi | Economy, Risk Score             |
| 3  | CountriesNow API       | Informasi negara       | Country, Global Country         |
| 4  | ExchangeRate API       | Nilai tukar mata uang  | Dashboard, Currency, Risk Score |
| 5  | GNews API              | Berita internasional   | Dashboard, News, Risk Score     |
| 6  | OpenStreetMap Tile API | Tile peta Leaflet      | Port                            |
| 7  | World Port Index (NGA) | Data pelabuhan dunia   | Sinkronisasi tabel `ports`      |

Semua pemanggilan API menggunakan mekanisme **try-catch**, timeout, dan cache sehingga kegagalan satu layanan tidak menyebabkan seluruh aplikasi berhenti bekerja.

---

# 📊 Cara Kerja Risk Score

Risk score dihitung menggunakan kelas `RiskCalculator` dengan empat komponen utama.

| Komponen               | Bobot | Sumber                          |
| ---------------------- | ----- | ------------------------------- |
| Risiko Cuaca           | 30%   | Open-Meteo API                  |
| Risiko Inflasi         | 20%   | World Bank API                  |
| Risiko Nilai Tukar     | 10%   | ExchangeRate API                |
| Risiko Sentimen Berita | 40%   | GNews API + `SentimentAnalyzer` |

### Klasifikasi Risiko

| Skor     | Level          |
| -------- | -------------- |
| 0 – 34   | 🟢 Low Risk    |
| 35 – 64  | 🟠 Medium Risk |
| 65 – 100 | 🔴 High Risk   |

### Analisis Sentimen

Kelas `SentimentAnalyzer` melakukan analisis sederhana berbasis kata kunci (keyword-based).

Contoh:

**Positif**

* growth
* profit
* stable
* recovery

**Negatif**

* crisis
* disruption
* sanction
* conflict

Selisih jumlah kata positif dan negatif dikonversi menjadi skor sentimen yang selanjutnya dihitung sebagai komponen risiko berita.

---

# 📁 Struktur Folder Proyek

```text
app/
├── Http/
│   ├── Controllers/
│   └── Middleware/
├── Models/
├── Services/
└── Console/
    └── Commands/

database/
├── migrations/
├── factories/
└── seeders/

resources/
├── views/
├── css/
└── js/

routes/
├── web.php
└── auth.php
```

---

# 🗄 Struktur Basis Data

| Tabel              | Deskripsi                |
| ------------------ | ------------------------ |
| users              | Data pengguna dan admin  |
| countries          | Data negara              |
| risk_scores        | Hasil perhitungan risiko |
| ports              | Data pelabuhan           |
| watchlists         | Negara favorit pengguna  |
| news_cache         | Cache berita             |
| articles           | Artikel                  |
| activity_logs      | Audit aktivitas admin    |
| news_categories    | Kategori berita          |
| system_settings    | Pengaturan sistem        |
| risk_alerts        | Notifikasi risiko        |
| currency_history   | Riwayat kurs             |
| sentiment_keywords | Kata kunci sentimen      |
| regional_stats     | Statistik wilayah        |

Sebagian besar tabel indikator menggunakan `country_code` sebagai penghubung karena data berasal dari API eksternal yang dapat berubah sewaktu-waktu.

---

# 🚀 Instalasi

```bash
# Clone Repository
git clone <repository-url>

cd supply-chain-monitor

# Install Dependency
composer install
npm install

# Copy Environment
cp .env.example .env

# Generate Application Key
php artisan key:generate

# Buat Database SQLite
touch database/database.sqlite

# Migration dan Seeder
php artisan migrate --seed

# Sinkronisasi Data Pelabuhan (Opsional)
php artisan ports:sync

# Jalankan Frontend
npm run dev

# atau

npm run build

# Jalankan Server
php artisan serve
```

Akses aplikasi melalui:

```text
http://127.0.0.1:8000
```

Beberapa layanan eksternal memerlukan API Key. Tambahkan konfigurasi pada file `.env` dan `config/services.php`.

---

# 👥 Role Pengguna

| Role  | Hak Akses                                                                               |
| ----- | --------------------------------------------------------------------------------------- |
| user  | Dashboard, Weather, Economy, Country, Currency, Risk Score, Compare, Watchlist, Profile |
| admin | Seluruh hak akses user ditambah panel `/admin`                                          |

Seluruh halaman administrator dilindungi menggunakan `AdminMiddleware`. Pengguna non-admin akan menerima respons **403 Forbidden**.

---

# 🔌 REST API Publik

RiskRadar juga menyediakan endpoint JSON yang dapat digunakan oleh aplikasi lain.

| Method | Endpoint         | Fungsi           |
| ------ | ---------------- | ---------------- |
| GET    | `/api/countries` | Daftar negara    |
| GET    | `/api/risk`      | Data risk score  |
| GET    | `/api/ports`     | Data pelabuhan   |
| GET    | `/api/news`      | Data berita      |
| GET    | `/api/currency`  | Data nilai tukar |

---

# 👨‍💻 Pengembang

**RiskRadar**

Disusun oleh:

**Marlina Yanti br. Tampubolon**
NIM: **240180076**
Program Studi **Sistem Informasi**
Fakultas Teknik
**Universitas Malikussaleh**
