
USE zora;

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
    /* =====================================
       1. SMARTPHONES
       ===================================== */

    SELECT
        'smartphones' AS category_slug,
        'Nova Phone Pro' AS name,
        'nova-phone-pro' AS slug,
        'ZOR-PHN-002' AS sku,
        'Smartphone premium dengan kamera berkualitas tinggi dan performa responsif.' AS description,
        7499000 AS price,
        7999000 AS compare_at_price,
        20 AS stock,
        'Silver' AS color,
        'POPULAR' AS tag,
        'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?auto=format&fit=crop&w=900&q=85' AS image_url,
        0 AS is_featured

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

    /* =====================================
       2. COMPUTERS
       ===================================== */

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

    /* =====================================
       3. AUDIO
       ===================================== */

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

    /* =====================================
       4. WEARABLES
       ===================================== */

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

    /* =====================================
       5. CAMERAS
       ===================================== */

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

    /* =====================================
       6. ACCESSORIES
       ===================================== */

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
