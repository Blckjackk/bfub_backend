# API Documentation - Token Controller

## Endpoint untuk mendapatkan token semua peserta

### GET /api/token-peserta

**Deskripsi**: Mendapatkan semua peserta beserta token-tokennya

**Response Format**:
```json
{
  "success": true,
  "message": "Data token peserta berhasil diambil",
  "data": [
    {
      "peserta_id": 1,
      "nama_peserta": "Azzam Ghifari",
      "nomor_pendaftaran": "MAT-001",
      "asal_sekolah": "SMA Negeri 1 Jakarta",
      "email": "azzam@example.com",
      "cabang_lomba": {
        "id": 1,
        "nama_cabang": "Matematika"
      },
      "jumlah_token": 2,
      "tokens": [
        {
          "id": 1,
          "kode_token": "MAT-TOKEN-001",
          "tipe": "utama",
          "status_token": "aktif",
          "created_at": "2025-07-16 10:00:00",
          "expired_at": "2025-08-01 10:00:00"
        },
        {
          "id": 2,
          "kode_token": "MAT-TOKEN-002",
          "tipe": "cadangan",
          "status_token": "aktif",
          "created_at": "2025-07-16 10:00:00",
          "expired_at": "2025-08-01 10:00:00"
        }
      ]
    }
  ]
}
```

## Endpoint untuk mencari token berdasarkan nama peserta

### GET /api/token-peserta-by-name?nama_peserta=azzam

**Deskripsi**: Mencari peserta berdasarkan nama dan menampilkan token-tokennya

**Parameters**:
- `nama_peserta` (string, required): Nama peserta yang dicari (tidak case sensitive)

**Response Format**:
```json
{
  "success": true,
  "message": "Data token peserta berhasil ditemukan",
  "data": [
    {
      "peserta_id": 1,
      "nama_peserta": "Azzam Ghifari",
      "nomor_pendaftaran": "MAT-001",
      "asal_sekolah": "SMA Negeri 1 Jakarta",
      "email": "azzam@example.com",
      "cabang_lomba": {
        "id": 1,
        "nama_cabang": "Matematika"
      },
      "jumlah_token": 2,
      "tokens": [
        {
          "id": 1,
          "kode_token": "MAT-TOKEN-001",
          "tipe": "utama",
          "status_token": "aktif",
          "created_at": "2025-07-16 10:00:00",
          "expired_at": "2025-08-01 10:00:00"
        },
        {
          "id": 2,
          "kode_token": "MAT-TOKEN-002",
          "tipe": "cadangan",
          "status_token": "aktif",
          "created_at": "2025-07-16 10:00:00",
          "expired_at": "2025-08-01 10:00:00"
        }
      ]
    }
  ]
}
```

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Nama peserta harus diisi"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Peserta dengan nama tersebut tidak ditemukan"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Terjadi kesalahan saat mengambil data token peserta",
  "error": "Detail error message"
}
```

## Contoh Penggunaan

### Menggunakan cURL

```bash
# Mendapatkan semua token peserta
curl -X GET http://localhost:8000/api/token-peserta

# Mencari token berdasarkan nama peserta
curl -X GET "http://localhost:8000/api/token-peserta-by-name?nama_peserta=azzam"
```

### Menggunakan JavaScript (Fetch API)

```javascript
// Mendapatkan semua token peserta
fetch('/api/token-peserta')
  .then(response => response.json())
  .then(data => {
    console.log(data);
    // Menampilkan peserta dan tokennya
    data.data.forEach(peserta => {
      console.log(`${peserta.nama_peserta} memiliki ${peserta.jumlah_token} token:`);
      peserta.tokens.forEach(token => {
        console.log(`- ${token.kode_token} (${token.tipe})`);
      });
    });
  });

// Mencari token berdasarkan nama
fetch('/api/token-peserta-by-name?nama_peserta=azzam')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Peserta ditemukan:', data.data);
    } else {
      console.log('Error:', data.message);
    }
  });
```
