/* Booking Kampus: skema database dan data awal.
   Dijalankan otomatis oleh aplikasi saat pertama kali dibuka, aman diulang (idempotent). */

IF OBJECT_ID('dbo.Users','U') IS NULL
CREATE TABLE dbo.Users (
    UserId       INT IDENTITY(1,1) CONSTRAINT PK_Users PRIMARY KEY,
    Username     NVARCHAR(50)  NOT NULL CONSTRAINT UQ_Users_Username UNIQUE,
    PasswordHash NVARCHAR(100) NOT NULL,
    Nama         NVARCHAR(100) NOT NULL,
    Email        NVARCHAR(100) NULL,
    [Role]       NVARCHAR(20)  NOT NULL CONSTRAINT CK_Users_Role CHECK ([Role] IN ('Admin','Pemohon')),
    IsActive     BIT NOT NULL CONSTRAINT DF_Users_IsActive DEFAULT 1
);
GO

IF OBJECT_ID('dbo.Fasilitas','U') IS NULL
CREATE TABLE dbo.Fasilitas (
    FasilitasId INT IDENTITY(1,1) CONSTRAINT PK_Fasilitas PRIMARY KEY,
    Nama        NVARCHAR(100) NOT NULL CONSTRAINT UQ_Fasilitas_Nama UNIQUE,
    Deskripsi   NVARCHAR(200) NULL,
    IsActive    BIT NOT NULL CONSTRAINT DF_Fasilitas_IsActive DEFAULT 1
);
GO

IF OBJECT_ID('dbo.Ruangan','U') IS NULL
CREATE TABLE dbo.Ruangan (
    RuanganId INT IDENTITY(1,1) CONSTRAINT PK_Ruangan PRIMARY KEY,
    Kode      NVARCHAR(20)  NOT NULL CONSTRAINT UQ_Ruangan_Kode UNIQUE,
    Nama      NVARCHAR(100) NOT NULL,
    Gedung    NVARCHAR(50)  NOT NULL,
    Lantai    INT NOT NULL CONSTRAINT CK_Ruangan_Lantai CHECK (Lantai BETWEEN 0 AND 50),
    Kapasitas INT NOT NULL CONSTRAINT CK_Ruangan_Kapasitas CHECK (Kapasitas > 0),
    Deskripsi NVARCHAR(200) NULL,
    IsActive  BIT NOT NULL CONSTRAINT DF_Ruangan_IsActive DEFAULT 1
);
GO

IF OBJECT_ID('dbo.RuanganFasilitas','U') IS NULL
CREATE TABLE dbo.RuanganFasilitas (
    RuanganId   INT NOT NULL CONSTRAINT FK_RF_Ruangan   FOREIGN KEY REFERENCES dbo.Ruangan(RuanganId)     ON DELETE CASCADE,
    FasilitasId INT NOT NULL CONSTRAINT FK_RF_Fasilitas FOREIGN KEY REFERENCES dbo.Fasilitas(FasilitasId) ON DELETE CASCADE,
    CONSTRAINT PK_RuanganFasilitas PRIMARY KEY (RuanganId, FasilitasId)
);
GO

IF OBJECT_ID('dbo.Booking','U') IS NULL
CREATE TABLE dbo.Booking (
    BookingId       INT IDENTITY(1,1) CONSTRAINT PK_Booking PRIMARY KEY,
    UserId          INT NOT NULL CONSTRAINT FK_Booking_User    FOREIGN KEY REFERENCES dbo.Users(UserId),
    RuanganId       INT NOT NULL CONSTRAINT FK_Booking_Ruangan FOREIGN KEY REFERENCES dbo.Ruangan(RuanganId),
    Tanggal         DATE NOT NULL,
    JamMulai        TIME(0) NOT NULL,
    JamSelesai      TIME(0) NOT NULL,
    Keperluan       NVARCHAR(200) NOT NULL,
    [Status]        NVARCHAR(20) NOT NULL CONSTRAINT DF_Booking_Status DEFAULT 'Menunggu'
                    CONSTRAINT CK_Booking_Status CHECK ([Status] IN ('Menunggu','Disetujui','Ditolak','Dibatalkan')),
    CatatanApprover NVARCHAR(200) NULL,
    CreatedAt       DATETIME2 NOT NULL CONSTRAINT DF_Booking_CreatedAt DEFAULT SYSDATETIME(),
    CONSTRAINT CK_Booking_Jam CHECK (JamSelesai > JamMulai)
);
GO

IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'IX_Booking_Ruangan_Tanggal')
CREATE INDEX IX_Booking_Ruangan_Tanggal ON dbo.Booking (RuanganId, Tanggal);
GO

IF OBJECT_ID('dbo.BookingFasilitas','U') IS NULL
CREATE TABLE dbo.BookingFasilitas (
    BookingId   INT NOT NULL CONSTRAINT FK_BF_Booking   FOREIGN KEY REFERENCES dbo.Booking(BookingId) ON DELETE CASCADE,
    FasilitasId INT NOT NULL CONSTRAINT FK_BF_Fasilitas FOREIGN KEY REFERENCES dbo.Fasilitas(FasilitasId),
    CONSTRAINT PK_BookingFasilitas PRIMARY KEY (BookingId, FasilitasId)
);
GO

/* Data contoh untuk demo. Boleh diubah atau dihapus lewat aplikasi. */
IF NOT EXISTS (SELECT 1 FROM dbo.Fasilitas)
INSERT dbo.Fasilitas (Nama, Deskripsi) VALUES
 (N'Proyektor',    N'Proyektor dan layar'),
 (N'Papan Tulis',  N'Papan tulis putih beserta spidol'),
 (N'AC',           N'Pendingin ruangan'),
 (N'Sound System', N'Pengeras suara dan mikrofon'),
 (N'Wi-Fi',        N'Akses internet nirkabel'),
 (N'Komputer',     N'Komputer untuk praktikum');
GO

IF NOT EXISTS (SELECT 1 FROM dbo.Ruangan)
INSERT dbo.Ruangan (Kode, Nama, Gedung, Lantai, Kapasitas, Deskripsi) VALUES
 (N'R-101',  N'Ruang Kelas 101',         N'Gedung A', 1, 40,  N'Data contoh untuk demo'),
 (N'LAB-01', N'Laboratorium Komputer 1', N'Gedung B', 2, 30,  N'Data contoh untuk demo'),
 (N'AUD-01', N'Aula Utama',              N'Gedung C', 1, 200, N'Data contoh untuk demo');
GO

IF NOT EXISTS (SELECT 1 FROM dbo.RuanganFasilitas)
INSERT dbo.RuanganFasilitas (RuanganId, FasilitasId)
SELECT r.RuanganId, f.FasilitasId
FROM dbo.Ruangan r
JOIN dbo.Fasilitas f ON
     (r.Kode = N'R-101'  AND f.Nama IN (N'Proyektor', N'Papan Tulis', N'AC'))
  OR (r.Kode = N'LAB-01' AND f.Nama IN (N'Komputer', N'Proyektor', N'AC', N'Wi-Fi'))
  OR (r.Kode = N'AUD-01' AND f.Nama IN (N'Proyektor', N'Sound System', N'AC'));
GO
