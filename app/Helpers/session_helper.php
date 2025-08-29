<?php

use Config\Database;

if (! function_exists('refreshUserSession')) {
    function refreshUserSession($userId)
    {
        $db = Database::connect();

        // Ambil data pegawai + jabatan
        $pegawai = $db->table('m_pegawai p')
                    ->select('p.ID, p.NAMA, p.JENIS_KELAMIN, j.JABATAN')
                    ->join('m_pegawai_jabatan j', 'p.JABATAN_ID = j.ID', 'left')
                    ->where('p.ID', $userId)
                    ->get()
                    ->getRowArray();

        // Ambil profil lembaga
        $lembaga = $db->table('pengaturan_profil')
                    ->where('ID', 1)
                    ->get()
                    ->getRowArray();

        // Set ulang session
        session()->set([
            'user_id'       => $pegawai['ID'],
            'nama'          => $pegawai['NAMA'],
            'login'         => true,
            'jenis_kelamin' => $pegawai['JENIS_KELAMIN'] ?? 'L',
            'jabatan'       => $pegawai['JABATAN'] ?? 'Super Admin',
            'NAMA_LEMBAGA'  => $lembaga['NAMA_LENGKAP'] ?? '',
            'NAMA_SINGKAT'  => $lembaga['NAMA_SINGKAT'] ?? '',
            'ALAMAT'        => $lembaga['ALAMAT'] ?? '',
            'LOGO'          => $lembaga['LOGO'] ?? '',
            'TELP'          => $lembaga['TELP'] ?? '',
            'FAX'           => $lembaga['FAX'] ?? '',
            'EMAIL'         => $lembaga['EMAIL'] ?? '',
        ]);
    }
}