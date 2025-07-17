# API Documentation - Sistem Ujian Online

## Base URL
```
http://localhost:8000
```

## Authentication Routes

### 1. Login Peserta
**POST** `/auth/login`

**Request Body:**
```json
{
    "email": "azzam@example.com",
    "password": "peserta123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "peserta": {...},
        "session_token": "abc123..."
    }
}
```

### 2. Register Peserta
**POST** `/auth/register`

**Request Body:**
```json
{
    "nama_lengkap": "Azzam Ghifari",
    "nomor_pendaftaran": "MAT-003",
    "asal_sekolah": "SMA Negeri 1 Jakarta",
    "email": "azzam@example.com",
    "password": "peserta123",
    "cabang_lomba_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "message": "Registrasi berhasil. Anda mendapatkan 5 token.",
    "data": {
        "peserta": {...},
        "tokens": [...]
    }
}
```

### 3. Verifikasi Token
**POST** `/auth/verify-token`

**Request Body:**
```json
{
    "kode_token": "MAT-TOKEN-001-1"
}
```

### 4. Request Token Ulang
**POST** `/auth/request-token-ulang`

**Request Body:**
```json
{
    "peserta_id": 1
}
```

## Peserta Routes

### 5. Data Peserta (Profil)
**GET** `/peserta/me`

**Query Parameters:**
- `peserta_id` (required)

### 6. Update Data Peserta
**PUT** `/peserta/update`

**Request Body:**
```json
{
    "peserta_id": 1,
    "nama_lengkap": "Azzam Ghifari Updated",
    "asal_sekolah": "SMA Negeri 2 Jakarta",
    "email": "azzam.new@example.com"
}
```

### 7. Status Ujian Peserta
**GET** `/peserta/status-ujian`

**Query Parameters:**
- `peserta_id` (required)

## Soal Routes

### 8. Ambil Semua Soal Pilihan Ganda
**GET** `/soal/pg`

**Query Parameters:**
- `cabang_lomba_id` (required)

### 9. Ambil Soal PG Berdasarkan Nomor
**GET** `/soal/pg/{nomor}`

**Query Parameters:**
- `cabang_lomba_id` (required)

### 16. Ambil Soal Essay
**GET** `/soal/essay`

**Query Parameters:**
- `cabang_lomba_id` (required)

## Jawaban Routes

### 10. Kirim Jawaban Pilihan Ganda
**POST** `/jawaban/pg`

**Request Body:**
```json
{
    "peserta_id": 1,
    "soal_id": 1,
    "jawaban_peserta": "B"
}
```

### 11. Ambil Jawaban Peserta
**GET** `/jawaban/pg`

**Query Parameters:**
- `peserta_id` (required)
- `cabang_lomba_id` (required)

### 17. Upload File Essay
**POST** `/jawaban/essay/upload`

**Request Body (multipart/form-data):**
- `peserta_id` (required)
- `soal_essay_id` (required)
- `file` (required, max 10MB, pdf/doc/docx/txt)
- `jawaban_teks` (optional)

### 18. Preview File Essay
**GET** `/jawaban/essay`

**Query Parameters:**
- `peserta_id` (required)
- `soal_essay_id` (required)

### 19. Re-upload File Essay
**PUT** `/jawaban/essay/upload`

Same as POST `/jawaban/essay/upload`

## Ujian Routes

### 12. Mulai Ujian
**POST** `/ujian/mulai`

**Request Body:**
```json
{
    "peserta_id": 1
}
```

### 13. Selesai Ujian
**POST** `/ujian/selesai`

**Request Body:**
```json
{
    "peserta_id": 1
}
```

### 14. Status Ujian
**GET** `/ujian/status`

**Query Parameters:**
- `peserta_id` (required)

### 15. Auto Save Jawaban
**POST** `/ujian/auto-save`

**Request Body:**
```json
{
    "peserta_id": 1,
    "jawaban_data": [
        {
            "soal_id": 1,
            "jawaban_peserta": "A"
        },
        {
            "soal_id": 2,
            "jawaban_peserta": "B"
        }
    ]
}
```

## Admin Routes

### 20. Daftar Peserta
**GET** `/admin/peserta`

**Query Parameters:**
- `cabang` (optional) - Filter by cabang lomba

### 22. Tambah Soal Pilihan Ganda
**POST** `/admin/soal/pg`

**Request Body:**
```json
{
    "cabang_lomba_id": 1,
    "nomor_soal": 1,
    "tipe_soal": "text",
    "deskripsi_soal": "Soal matematika dasar",
    "pertanyaan": "Berapa hasil 2+2?",
    "opsi_a": "3",
    "opsi_b": "4",
    "opsi_c": "5",
    "opsi_d": "6",
    "opsi_e": "7",
    "jawaban_benar": "B"
}
```

### 23. Tambah Soal Essay
**POST** `/admin/soal/essay`

**Request Body:**
```json
{
    "cabang_lomba_id": 1,
    "pertanyaan_essay": "Jelaskan konsep limit dalam matematika!"
}
```

### 24. Lihat Semua Jawaban Peserta
**GET** `/admin/jawaban/peserta`

### 25. Hitung Nilai Otomatis
**GET** `/admin/nilai/otomatis`

### 26. Export ke Excel
**GET** `/admin/export/excel`

### 27. Download Semua File Upload
**GET** `/admin/export/files`

## Additional Routes

### 28. Simulasi Test
**GET** `/simulasi/test`

### 29. Dokumentasi API
**GET** `/dokumentasi`

### 30. Landing Page
**GET** `/`

## Error Responses

All endpoints may return these error responses:

### 400 Bad Request
```json
{
    "success": false,
    "message": "Validation error message"
}
```

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Unauthorized access"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Resource not found"
}
```

### 500 Internal Server Error
```json
{
    "success": false,
    "message": "Internal server error",
    "error": "Detailed error message"
}
```

## Notes

1. **Authentication**: Most endpoints require authentication. Use session tokens or implement JWT.
2. **File Upload**: Use `multipart/form-data` for file uploads.
3. **Database**: Make sure to run migrations and seeders before testing.
4. **CORS**: Configure CORS if accessing from different domains.
5. **Rate Limiting**: Consider implementing rate limiting for production.

## Testing

Test the API using tools like:
- Postman
- cURL
- Frontend applications
- Laravel Tinker

## Database Requirements

Make sure these tables exist and have proper relationships:
- admin
- cabang_lomba
- peserta
- token
- soal
- jawaban
- soal_essay
- jawaban_essay
