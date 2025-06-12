<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Jabatan;
use Carbon\Carbon;

class PegawaiFormasi2024Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar pegawai sesuai formasi personalia pesantren
        $pegawaiData = [
            // DEWAN PENGASUH PESANTREN (Majelis Ri'ayah)
            [
                'nama_pegawai' => 'KH. Zainal Abidin',
                'email' => 'zainal.abidin@salsabila.sch.id',
                'nip' => 'DPP001',
                'jabatan_kode' => 'KDP',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1960-05-15',
                'alamat' => 'Pondok Pesantren Salsabila Zainia',
                'nomor_telepon' => '08123456789'
            ],
            [
                'nama_pegawai' => 'Hj. Hasanuddin',
                'email' => 'hasanuddin@salsabila.sch.id',
                'nip' => 'DPP002',
                'jabatan_kode' => 'ADP',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Gresik',
                'tanggal_lahir' => '1965-08-20',
                'alamat' => 'Pondok Pesantren Salsabila Zainia',
                'nomor_telepon' => '08123456790'
            ],
            [
                'nama_pegawai' => 'Abdul Aziz',
                'email' => 'abdul.aziz@salsabila.sch.id',
                'nip' => 'DPP003',
                'jabatan_kode' => 'ADP',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1968-12-10',
                'alamat' => 'Pondok Pesantren Salsabila Zainia',
                'nomor_telepon' => '08123456791'
            ],
            [
                'nama_pegawai' => 'Dahlan S.Pd.I',
                'email' => 'dahlan@salsabila.sch.id',
                'nip' => 'DPP004',
                'jabatan_kode' => 'ADP',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1970-03-25',
                'alamat' => 'Pondok Pesantren Salsabila Zainia',
                'nomor_telepon' => '08123456792'
            ],

            // PENGURUS HARIAN YAYASAN (Muassasah)
            [
                'nama_pegawai' => 'E Rahmad Hidayat',
                'email' => 'rahmad.hidayat@salsabila.sch.id',
                'nip' => 'PHY001',
                'jabatan_kode' => 'KPH',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1975-06-12',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456793'
            ],
            [
                'nama_pegawai' => 'Dahlan Mungin S.Kom',
                'email' => 'dahlan.mungin@salsabila.sch.id',
                'nip' => 'PHY002',
                'jabatan_kode' => 'SEKUM',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Gresik',
                'tanggal_lahir' => '1980-09-05',
                'alamat' => 'Gresik',
                'nomor_telepon' => '08123456794'
            ],
            [
                'nama_pegawai' => 'Atep Janhur Soleh',
                'email' => 'atep.janhur@salsabila.sch.id',
                'nip' => 'PHY003',
                'jabatan_kode' => 'SEKUM',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1978-11-18',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456795'
            ],
            [
                'nama_pegawai' => 'Dadang Sujana',
                'email' => 'dadang.sujana@salsabila.sch.id',
                'nip' => 'PHY004',
                'jabatan_kode' => 'SEKUM',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Tasikmalaya',
                'tanggal_lahir' => '1982-02-28',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456796'
            ],

            // PIMPINAN PESANTREN (Mudir Al-Ma'had)
            [
                'nama_pegawai' => 'Abdul Geri',
                'email' => 'abdul.geri@salsabila.sch.id',
                'nip' => 'MUD001',
                'jabatan_kode' => 'MUDIR',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1985-07-14',
                'alamat' => 'Pondok Pesantren Salsabila Zainia',
                'nomor_telepon' => '08123456797'
            ],

            // NA'IB MUDIR BIDANG AKADEMIK
            [
                'nama_pegawai' => 'Geri Nurdiansyah S.Ag',
                'email' => 'geri.nurdiansyah@salsabila.sch.id',
                'nip' => 'NAK001',
                'jabatan_kode' => 'NAIB-AKAD',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1988-04-22',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456798'
            ],

            // NA'IB MUDIR BIDANG KESANTRIAN
            [
                'nama_pegawai' => 'Aep Saepudin, S.Pd',
                'email' => 'aep.saepudin@salsabila.sch.id',
                'nip' => 'NAS001',
                'jabatan_kode' => 'NAIB-SANT',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Garut',
                'tanggal_lahir' => '1986-01-10',
                'alamat' => 'Pondok Pesantren Salsabila Zainia',
                'nomor_telepon' => '08123456799'
            ],

            // NA'IB MUDIR BIDANG ADMINISTRASI DAN KEUANGAN
            [
                'nama_pegawai' => 'Rizal Maulana',
                'email' => 'rizal.maulana@salsabila.sch.id',
                'nip' => 'NAA001',
                'jabatan_kode' => 'NAIB-ADKEU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1990-03-15',
                'alamat' => 'Jakarta',
                'nomor_telepon' => '08123456800'
            ],

            // NA'IB MUDIR BIDANG HUMAS
            [
                'nama_pegawai' => 'M. Irwansyah U, S.Pd',
                'email' => 'irwansyah@salsabila.sch.id',
                'nip' => 'NAH001',
                'jabatan_kode' => 'NAIB-HUMAS',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1987-09-08',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456801'
            ],

            // NA'IB MUDIR BIDANG SARANA PRASARANA  
            [
                'nama_pegawai' => 'E. Iskandar Kosasih',
                'email' => 'iskandar.kosasih@salsabila.sch.id',
                'nip' => 'NAP001',
                'jabatan_kode' => 'NAIB-SARPRAS',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1983-12-03',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456802'
            ],

            // NA'IB MUDIR BIDANG KEWIRAUSAHAAN
            [
                'nama_pegawai' => 'Dede Hasanah',
                'email' => 'dede.hasanah@salsabila.sch.id',
                'nip' => 'NAW001',
                'jabatan_kode' => 'NAIB-WIRAUSAHA',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Tasikmalaya',
                'tanggal_lahir' => '1984-05-20',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456803'
            ],

            // KEPALA SEKOLAH/MADRASAH
            [
                'nama_pegawai' => 'Ahmad Wahyudin',
                'email' => 'ahmad.wahyudin@salsabila.sch.id',
                'nip' => 'SMK001',
                'jabatan_kode' => 'KASMK',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Cirebon',
                'tanggal_lahir' => '1985-11-12',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456804'
            ],
            [
                'nama_pegawai' => 'Dahlan Mungin S.Kom',
                'email' => 'dahlan.mts@salsabila.sch.id',
                'nip' => 'MTS001',
                'jabatan_kode' => 'KAMTS',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Gresik',
                'tanggal_lahir' => '1980-09-05',
                'alamat' => 'Gresik',
                'nomor_telepon' => '08123456805'
            ],
            [
                'nama_pegawai' => 'Deden Pratama M.Pd.I',
                'email' => 'deden.pratama@salsabila.sch.id',
                'nip' => 'STA001',
                'jabatan_kode' => 'KASTA',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Garut',
                'tanggal_lahir' => '1982-07-18',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456806'
            ],
            [
                'nama_pegawai' => 'Al Rubaish, S.Pd',
                'email' => 'al.rubaish@salsabila.sch.id',
                'nip' => 'PAU001',
                'jabatan_kode' => 'KAPAUD',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Aceh',
                'tanggal_lahir' => '1988-02-25',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456807'
            ],

            // WALI KELAS
            [
                'nama_pegawai' => 'Abdul Mutholib',
                'email' => 'abdul.mutholib@salsabila.sch.id',
                'nip' => 'WAL001',
                'jabatan_kode' => 'WALIKELAS',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1990-06-14',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456808'
            ],
            [
                'nama_pegawai' => 'Malika Ega Putri',
                'email' => 'malika.ega@salsabila.sch.id',
                'nip' => 'WAL002',
                'jabatan_kode' => 'WALIKELAS',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1992-04-08',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456809'
            ],

            // OPERATOR SEKOLAH/MADRASAH
            [
                'nama_pegawai' => 'Doni Setiawan',
                'email' => 'doni.setiawan@salsabila.sch.id',
                'nip' => 'OPR001',
                'jabatan_kode' => 'OPERATOR',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1993-08-22',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456810'
            ],

            // BAGIAN ADMINISTRASI DAN KEUANGAN
            [
                'nama_pegawai' => 'Aris Munawar',
                'email' => 'aris.munawar@salsabila.sch.id',
                'nip' => 'ADM001',
                'jabatan_kode' => 'KASEKRET',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1987-03-12',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456811'
            ],
            [
                'nama_pegawai' => 'Sukmiati, S.Pd',
                'email' => 'sukmiati@salsabila.sch.id',
                'nip' => 'KEU001',
                'jabatan_kode' => 'KAKEU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1989-10-15',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456812'
            ],
            [
                'nama_pegawai' => 'Yasinta Widayanti',
                'email' => 'yasinta.widayanti@salsabila.sch.id',
                'nip' => 'PEL001',
                'jabatan_kode' => 'STAFFTU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1991-12-07',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456813'
            ],
            [
                'nama_pegawai' => 'Rizal Maulana',
                'email' => 'rizal.bendahara@salsabila.sch.id',
                'nip' => 'BEN001',
                'jabatan_kode' => 'BENDAHARA',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1990-03-15',
                'alamat' => 'Jakarta',
                'nomor_telepon' => '08123456814'
            ],
            [
                'nama_pegawai' => 'Elin Tamaya, S.Pd',
                'email' => 'elin.tamaya@salsabila.sch.id',
                'nip' => 'BEN002',
                'jabatan_kode' => 'BENDAHARA',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1988-06-28',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456815'
            ],

            // PEMBINA ASRAMA
            [
                'nama_pegawai' => 'Hj. Al Rubaish, S.Pd',
                'email' => 'al.rubaish.pembina@salsabila.sch.id',
                'nip' => 'ASR001',
                'jabatan_kode' => 'PASRAMPUT',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Aceh',
                'tanggal_lahir' => '1988-02-25',
                'alamat' => 'Pondok Pesantren Salsabila Zainia',
                'nomor_telepon' => '08123456816'
            ],

            // MUSYRIF/MUSYRIFAH ASRAMA
            [
                'nama_pegawai' => 'Zenal Muttakin',
                'email' => 'zenal.muttakin@salsabila.sch.id',
                'nip' => 'MUS001',
                'jabatan_kode' => 'MUSYRIF',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1992-11-30',
                'alamat' => 'Pondok Pesantren Salsabila Zainia',
                'nomor_telepon' => '08123456817'
            ],
            [
                'nama_pegawai' => 'Salamita',
                'email' => 'salamita@salsabila.sch.id',
                'nip' => 'MUS002',
                'jabatan_kode' => 'MUSYRIF',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1990-08-17',
                'alamat' => 'Pondok Pesantren Salsabila Zainia',
                'nomor_telepon' => '08123456818'
            ],

            // GURU/USTADZ (Sample)
            [
                'nama_pegawai' => 'M Ikbal Nugraha, Lc',
                'email' => 'ikbal.nugraha@salsabila.sch.id',
                'nip' => 'GUR001',
                'jabatan_kode' => 'GURU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Lamongan',
                'tanggal_lahir' => '1985-05-12',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456819'
            ],
            [
                'nama_pegawai' => 'Muhammad Muslim Akbar',
                'email' => 'muslim.akbar@salsabila.sch.id',
                'nip' => 'GUR002',
                'jabatan_kode' => 'GURU',
                'status_pegawai' => 'Aktif',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1987-09-08',
                'alamat' => 'Lamongan',
                'nomor_telepon' => '08123456820'
            ]
        ];

        foreach ($pegawaiData as $data) {
            // Cari jabatan berdasarkan kode
            $jabatan = Jabatan::where('kode_jabatan', $data['jabatan_kode'])->first();
            
            if ($jabatan) {
                Pegawai::create([
                    'nama_pegawai' => $data['nama_pegawai'],
                    'email' => $data['email'],
                    'nip' => $data['nip'],
                    'nik' => $data['nik'] ?? '32' . rand(10000000000000, 99999999999999), // Generate NIK jika tidak ada
                    'jabatan_id' => $jabatan->id,
                    'status_pegawai' => $data['status_pegawai'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'tempat_lahir' => $data['tempat_lahir'],
                    'tanggal_lahir' => Carbon::parse($data['tanggal_lahir']),
                    'alamat' => $data['alamat'],
                    'no_hp' => $data['nomor_telepon'],
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
