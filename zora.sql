
-- =========================================================
-- ZORA ELECTRONICS STORE
-- DATABASE + TABLES + CATEGORIES + 24 PRODUCTS
-- =========================================================

-- 1. CREATE DATABASE
CREATE DATABASE IF NOT EXISTS zora
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE zora;

SET NAMES utf8mb4;


-- =========================================================
-- 2. CREATE TABLE CATEGORIES
-- =========================================================

CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL,
    description TEXT DEFAULT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_slug (slug),
    KEY idx_categories_active_sort (is_active, sort_order)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 3. CREATE TABLE PRODUCTS
-- =========================================================

CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id BIGINT UNSIGNED DEFAULT NULL,

    name VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    sku VARCHAR(80) NOT NULL,
    description TEXT DEFAULT NULL,

    price DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    compare_at_price DECIMAL(14,2) DEFAULT NULL,

    stock INT UNSIGNED NOT NULL DEFAULT 0,

    color VARCHAR(80) DEFAULT NULL,
    tag VARCHAR(30) DEFAULT NULL,
    image_url VARCHAR(500) DEFAULT NULL,

    status ENUM(
        'draft',
        'active',
        'archived'
    ) NOT NULL DEFAULT 'active',

    is_featured TINYINT(1) NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    UNIQUE KEY uq_products_slug (slug),
    UNIQUE KEY uq_products_sku (sku),

    KEY idx_products_category (category_id),
    KEY idx_products_status_featured (status, is_featured),
    KEY idx_products_price (price),

    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 4. INSERT 6 CATEGORIES
-- =========================================================

INSERT INTO categories (
    name,
    slug,
    description,
    sort_order
)
VALUES
(
    'Smartphones',
    'smartphones',
    'Smartphone untuk kebutuhan sehari-hari.',
    1
),
(
    'Computers',
    'computers',
    'Laptop dan tablet untuk kerja serta hiburan.',
    2
),
(
    'Audio',
    'audio',
    'Headphone dan speaker untuk pengalaman audio.',
    3
),
(
    'Wearables',
    'wearables',
    'Smartwatch dan perangkat wearable.',
    4
),
(
    'Cameras',
    'cameras',
    'Kamera untuk mengabadikan momen.',
    5
),
(
    'Accessories',
    'accessories',
    'Aksesori elektronik dan produktivitas.',
    6
)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    sort_order = VALUES(sort_order);


-- =========================================================
-- 5. INSERT 24 PRODUCTS
-- 6 CATEGORIES X 4 PRODUCTS
-- =========================================================

INSERT INTO products (
    category_id,
    name,
    slug,
    sku,
    description,
    price,
    compare_at_price,
    stock,
    color,
    tag,
    image_url,
    status,
    is_featured
)

SELECT
    c.id,
    p.name,
    p.slug,
    p.sku,
    p.description,
    p.price,
    p.compare_at_price,
    p.stock,
    p.color,
    p.tag,
    p.image_url,
    'active',
    p.is_featured

FROM (

    -- =====================================================
    -- CATEGORY 1: SMARTPHONES
    -- =====================================================

    SELECT
        'smartphones' AS category_slug,
        'Nova Phone X' AS name,
        'nova-phone-x' AS slug,
        'ZOR-PHN-001' AS sku,
        'Smartphone dengan desain ringkas, layar jernih, dan performa untuk aktivitas sehari-hari.' AS description,
        4299000 AS price,
        4799000 AS compare_at_price,
        25 AS stock,
        'Midnight Blue' AS color,
        'NEW' AS tag,
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=85' AS image_url,
        1 AS is_featured

    UNION ALL SELECT
        'smartphones',
        'Nova Phone Pro',
        'nova-phone-pro',
        'ZOR-PHN-002',
        'Smartphone premium dengan kamera berkualitas tinggi dan performa responsif.',
        7499000,
        7999000,
        20,
        'Silver',
        'POPULAR',
        'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'smartphones',
        'Nova Phone SE',
        'nova-phone-se',
        'ZOR-PHN-003',
        'Smartphone praktis dengan desain minimalis untuk kebutuhan sehari-hari.',
        3299000,
        3599000,
        30,
        'White',
        'NEW',
        'https://images.unsplash.com/photo-1512499617640-c74ae3a79d37?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'smartphones',
        'Nova Fold',
        'nova-fold',
        'ZOR-PHN-004',
        'Smartphone layar lipat dengan desain modern dan pengalaman multitasking.',
        9999000,
        10999000,
        10,
        'Black',
        'NEW',
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=85',
        0


    -- =====================================================
    -- CATEGORY 2: COMPUTERS
    -- =====================================================

    UNION ALL SELECT
        'computers',
        'AirBook 14',
        'airbook-14',
        'ZOR-LPT-001',
        'Laptop ringan untuk bekerja, belajar, dan menikmati hiburan.',
        8999000,
        9499000,
        12,
        'Silver',
        'POPULAR',
        'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=85',
        1

    UNION ALL SELECT
        'computers',
        'AirBook Pro 16',
        'airbook-pro-16',
        'ZOR-LPT-002',
        'Laptop performa tinggi untuk pekerjaan profesional dan kebutuhan kreatif.',
        14999000,
        15999000,
        10,
        'Space Gray',
        'POPULAR',
        'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'computers',
        'Creator Laptop 15',
        'creator-laptop-15',
        'ZOR-LPT-003',
        'Laptop untuk produktivitas, desain grafis, dan aktivitas multimedia.',
        6499000,
        6999000,
        15,
        'Silver',
        'NEW',
        'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'computers',
        'Tab S Lite',
        'tab-s-lite',
        'ZOR-TAB-001',
        'Tablet serbaguna untuk membaca, mencatat, dan menikmati konten.',
        2899000,
        NULL,
        16,
        'Silver',
        NULL,
        'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=900&q=85',
        0


    -- =====================================================
    -- CATEGORY 3: AUDIO
    -- =====================================================

    UNION ALL SELECT
        'audio',
        'Studio Headphones',
        'studio-headphones',
        'ZOR-AUD-001',
        'Headphone wireless dengan suara seimbang dan bantalan nyaman.',
        1299000,
        NULL,
        30,
        'Navy',
        'POPULAR',
        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=85',
        1

    UNION ALL SELECT
        'audio',
        'Room Speaker Mini',
        'room-speaker-mini',
        'ZOR-AUD-002',
        'Speaker compact untuk ruang kerja atau rumah.',
        899000,
        NULL,
        22,
        'Cloud White',
        NULL,
        'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'audio',
        'Wireless Earbuds Pro',
        'wireless-earbuds-pro',
        'ZOR-AUD-003',
        'Earbuds wireless dengan desain compact dan kualitas suara jernih.',
        1499000,
        1699000,
        35,
        'White',
        'NEW',
        'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'audio',
        'Bluetooth Speaker Go',
        'bluetooth-speaker-go',
        'ZOR-AUD-004',
        'Speaker portable dengan desain minimalis untuk menemani aktivitas.',
        799000,
        899000,
        25,
        'Black',
        NULL,
        'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=900&q=85',
        0


    -- =====================================================
    -- CATEGORY 4: WEARABLES
    -- =====================================================

    UNION ALL SELECT
        'wearables',
        'Motion Watch',
        'motion-watch',
        'ZOR-WAT-001',
        'Jam pintar dengan tampilan minimal dan fitur praktis.',
        1799000,
        1999000,
        18,
        'Black',
        'NEW',
        'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=900&q=85',
        1

    UNION ALL SELECT
        'wearables',
        'Fit Band 2',
        'fit-band-2',
        'ZOR-WAT-002',
        'Smart band ringan untuk mendukung aktivitas dan rutinitas harian.',
        699000,
        799000,
        30,
        'Black',
        'NEW',
        'https://images.unsplash.com/photo-1544117519-31a4b719223d?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'wearables',
        'Motion Watch Pro',
        'motion-watch-pro',
        'ZOR-WAT-003',
        'Smartwatch premium dengan tampilan modern dan fitur pintar.',
        2599000,
        2899000,
        18,
        'Silver',
        'POPULAR',
        'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'wearables',
        'Smart Ring',
        'smart-ring',
        'ZOR-WAT-004',
        'Perangkat wearable berbentuk cincin dengan desain sederhana.',
        1899000,
        NULL,
        12,
        'Titanium',
        'NEW',
        'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?auto=format&fit=crop&w=900&q=85',
        0


    -- =====================================================
    -- CATEGORY 5: CAMERAS
    -- =====================================================

    UNION ALL SELECT
        'cameras',
        'Pocket Camera',
        'pocket-camera',
        'ZOR-CAM-001',
        'Kamera praktis untuk mengabadikan momen sehari-hari.',
        3599000,
        3999000,
        9,
        'Graphite',
        'LIMITED',
        'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'cameras',
        'Mirrorless Camera M1',
        'mirrorless-camera-m1',
        'ZOR-CAM-002',
        'Kamera mirrorless untuk fotografi dan kebutuhan konten profesional.',
        8999000,
        9499000,
        8,
        'Black',
        'POPULAR',
        'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'cameras',
        'Action Camera 4K',
        'action-camera-4k',
        'ZOR-CAM-003',
        'Kamera aksi compact untuk dokumentasi aktivitas dan perjalanan.',
        1899000,
        2199000,
        14,
        'Black',
        'NEW',
        'https://images.unsplash.com/photo-1510127034890-2eea7e4c6a69?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'cameras',
        'Instant Camera',
        'instant-camera',
        'ZOR-CAM-004',
        'Kamera instan dengan desain klasik untuk mengabadikan momen.',
        2499000,
        NULL,
        11,
        'White',
        NULL,
        'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=900&q=85',
        0


    -- =====================================================
    -- CATEGORY 6: ACCESSORIES
    -- =====================================================

    UNION ALL SELECT
        'accessories',
        'Precision Mouse',
        'precision-mouse',
        'ZOR-ACC-001',
        'Mouse ergonomis untuk menunjang produktivitas.',
        349000,
        399000,
        40,
        'White',
        NULL,
        'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'accessories',
        'USB-C Hub 7-in-1',
        'usb-c-hub-7in1',
        'ZOR-ACC-002',
        'Hub USB-C multifungsi untuk menghubungkan berbagai perangkat.',
        899000,
        999000,
        40,
        'Silver',
        'NEW',
        'https://images.unsplash.com/photo-1625842268584-8f3296236761?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'accessories',
        'Mechanical Keyboard Mini',
        'mechanical-keyboard-mini',
        'ZOR-ACC-003',
        'Keyboard mechanical compact untuk produktivitas dan kebutuhan gaming.',
        599000,
        699000,
        25,
        'White',
        'POPULAR',
        'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=85',
        0

    UNION ALL SELECT
        'accessories',
        'Power Bank 20K',
        'power-bank-20k',
        'ZOR-ACC-004',
        'Power bank berkapasitas besar untuk menunjang mobilitas perangkat.',
        349000,
        399000,
        45,
        'Black',
        NULL,
        'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?auto=format&fit=crop&w=900&q=85',
        0

) AS p

INNER JOIN categories c
    ON c.slug = p.category_slug

WHERE NOT EXISTS (
    SELECT 1
    FROM products existing
    WHERE existing.slug = p.slug
       OR existing.sku = p.sku
);


-- =========================================================
-- 6. VERIFY CATEGORY AND PRODUCT COUNTS
-- =========================================================

SELECT
    c.id,
    c.name AS kategori,
    COUNT(p.id) AS jumlah_produk,

    CASE
        WHEN COUNT(p.id) = 4 THEN 'SESUAI'
        ELSE 'PERLU DIPERIKSA'
    END AS status

FROM categories c

LEFT JOIN products p
    ON p.category_id = c.id
    AND p.status = 'active'

WHERE c.is_active = 1

GROUP BY
    c.id,
    c.name,
    c.sort_order

ORDER BY c.sort_order;


-- =========================================================
-- 7. VERIFY ALL PRODUCTS
-- =========================================================

SELECT
    p.id,
    c.name AS kategori,
    p.name AS nama_produk,
    p.sku,
    p.price,
    p.stock,
    p.status

FROM products p

LEFT JOIN categories c
    ON p.category_id = c.id

ORDER BY
    c.sort_order,
    p.name;


-- =========================================================
-- END OF ZORA DATABASE SCRIPT
-- =========================================================
