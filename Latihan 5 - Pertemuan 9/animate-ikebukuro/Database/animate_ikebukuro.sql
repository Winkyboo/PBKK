/* Select AnimateIkebukuroDB in VS Code MSSQL before running this file. */
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
        role VARCHAR(20) NOT NULL CONSTRAINT CK_users_role CHECK (role IN ('admin','customer')),
        created_at DATETIME2 NOT NULL CONSTRAINT DF_users_created DEFAULT (SYSDATETIME())
    );
END;
GO
IF OBJECT_ID(N'dbo.categories', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.categories (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_categories PRIMARY KEY,
        name NVARCHAR(100) NOT NULL CONSTRAINT UQ_categories_name UNIQUE,
        description NVARCHAR(300) NULL
    );
END;
GO
IF OBJECT_ID(N'dbo.products', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.products (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_products PRIMARY KEY,
        category_id INT NOT NULL,
        name NVARCHAR(160) NOT NULL,
        description NVARCHAR(1200) NULL,
        price DECIMAL(12,2) NOT NULL,
        stock INT NOT NULL CONSTRAINT DF_products_stock DEFAULT (0),
        image NVARCHAR(255) NULL,
        is_featured BIT NOT NULL CONSTRAINT DF_products_featured DEFAULT (0),
        created_at DATETIME2 NOT NULL CONSTRAINT DF_products_created DEFAULT (SYSDATETIME()),
        CONSTRAINT CK_products_price CHECK (price >= 0),
        CONSTRAINT CK_products_stock CHECK (stock >= 0),
        CONSTRAINT FK_products_category FOREIGN KEY (category_id) REFERENCES dbo.categories(id)
    );
END;
GO
IF OBJECT_ID(N'dbo.orders', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.orders (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_orders PRIMARY KEY,
        order_number VARCHAR(30) NOT NULL CONSTRAINT UQ_orders_number UNIQUE,
        user_id INT NOT NULL,
        status NVARCHAR(20) NOT NULL CONSTRAINT DF_orders_status DEFAULT (N'Menunggu'),
        total_amount DECIMAL(14,2) NOT NULL,
        shipping_name NVARCHAR(120) NOT NULL,
        shipping_phone NVARCHAR(30) NOT NULL,
        shipping_address NVARCHAR(500) NOT NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_orders_created DEFAULT (SYSDATETIME()),
        CONSTRAINT CK_orders_status CHECK (status IN (N'Menunggu',N'Diproses',N'Dikirim',N'Selesai',N'Dibatalkan')),
        CONSTRAINT FK_orders_user FOREIGN KEY (user_id) REFERENCES dbo.users(id)
    );
END;
GO
IF OBJECT_ID(N'dbo.order_items', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.order_items (
        id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_order_items PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name NVARCHAR(160) NOT NULL,
        quantity INT NOT NULL,
        unit_price DECIMAL(12,2) NOT NULL,
        CONSTRAINT CK_order_items_quantity CHECK (quantity > 0),
        CONSTRAINT FK_order_items_order FOREIGN KEY (order_id) REFERENCES dbo.orders(id),
        CONSTRAINT FK_order_items_product FOREIGN KEY (product_id) REFERENCES dbo.products(id)
    );
    CREATE INDEX IX_orders_user ON dbo.orders(user_id,created_at);
END;
GO

/* Demo accounts: Admin123! and Belanja123! */
IF NOT EXISTS (SELECT 1 FROM dbo.users WHERE email=N'admin@animate.test')
    INSERT dbo.users(name,email,password_hash,role) VALUES(N'Animate Store Admin',N'admin@animate.test','$2y$10$3fZS4GWZ.rGydpqtQlScbe4t.POq5UF8dtrfddSREWQdzecg4Rwv2','admin');
IF NOT EXISTS (SELECT 1 FROM dbo.users WHERE email=N'belanja@animate.test')
    INSERT dbo.users(name,email,password_hash,role) VALUES(N'Pengunjung Animate',N'belanja@animate.test','$2y$10$3jAFMV.TaWOlHSe38oIPXOiVcMdnakGt0TOJGDkCFblxNYl24mESW','customer');
GO
IF NOT EXISTS (SELECT 1 FROM dbo.categories)
    INSERT dbo.categories(name,description) VALUES
        (N'Acrylic & Figures',N'Acrylic stand, figure, dan display collectible.'),
        (N'Apparel',N'Pakaian dan aksesori bertema anime.'),
        (N'Books & Art',N'Artbook, komik, dan publikasi pilihan.'),
        (N'Accessories',N'Keychain, badge, dan koleksi kecil lainnya.');
GO
IF NOT EXISTS (SELECT 1 FROM dbo.products)
BEGIN
    INSERT dbo.products(category_id,name,description,price,stock,is_featured)
    SELECT c.id,v.name,v.description,v.price,v.stock,v.featured
    FROM (VALUES
        (N'Acrylic & Figures',N'Skyline Acrylic Stand',N'Acrylic stand ilustrasi original bergaya musim semi, cocok untuk meja koleksi.',149000,18,1),
        (N'Apparel',N'Blue Court Practice Tee',N'Kaos katun lembut dengan grafis lapangan basket minimalis.',289000,12,1),
        (N'Books & Art',N'After School Artbook',N'Kumpulan ilustrasi original dengan nuansa sekolah dan langit sore.',239000,9,1),
        (N'Accessories',N'Cloud Nine Pin Set',N'Satu set enamel pin kecil untuk tas dan jaket.',89000,24,1),
        (N'Acrylic & Figures',N'Window Seat Mini Figure',N'Figur mini karakter original dengan alas display transparan.',199000,7,0),
        (N'Accessories',N'Weekend Keychain',N'Gantungan kunci akrilik dua sisi dengan ilustrasi original.',69000,30,0)
    ) AS v(category_name,name,description,price,stock,featured)
    JOIN dbo.categories c ON c.name=v.category_name;
END;
GO
