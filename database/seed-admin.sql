INSERT INTO users (
    name,
    email,
    password,
    is_admin,
    is_active,
    created_at,
    updated_at
) VALUES (
    'Admin',
    'admin@admin.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- toto je zahashované 'password'
    1,
    1,
    datetime('now'),
    datetime('now')
);
