<?php
declare(strict_types=1);

/**
 * USER DEMO — sementara, sampai database/UserModel dibuat.
 * Password disimpan sebagai hash bcrypt (diverifikasi dengan password_verify).
 * Dev: admin/admin123, siti/123456, budi/123456.
 */
return [
    'admin' => ['id' => 'u3', 'name' => 'Admin Adi Laundry', 'username' => 'admin', 'role' => 'admin',    'subtitle' => 'Admin dan pemilik', 'active' => true, 'must_change_pass' => false, 'hash' => '$2b$12$lCe9I7NhuflNTQ3mp84j6OWVRCRjFA6U2XExgyPGCrtDV45Eq9rzi'],
    'siti'  => ['id' => 'u1', 'name' => 'Siti Aminah',       'username' => 'siti',  'role' => 'kasir',    'subtitle' => 'Kasir meja depan',  'active' => true, 'must_change_pass' => false, 'hash' => '$2b$12$amjoZ6qZVipV3UES9Xk0t.DmUxXIo5PGWfJzYIP6Xmfohnt7gVtdO'],
    'budi'  => ['id' => 'u2', 'name' => 'Budi Santoso',      'username' => 'budi',  'role' => 'produksi', 'subtitle' => 'Pegawai produksi',  'active' => true, 'must_change_pass' => true,  'hash' => '$2b$12$amjoZ6qZVipV3UES9Xk0t.DmUxXIo5PGWfJzYIP6Xmfohnt7gVtdO'],
];
