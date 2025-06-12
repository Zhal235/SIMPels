<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Jabatan;
use Carbon\Carbon;

class PegawaiTambahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pegawai tambahan sesuai formasi yang belum ada
        $pegawaiTambahan = [
            // Wali Kelas dan Guru tambahan
            [
                'nama_pegawai' => 'Lutfi Dhiaulhaq',
                'email' => 'lutfi.dhiaulhaq@salsabila.sch.id',
                'nip' => 'WAL003',
                'jabatan_kode' => 'WALIKELAS',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1989-03-18'
            ],
            [
                'nama_pegawai' => 'Firda Anida Fadilah',
                'email' => 'firda.anida@salsabila.sch.id',
                'nip' => 'WAL004',
                'jabatan_kode' => 'WALIKELAS',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1991-07-22'
            ],
            [
                'nama_pegawai' => 'Sandi Irawan',
                'email' => 'sandi.irawan@salsabila.sch.id',
                'nip' => 'WAL005',
                'jabatan_kode' => 'WALIKELAS',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1988-11-05'
            ],
            [
                'nama_pegawai' => 'Aep Saepudin, S.Pd',
                'email' => 'aep.saepudin.guru@salsabila.sch.id',
                'nip' => 'GUR003',
                'jabatan_kode' => 'GURU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Garut',
                'tanggal_lahir' => '1986-01-10'
            ],
            [
                'nama_pegawai' => 'Zenal Muttakin',
                'email' => 'zenal.muttakin.guru@salsabila.sch.id',
                'nip' => 'GUR004',
                'jabatan_kode' => 'GURU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1992-11-30'
            ],
            [
                'nama_pegawai' => 'Shj Haibah',
                'email' => 'shj.haibah@salsabila.sch.id',
                'nip' => 'GUR005',
                'jabatan_kode' => 'GURU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1990-09-15'
            ],
            [
                'nama_pegawai' => 'Ayu Nursyiroh',
                'email' => 'ayu.nursyiroh@salsabila.sch.id',
                'nip' => 'GUR006',
                'jabatan_kode' => 'GURU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1993-05-28'
            ],
            [
                'nama_pegawai' => 'Dedeh Yustika Basith, S.Pd',
                'email' => 'dedeh.yustika@salsabila.sch.id',
                'nip' => 'GUR007',
                'jabatan_kode' => 'GURU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1987-12-10'
            ],
            [
                'nama_pegawai' => 'Elin Tamaya, S.Pd',
                'email' => 'elin.tamaya.guru@salsabila.sch.id',
                'nip' => 'GUR008',
                'jabatan_kode' => 'GURU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1988-06-28'
            ],
            [
                'nama_pegawai' => 'M Zumhur Alparij',
                'email' => 'zumhur.alparij@salsabila.sch.id',
                'nip' => 'GUR009',
                'jabatan_kode' => 'GURU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1985-04-17'
            ],

            // Operator tambahan
            [
                'nama_pegawai' => 'Sandi Irawan',
                'email' => 'sandi.irawan.operator@salsabila.sch.id',
                'nip' => 'OPR002',
                'jabatan_kode' => 'OPERATOR',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1988-11-05'
            ],
            [
                'nama_pegawai' => 'Sahrul Ramadhan',
                'email' => 'sahrul.ramadhan@salsabila.sch.id',
                'nip' => 'OPR003',
                'jabatan_kode' => 'OPERATOR',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1992-08-12'
            ],
            [
                'nama_pegawai' => 'Abdul Mutholib',
                'email' => 'abdul.mutholib.operator@salsabila.sch.id',
                'nip' => 'OPR004',
                'jabatan_kode' => 'OPERATOR',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1990-06-14'
            ],

            // Musyrif/Musyrifah tambahan
            [
                'nama_pegawai' => 'M Zumhur Alfarij',
                'email' => 'zumhur.alfarij.musyrif@salsabila.sch.id',
                'nip' => 'MUS003',
                'jabatan_kode' => 'MUSYRIF',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1985-04-17'
            ],
            [
                'nama_pegawai' => 'Abdul Geri',
                'email' => 'abdul.geri.musyrif@salsabila.sch.id',
                'nip' => 'MUS004',
                'jabatan_kode' => 'MUSYRIF',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1985-07-14'
            ],
            [
                'nama_pegawai' => 'Abdul Mutholib',
                'email' => 'abdul.mutholib.musyrif@salsabila.sch.id',
                'nip' => 'MUS005',
                'jabatan_kode' => 'MUSYRIF',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1990-06-14'
            ],
            [
                'nama_pegawai' => 'Nana Munawar',
                'email' => 'nana.munawar@salsabila.sch.id',
                'nip' => 'MUS006',
                'jabatan_kode' => 'MUSYRIF',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1991-01-20'
            ],
            [
                'nama_pegawai' => 'Ayu Nursyiroh',
                'email' => 'ayu.nursyiroh.musyrifah@salsabila.sch.id',
                'nip' => 'MUS007',
                'jabatan_kode' => 'MUSYRIF',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1993-05-28'
            ],
            [
                'nama_pegawai' => 'Putri Oktaviani',
                'email' => 'putri.oktaviani@salsabila.sch.id',
                'nip' => 'MUS008',
                'jabatan_kode' => 'MUSYRIF',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1992-10-15'
            ],
            [
                'nama_pegawai' => 'Siti Haibah',
                'email' => 'siti.haibah@salsabila.sch.id',
                'nip' => 'MUS009',
                'jabatan_kode' => 'MUSYRIF',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1989-07-08'
            ],
            [
                'nama_pegawai' => 'Malika Ega Putri',
                'email' => 'malika.ega.musyrifah@salsabila.sch.id',
                'nip' => 'MUS010',
                'jabatan_kode' => 'MUSYRIF',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1992-04-08'
            ],

            // Staff TU tambahan
            [
                'nama_pegawai' => 'Ahmad Soleh Nurjaman',
                'email' => 'ahmad.soleh@salsabila.sch.id',
                'nip' => 'STU001',
                'jabatan_kode' => 'STAFFTU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Garut',
                'tanggal_lahir' => '1987-09-20'
            ],
            [
                'nama_pegawai' => 'Yusi Febriani',
                'email' => 'yusi.febriani@salsabila.sch.id',
                'nip' => 'STU002',
                'jabatan_kode' => 'STAFFTU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1990-02-28'
            ],
            [
                'nama_pegawai' => 'Mela Indriani',
                'email' => 'mela.indriani@salsabila.sch.id',
                'nip' => 'STU003',
                'jabatan_kode' => 'STAFFTU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1991-11-15'
            ]
        ];

        foreach ($pegawaiTambahan as $data) {
            // Cari jabatan berdasarkan kode
            $jabatan = Jabatan::where('kode_jabatan', $data['jabatan_kode'])->first();
            
            if ($jabatan) {
                Pegawai::create([
                    'nama_pegawai' => $data['nama_pegawai'],
                    'email' => $data['email'],
                    'nip' => $data['nip'],
                    'nik' => '32' . rand(10000000000000, 99999999999999), // Generate NIK
                    'jabatan_id' => $jabatan->id,
                    'status_pegawai' => $data['status_pegawai'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'tempat_lahir' => $data['tempat_lahir'],
                    'tanggal_lahir' => Carbon::parse($data['tanggal_lahir']),
                    'alamat' => 'Pondok Pesantren Salsabila Zainia, Lamongan',
                    'no_hp' => '0812345' . rand(1000, 9999),
                    'agama' => 'Islam',
                    'status_pernikahan' => 'Menikah',
                    'pendidikan_terakhir' => 'S1',
                    'jenis_pegawai' => 'Tetap',
                    'tanggal_masuk' => Carbon::parse('2024-07-01'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
