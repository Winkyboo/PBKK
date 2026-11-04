/* Select SIAKADDB in VS Code MSSQL before running this file. */
SET ANSI_NULLS ON;
SET QUOTED_IDENTIFIER ON;
GO

IF OBJECT_ID(N'dbo.users', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.users (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_users PRIMARY KEY,
        name NVARCHAR(120) NOT NULL,
        email NVARCHAR(150) NOT NULL CONSTRAINT UQ_users_email UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(20) NOT NULL,
        CONSTRAINT CK_users_role CHECK (role IN ('admin', 'dosen', 'mahasiswa'))
    );
END;
GO
IF OBJECT_ID(N'dbo.program_studi', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.program_studi (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_program_studi PRIMARY KEY,
        kode VARCHAR(10) NOT NULL CONSTRAINT UQ_program_studi_kode UNIQUE,
        nama NVARCHAR(120) NOT NULL
    );
END;
GO
IF OBJECT_ID(N'dbo.dosen', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.dosen (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_dosen PRIMARY KEY,
        user_id INT NOT NULL CONSTRAINT UQ_dosen_user UNIQUE,
        nip VARCHAR(30) NOT NULL CONSTRAINT UQ_dosen_nip UNIQUE,
        prodi_id INT NOT NULL,
        CONSTRAINT FK_dosen_user FOREIGN KEY (user_id) REFERENCES dbo.users(id),
        CONSTRAINT FK_dosen_prodi FOREIGN KEY (prodi_id) REFERENCES dbo.program_studi(id)
    );
END;
GO
IF OBJECT_ID(N'dbo.mahasiswa', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.mahasiswa (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_mahasiswa PRIMARY KEY,
        user_id INT NOT NULL CONSTRAINT UQ_mahasiswa_user UNIQUE,
        nim VARCHAR(30) NOT NULL CONSTRAINT UQ_mahasiswa_nim UNIQUE,
        prodi_id INT NOT NULL,
        dosen_wali_id INT NULL,
        angkatan SMALLINT NOT NULL,
        CONSTRAINT FK_mahasiswa_user FOREIGN KEY (user_id) REFERENCES dbo.users(id),
        CONSTRAINT FK_mahasiswa_prodi FOREIGN KEY (prodi_id) REFERENCES dbo.program_studi(id),
        CONSTRAINT FK_mahasiswa_wali FOREIGN KEY (dosen_wali_id) REFERENCES dbo.dosen(id)
    );
END;
GO
IF OBJECT_ID(N'dbo.mata_kuliah', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.mata_kuliah (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_mata_kuliah PRIMARY KEY,
        kode VARCHAR(20) NOT NULL CONSTRAINT UQ_mata_kuliah_kode UNIQUE,
        nama NVARCHAR(150) NOT NULL,
        sks TINYINT NOT NULL,
        prodi_id INT NOT NULL,
        CONSTRAINT CK_mata_kuliah_sks CHECK (sks BETWEEN 1 AND 6),
        CONSTRAINT FK_mata_kuliah_prodi FOREIGN KEY (prodi_id) REFERENCES dbo.program_studi(id)
    );
END;
GO
IF OBJECT_ID(N'dbo.kelas', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.kelas (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_kelas PRIMARY KEY,
        mata_kuliah_id INT NOT NULL,
        dosen_id INT NOT NULL,
        semester NVARCHAR(12) NOT NULL,
        tahun_ajaran VARCHAR(9) NOT NULL,
        nama_kelas NVARCHAR(10) NOT NULL,
        kuota SMALLINT NOT NULL DEFAULT (30),
        CONSTRAINT CK_kelas_kuota CHECK (kuota > 0),
        CONSTRAINT FK_kelas_matkul FOREIGN KEY (mata_kuliah_id) REFERENCES dbo.mata_kuliah(id),
        CONSTRAINT FK_kelas_dosen FOREIGN KEY (dosen_id) REFERENCES dbo.dosen(id)
    );
END;
GO
IF OBJECT_ID(N'dbo.frs_detail', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.frs_detail (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_frs_detail PRIMARY KEY,
        mahasiswa_id INT NOT NULL,
        kelas_id INT NOT NULL,
        nilai_angka DECIMAL(5,2) NULL,
        nilai_huruf NVARCHAR(2) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_frs_detail_created DEFAULT (SYSDATETIME()),
        CONSTRAINT UQ_frs_mahasiswa_kelas UNIQUE (mahasiswa_id, kelas_id),
        CONSTRAINT CK_frs_nilai CHECK (nilai_angka IS NULL OR nilai_angka BETWEEN 0 AND 100),
        CONSTRAINT FK_frs_mahasiswa FOREIGN KEY (mahasiswa_id) REFERENCES dbo.mahasiswa(id),
        CONSTRAINT FK_frs_kelas FOREIGN KEY (kelas_id) REFERENCES dbo.kelas(id)
    );
    CREATE INDEX IX_frs_kelas ON dbo.frs_detail(kelas_id);
END;
GO

/* Demo users: admin123, dosen123, mahasiswa123. */
IF NOT EXISTS (SELECT 1 FROM dbo.users WHERE email=N'admin@siakad.test')
    INSERT dbo.users(name,email,password_hash,role) VALUES(N'Administrator SIAKAD',N'admin@siakad.test','$2y$10$3fZS4GWZ.rGydpqtQlScbe4t.POq5UF8dtrfddSREWQdzecg4Rwv2','admin');
IF NOT EXISTS (SELECT 1 FROM dbo.users WHERE email=N'dosen@siakad.test')
    INSERT dbo.users(name,email,password_hash,role) VALUES(N'Dr. Budi Santoso',N'dosen@siakad.test','$2y$10$h35/Yqh4tUwH0q7kgx32F.3CsZxYpsoBHNOubJYmLvxjuOMFWPDKC','dosen');
IF NOT EXISTS (SELECT 1 FROM dbo.users WHERE email=N'mahasiswa@siakad.test')
    INSERT dbo.users(name,email,password_hash,role) VALUES(N'Christina Tan',N'mahasiswa@siakad.test','$2y$10$eXvsfpKduJxUqnEuCoVh5.93qwI5eWF/KLkP/Iv9X2fz7fNeyl6wK','mahasiswa');
IF NOT EXISTS (SELECT 1 FROM dbo.users WHERE email=N'mahasiswa2@siakad.test')
    INSERT dbo.users(name,email,password_hash,role) VALUES(N'Raka Pratama',N'mahasiswa2@siakad.test','$2y$10$eXvsfpKduJxUqnEuCoVh5.93qwI5eWF/KLkP/Iv9X2fz7fNeyl6wK','mahasiswa');
GO

IF NOT EXISTS (SELECT 1 FROM dbo.program_studi WHERE kode='IF') INSERT dbo.program_studi(kode,nama) VALUES('IF',N'Informatika');
IF NOT EXISTS (SELECT 1 FROM dbo.program_studi WHERE kode='SI') INSERT dbo.program_studi(kode,nama) VALUES('SI',N'Sistem Informasi');
GO
DECLARE @if INT=(SELECT id FROM dbo.program_studi WHERE kode='IF');
DECLARE @si INT=(SELECT id FROM dbo.program_studi WHERE kode='SI');
DECLARE @dosenUser INT=(SELECT id FROM dbo.users WHERE email=N'dosen@siakad.test');
IF NOT EXISTS (SELECT 1 FROM dbo.dosen WHERE nip='198501012010011001') INSERT dbo.dosen(user_id,nip,prodi_id) VALUES(@dosenUser,'198501012010011001',@if);
DECLARE @dosen INT=(SELECT id FROM dbo.dosen WHERE nip='198501012010011001');
DECLARE @mhsUser INT=(SELECT id FROM dbo.users WHERE email=N'mahasiswa@siakad.test');
DECLARE @mhs2User INT=(SELECT id FROM dbo.users WHERE email=N'mahasiswa2@siakad.test');
IF NOT EXISTS (SELECT 1 FROM dbo.mahasiswa WHERE nim='5025241001') INSERT dbo.mahasiswa(user_id,nim,prodi_id,dosen_wali_id,angkatan) VALUES(@mhsUser,'5025241001',@if,@dosen,2024);
IF NOT EXISTS (SELECT 1 FROM dbo.mahasiswa WHERE nim='5025241002') INSERT dbo.mahasiswa(user_id,nim,prodi_id,dosen_wali_id,angkatan) VALUES(@mhs2User,'5025241002',@if,@dosen,2024);
IF NOT EXISTS (SELECT 1 FROM dbo.mata_kuliah WHERE kode='IF101') INSERT dbo.mata_kuliah(kode,nama,sks,prodi_id) VALUES('IF101',N'Algoritma dan Pemrograman',3,@if);
IF NOT EXISTS (SELECT 1 FROM dbo.mata_kuliah WHERE kode='IF202') INSERT dbo.mata_kuliah(kode,nama,sks,prodi_id) VALUES('IF202',N'Basis Data',3,@if);
IF NOT EXISTS (SELECT 1 FROM dbo.mata_kuliah WHERE kode='IF305') INSERT dbo.mata_kuliah(kode,nama,sks,prodi_id) VALUES('IF305',N'Rekayasa Perangkat Lunak',3,@if);
IF NOT EXISTS (SELECT 1 FROM dbo.mata_kuliah WHERE kode='IF110') INSERT dbo.mata_kuliah(kode,nama,sks,prodi_id) VALUES('IF110',N'Matematika Diskrit',3,@if);
GO
DECLARE @dosen INT=(SELECT id FROM dbo.dosen WHERE nip='198501012010011001');
IF NOT EXISTS (SELECT 1 FROM dbo.kelas WHERE tahun_ajaran='2026/2027' AND semester=N'Ganjil' AND nama_kelas=N'A')
BEGIN
    INSERT dbo.kelas(mata_kuliah_id,dosen_id,semester,tahun_ajaran,nama_kelas,kuota)
    SELECT id,@dosen,N'Ganjil','2026/2027',N'A',30 FROM dbo.mata_kuliah WHERE kode IN ('IF202','IF305');
END;
IF NOT EXISTS (SELECT 1 FROM dbo.kelas WHERE tahun_ajaran='2025/2026' AND semester=N'Genap')
BEGIN
    INSERT dbo.kelas(mata_kuliah_id,dosen_id,semester,tahun_ajaran,nama_kelas,kuota)
    SELECT id,@dosen,N'Genap','2025/2026',N'A',30 FROM dbo.mata_kuliah WHERE kode IN ('IF101','IF110');
END;
GO
DECLARE @mhs INT=(SELECT id FROM dbo.mahasiswa WHERE nim='5025241001');
IF NOT EXISTS (SELECT 1 FROM dbo.frs_detail WHERE mahasiswa_id=@mhs)
BEGIN
    INSERT dbo.frs_detail(mahasiswa_id,kelas_id,nilai_angka,nilai_huruf)
    SELECT @mhs,k.id,CASE WHEN mk.kode='IF101' THEN 88 ELSE 82 END,CASE WHEN mk.kode='IF101' THEN N'A' ELSE N'A-' END
    FROM dbo.kelas k JOIN dbo.mata_kuliah mk ON mk.id=k.mata_kuliah_id
    WHERE k.tahun_ajaran='2025/2026' AND k.semester=N'Genap';
END;
GO
