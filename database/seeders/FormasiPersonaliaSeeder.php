<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Jabatan;
use Illuminate\Support\Facades\DB;

class FormasiPersonaliaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua data pegawai existing untuk fresh start
        DB::statement('PRAGMA foreign_keys = OFF;');
        Pegawai::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        $this->command->info('Membuat data formasi personalia pesantren...');

        // DEWAN PENGASUH PESANTREN (Majelis Ri'ayah)
        $this->createPegawai('KH. Zaenul Abidin', 'KDP', 'L', 'Pengasuh');
        $this->createPegawai('Ejen Jaenudin', 'ADP', 'L', 'Kepesantrenan');
        $this->createPegawai('Abdul Aziz', 'ADP', 'L', 'Pengajar Kode Etik Guru');
        $this->createPegawai('Dahlan S.Pd.I', 'ADP', 'L', 'Akhlak & Ubudiyah');

        // PENGURUS HARIAN YAYASAN (Muassasah)
        $this->createPegawai('E Rahmat Hidayat', 'KPH', 'L', 'Ketua');
        $this->createPegawai('Dahlan Munajir S.Kom', 'SEKUM', 'L', 'Sekretaris');
        $this->createPegawai('Asep Jamhur Soleh', 'BENDAHARA', 'L', 'Bendahara');
        $this->createPegawai('Dadang Sujana', 'STAFF', 'L', 'Pengawas'); // Changed from PENGAWAS to STAFF

        // PIMPINAN PESANTREN UNGGUL SALSABILA ZAINIA (Mudir Al-Ma'had)
        $this->createPegawai('Deden Priatna S.Ag, M.Pd.I', 'MUDIR', 'L', 'Pimpinan Pesantren');

        // NA'IB MUDIR AL-MA'HAD BIDANG AKADEMIK
        $this->createPegawai('Geri Nurdiansyah S.Ag', 'NAIB-AKAD', 'L', 'Na\'ib Mudir Akademik');

        // Bagian Evaluasi & Pengajaran
        $this->createPegawai('Nurhikmat, S.Pd', 'GURU', 'L', 'Evaluasi & Pengajaran');
        $this->createPegawai('Jumhur Alfariz', 'GURU', 'L', 'Evaluasi & Pengajaran');

        // Bagian Pengembangan Bakat Akademik
        $this->createPegawai('Yuni Wihartiningsih', 'GURU', 'P', 'Pengembangan Bakat Akademik');
        $this->createPegawai('Al Siti Nuryasah, S.Pd', 'GURU', 'P', 'Pengembangan Bakat Akademik');

        // Digital Class Room (DCR)
        $this->createPegawai('Lindawati, S.Pd', 'OPERATOR', 'P', 'Digital Class Room');
        $this->createPegawai('Rani Rizal Maulana', 'OPERATOR', 'P', 'Digital Class Room');

        // Kepala SMK
        $this->createPegawai('Ahmad Wahyudin', 'KASMK', 'L', 'Kepala SMK');
        $this->createPegawai('Dahlan Munajir S.Kom', 'GURU', 'L', 'Kepala SMK - Staff');

        // Kepala MTs
        $this->createPegawai('Deden Priatna M.Pd.I', 'KAMTS', 'L', 'Kepala MTs');
        $this->createPegawai('Al Rubaish, S.Pd', 'GURU', 'L', 'Kepala MTs - Staff');

        // Kepala STA
        $this->createPegawai('Cucu Saidah, S.Pd', 'KASTA', 'P', 'Kepala STA');
        $this->createPegawai('Hertiawati S.Kom', 'GURU', 'P', 'Kepala STA - Staff');

        // Kepala PAUD
        $this->createPegawai('Doni Setiawan', 'KAPAUD', 'L', 'Kepala PAUD');
        $this->createPegawai('Sandi Irawan', 'GURU', 'L', 'Kepala PAUD - Staff');
        $this->createPegawai('Sahrul Ramadhan', 'GURU', 'L', 'Kepala PAUD - Staff');
        $this->createPegawai('Abdul Mutholib', 'GURU', 'L', 'Kepala PAUD - Staff');

        // Wali Kelas
        $this->createPegawai('Abdul Mutholib', 'WALIKELAS', 'L', 'Wali Kelas VII A', true);
        $this->createPegawai('Malika Ega Putri', 'WALIKELAS', 'P', 'Wali Kelas VII B');
        $this->createPegawai('Lutfi Dhiaulhaq', 'WALIKELAS', 'L', 'Wali Kelas VII C');
        $this->createPegawai('Firda Anida Fadilah', 'WALIKELAS', 'P', 'Wali Kelas VII C');
        $this->createPegawai('Sandi Irawan', 'WALIKELAS', 'L', 'Wali Kelas VIII A', true);
        $this->createPegawai('Dedeh Yustika Basith, S.Pd', 'WALIKELAS', 'P', 'Wali Kelas VIII B');
        $this->createPegawai('Aes Saepudin S.Pd', 'WALIKELAS', 'L', 'Wali Kelas IX A');
        $this->createPegawai('Selamita', 'WALIKELAS', 'P', 'Wali Kelas IX B');
        $this->createPegawai('Zenal Muttakin', 'WALIKELAS', 'L', 'Wali Kelas X');
        $this->createPegawai('Siti Haibah', 'WALIKELAS', 'P', 'Wali Kelas XI');
        $this->createPegawai('Sahrul Ramadhan', 'WALIKELAS', 'L', 'Wali Kelas XI', true);
        $this->createPegawai('Ayu Nursyiroh', 'WALIKELAS', 'P', 'Wali Kelas XII');
        $this->createPegawai('Geri Nurdiansyah S.Ag', 'WALIKELAS', 'L', 'Wali Kelas XII', true);
        $this->createPegawai('Elin Tamaya S.Pd', 'WALIKELAS', 'P', 'Wali Kelas XII');

        // Tenaga Pengajar PAUD/OTA
        $this->createPegawai('Anelika Pratiwi S.Adita', 'GURU', 'P', 'Tenaga Pengajar PAUD/OTA');
        $this->createPegawai('Siti Maesaroh', 'GURU', 'P', 'Tenaga Pengajar PAUD/OTA');

        // NA'IB MUDIR BIDANG KESANTRIAN
        $this->createPegawai('Aep Saepudin, S.Pd', 'NAIB-SANT', 'L', 'Na\'ib Mudir Kesantrian');

        // Bagian Keorganisasian (IKSSA)
        $this->createPegawai('Sahrul Ramadhan', 'STAFFTU', 'L', 'Bagian Keorganisasian', true);
        $this->createPegawai('Ayu Nursyiroh', 'STAFFTU', 'P', 'Bagian Keorganisasian');

        // Bagian Ubudiyah
        $this->createPegawai('Nana Munawar', 'GURU', 'L', 'Bagian Ubudiyah');
        $this->createPegawai('Abdul Majid', 'GURU', 'L', 'Bagian Ubudiyah');
        $this->createPegawai('Yusi Febriani', 'GURU', 'P', 'Bagian Ubudiyah');
        $this->createPegawai('Mela Indriani', 'GURU', 'P', 'Bagian Ubudiyah');

        // Bagian Ta'lim dan Tahfidz
        $this->createPegawai('M Ikbal Nugraha, Lc', 'GURU', 'L', 'Bagian Ta\'lim dan Tahfidz');
        $this->createPegawai('Hj. Al Rubaish, S.Pd', 'GURU', 'P', 'Bagian Ta\'lim dan Tahfidz');
        $this->createPegawai('Sukmiati, S.Pd', 'GURU', 'P', 'Bagian Ta\'lim dan Tahfidz');
        $this->createPegawai('Firda Anida Fadilah', 'GURU', 'P', 'Bagian Ta\'lim dan Tahfidz', true);

        // Bagian Bahasa
        $this->createPegawai('Elis Sapuroh, S.Pd', 'GURU', 'P', 'Bagian Bahasa');
        $this->createPegawai('M Zumhur Alparij', 'GURU', 'L', 'Bagian Bahasa');
        $this->createPegawai('Siti Haibah', 'GURU', 'P', 'Bagian Bahasa', true);
        $this->createPegawai('Malika Ega Putri', 'GURU', 'P', 'Bagian Bahasa', true);

        // Bagian Keamanan
        $this->createPegawai('Nana Munawar', 'MUSYRIF', 'L', 'Bagian Keamanan', true);
        $this->createPegawai('Abdul Geri', 'MUSYRIF', 'L', 'Bagian Keamanan');
        $this->createPegawai('Selamita', 'MUSYRIF', 'P', 'Bagian Keamanan', true);
        $this->createPegawai('Yara Muntaha', 'MUSYRIF', 'P', 'Bagian Keamanan');

        // Bagian Pengembangan minat dan bakat
        $this->createPegawai('Rizki Ayu Septiani', 'GURU', 'P', 'Bagian Pengembangan minat dan bakat');

        // Bagian Pramuka & Paskibra
        $this->createPegawai('Abdul Mutholib', 'GURU', 'L', 'Bagian Pramuka & Paskibra', true);
        $this->createPegawai('Selamita', 'GURU', 'P', 'Bagian Pramuka & Paskibra', true);

        // Bagian Kesehatan
        $this->createPegawai('Dedeh Yustika Basith, S.Pd', 'GURU', 'P', 'Bagian Kesehatan', true);
        $this->createPegawai('Putri Oktaviani', 'GURU', 'P', 'Bagian Kesehatan');
        $this->createPegawai('Abdul Geri', 'GURU', 'L', 'Bagian Kesehatan', true);

        // Bagian Kebersihan
        $this->createPegawai('Elin Tamaya, S.Pd', 'STAFFTU', 'P', 'Bagian Kebersihan');
        $this->createPegawai('Ahmad Soleh Nurjaman', 'STAFFTU', 'L', 'Bagian Kebersihan');
        $this->createPegawai('Yusi Febriani', 'STAFFTU', 'P', 'Bagian Kebersihan', true);
        $this->createPegawai('Mela Indriyani', 'STAFFTU', 'P', 'Bagian Kebersihan');

        // Pembina Asrama
        $this->createPegawai('Hj. Al Rubaish, S.Pd', 'PASRAMPUTRI', 'P', 'Pembina Asrama Putra', true);
        
        // Wali Asrama Putra
        $this->createPegawai('Zenal Muttakin', 'MUSYRIF', 'L', 'Wali Asrama Putra Az-Zaini 1', true);
        $this->createPegawai('M Zumhur Alfarij', 'MUSYRIF', 'L', 'Wali Asrama Putra Az-Zaini 2');
        $this->createPegawai('Abdul Geri', 'MUSYRIF', 'L', 'Wali Asrama Putra Az-Zaini 3', true);
        $this->createPegawai('Abdul Mutholib', 'MUSYRIF', 'L', 'Wali Asrama Putra Az-Zaini 4', true);
        $this->createPegawai('Nana Munawar', 'MUSYRIF', 'L', 'Wali Asrama Putra Az-Zaini 5', true);

        // Wali Asrama Putri
        $this->createPegawai('Selamita', 'MUSYRIF', 'P', 'Wali Asrama Putri Zainia 1', true);
        $this->createPegawai('Ayu Nursyiroh', 'MUSYRIF', 'P', 'Wali Asrama Putri Zainia 2', true);
        $this->createPegawai('Putri Oktaviani', 'MUSYRIF', 'P', 'Wali Asrama Putri Zainia 3');
        $this->createPegawai('Siti Haibah', 'MUSYRIF', 'P', 'Wali Asrama Putri As-Sya\'biah 1', true);
        $this->createPegawai('Malika Ega Putri', 'MUSYRIF', 'P', 'Wali Asrama Putri As-Sya\'biah 2', true);
        $this->createPegawai('Dedeh Yustika Basith, S.Pd', 'MUSYRIF', 'P', 'Wali Asrama Putri As-Sya\'biah 2', true);
        $this->createPegawai('Elin Tamaya, S.Pd', 'MUSYRIF', 'P', 'Wali Asrama Putri Al-Kautsar 1', true);
        $this->createPegawai('Yusi Febriani', 'MUSYRIF', 'P', 'Wali Asrama Putri Al-Kautsar 2', true);
        $this->createPegawai('Siti Maesaroh', 'MUSYRIF', 'P', 'Wali Asrama Putri Al-Kautsar 3');
        $this->createPegawai('Intan Meliani', 'MUSYRIF', 'P', 'Wali Asrama Putri Al-Kautsar 3');
        $this->createPegawai('Yara Muntaha', 'MUSYRIF', 'P', 'Wali Asrama Putri Al-Kautsar 3', true);
        $this->createPegawai('Dian Herawati', 'MUSYRIF', 'P', 'Wali Asrama Putri Al-Kautsar 3');
        $this->createPegawai('Mela Indriani', 'MUSYRIF', 'P', 'Wali Asrama Putri Al-Kautsar 3', true);

        // NA'IB MUDIR Bagian Administrasi dan keuangan
        $this->createPegawai('Rizal Maulana', 'NAIB-ADKEU', 'L', 'Na\'ib Mudir Administrasi dan Keuangan');

        // Bagian Kesekretariatan
        $this->createPegawai('Aris Munawar', 'KASEKRET', 'L', 'Bagian Kesekretariatan');

        // Bagian Keuangan & KKT
        $this->createPegawai('Sukmiati, S.Pd', 'KAKEU', 'P', 'Bagian Keuangan & KKT', true);

        // Bagian Pelayanan
        $this->createPegawai('Yasinta Widayanti', 'STAFFTU', 'P', 'Bagian Pelayanan');

        // Bendahara MTs
        $this->createPegawai('Rizal Maulana', 'BENDAHARA', 'L', 'Bendahara MTs', true);

        // Bendahara SMK
        $this->createPegawai('Elin Tamaya, S.Pd', 'BENDAHARA', 'P', 'Bendahara SMK', true);

        // NA'IB MUDIR BIDANG HUMAS
        $this->createPegawai('M. Irwansyah U, S.Pd', 'NAIB-HUMAS', 'L', 'Na\'ib Mudir Bidang Humas');

        // Humas P3M
        $this->createPegawai('Danung, S.Pd', 'STAFFTU', 'L', 'Humas P3M');

        // SAZA Media
        $this->createPegawai('Muhammad Muslim Akbar', 'STAFFTU', 'L', 'SAZA Media');

        // NA'IB MUDIR BIDANG SARPRAS
        $this->createPegawai('E. Iskandar Kosasih', 'NAIB-SARPRAS', 'L', 'Na\'ib Mudir Bidang Sarpras');

        // Sekretaris Umum
        $this->createPegawai('Ahmad Wahyudin', 'SEKUM', 'L', 'Sekretaris Umum', true);

        // Bagian K3LH
        $this->createPegawai('Ajid Abdul Ajiz', 'STAFFTU', 'L', 'Bagian K3LH');

        // NA'IB MUDIR BIDANG KEWIRAUSAHAAN
        $this->createPegawai('Dede Hasanah', 'NAIB-WIRAUSAHA', 'P', 'Na\'ib Mudir Bidang Kewirausahaan');

        // Bagian APP
        $this->createPegawai('Dejan Saefiq', 'STAFFTU', 'L', 'Bagian APP');

        // Laundry
        $this->createPegawai('Azmi Taqwim', 'STAFFTU', 'L', 'Laundry');

        // Syirkah & Math'am
        $this->createPegawai('Neng Rani', 'STAFFTU', 'P', 'Syirkah & Math\'am');
        $this->createPegawai('Rizki Ayu Septiani', 'STAFFTU', 'P', 'Syirkah & Math\'am', true);
        $this->createPegawai('Ahmad Soleh Nurjaman', 'STAFFTU', 'L', 'Syirkah & Math\'am', true);
        $this->createPegawai('Abdul Majid', 'STAFFTU', 'L', 'Syirkah & Math\'am', true);
        $this->createPegawai('Syifa Aulia Zahra', 'STAFFTU', 'P', 'Syirkah & Math\'am');
        $this->createPegawai('Intan Meliani', 'STAFFTU', 'P', 'Syirkah & Math\'am', true);

        // Rujiqo
        $this->createPegawai('Hj. Al Rubaish, S.Pd', 'STAFFTU', 'P', 'Rujiqo', true);
        $this->createPegawai('Hj. Enung K Hilmiah', 'STAFFTU', 'P', 'Rujiqo');
        $this->createPegawai('Hj. Didah Ismail', 'STAFFTU', 'P', 'Rujiqo');
        $this->createPegawai('Jenal Muttaqin', 'STAFFTU', 'L', 'Rujiqo');

        $this->command->info('Data formasi personalia berhasil dibuat!');
        $this->command->info('Total pegawai: ' . Pegawai::count());
    }

    private function createPegawai($nama, $kodeJabatan, $jenisKelamin, $keterangan = null, $isDuplicate = false)
    {
        // Cek apakah pegawai sudah ada
        $existing = Pegawai::where('nama_pegawai', $nama)->first();
        
        if ($existing && !$isDuplicate) {
            // Update jabatan utama jika level lebih tinggi
            $jabatan = Jabatan::where('kode_jabatan', $kodeJabatan)->first();
            $currentJabatan = Jabatan::find($existing->jabatan_id);
            
            if ($jabatan && (!$existing->jabatan_id || ($currentJabatan && $jabatan->level_jabatan < $currentJabatan->level_jabatan))) {
                $existing->update([
                    'jabatan_id' => $jabatan->id,
                    'jabatan' => $jabatan->nama_jabatan,
                    'divisi' => $jabatan->bidang?->nama_bidang
                ]);
            }
            return $existing;
        }

        if ($existing && $isDuplicate) {
            // Jangan buat duplikat, skip
            return $existing;
        }

        $jabatan = Jabatan::where('kode_jabatan', $kodeJabatan)->first();
        
        if (!$jabatan) {
            $this->command->warn("Jabatan dengan kode {$kodeJabatan} tidak ditemukan untuk {$nama}");
            return null;
        }

        // Generate data pegawai
        $nik = $this->generateNIK();
        $nip = $jenisKelamin == 'L' ? 'NIP' . rand(100000, 999999) : null;
        
        return Pegawai::create([
            'nama_pegawai' => $nama,
            'nik' => $nik,
            'nip' => $nip,
            'jenis_kelamin' => $jenisKelamin,
            'tempat_lahir' => $this->getRandomCity(),
            'tanggal_lahir' => $this->getRandomBirthDate(),
            'alamat' => $this->getRandomAddress(),
            'no_hp' => '081' . rand(10000000, 99999999),
            'email' => $this->generateEmail($nama),
            'agama' => 'Islam',
            'status_pernikahan' => rand(1, 10) > 3 ? 'Menikah' : 'Belum Menikah',
            'pendidikan_terakhir' => $this->getRandomEducation(),
            'jurusan' => $this->getRandomMajor(),
            'institusi' => $this->getRandomInstitution(),
            'tahun_lulus' => rand(2000, 2023),
            'jabatan_id' => $jabatan->id,
            'jabatan' => $jabatan->nama_jabatan,
            'divisi' => $jabatan->bidang?->nama_bidang,
            'tanggal_masuk' => $this->getRandomHireDate(),
            'status_pegawai' => 'Aktif',
            'jenis_pegawai' => $this->getRandomEmployeeType(),
            'gaji_pokok' => $jabatan->gaji_pokok,
            'keterangan' => $keterangan
        ]);
    }

    private function generateNIK()
    {
        return '3207' . rand(1000000000, 9999999999) . rand(10, 99);
    }

    private function getRandomCity()
    {
        $cities = ['Tasikmalaya', 'Bandung', 'Jakarta', 'Ciamis', 'Garut', 'Sukabumi', 'Cirebon'];
        return $cities[array_rand($cities)];
    }

    private function getRandomBirthDate()
    {
        return now()->subYears(rand(25, 55))->subDays(rand(1, 365));
    }

    private function getRandomAddress()
    {
        $addresses = [
            'Jl. Raya Salabenda No. 123, Tasikmalaya',
            'Jl. Ahmad Yani No. 456, Tasikmalaya', 
            'Jl. Otto Iskandardinata No. 789, Tasikmalaya',
            'Jl. RE Martadinata No. 321, Tasikmalaya',
            'Jl. Sutisna Senjaya No. 654, Tasikmalaya'
        ];
        return $addresses[array_rand($addresses)];
    }

    private function generateEmail($nama)
    {
        $name = strtolower(str_replace([' ', '.', ',', 'Hj.', 'M.', 'S.Pd', 'S.Ag', 'S.Kom', 'M.Pd.I', 'Lc'], '', $nama));
        return $name . rand(1, 99) . '@salsabila.sch.id';
    }

    private function getRandomEducation()
    {
        $educations = ['S1', 'S2', 'D3', 'SMA', 'SMK', 'MA'];
        return $educations[array_rand($educations)];
    }

    private function getRandomMajor()
    {
        $majors = [
            'Pendidikan Agama Islam', 'Pendidikan Bahasa Arab', 'Pendidikan Bahasa Inggris',
            'Teknik Informatika', 'Sistem Informasi', 'Pendidikan Matematika',
            'Pendidikan Bahasa Indonesia', 'Ilmu Al-Quran dan Tafsir', 'Hadits'
        ];
        return $majors[array_rand($majors)];
    }

    private function getRandomInstitution()
    {
        $institutions = [
            'IAIN Sunan Gunung Djati Bandung', 'UIN Bandung', 'UNSIL Tasikmalaya',
            'Institut Agama Islam Cipasung', 'STAI Al-Musaddadiyah Garut',
            'Universitas Siliwangi', 'STMIK Tasikmalaya'
        ];
        return $institutions[array_rand($institutions)];
    }

    private function getRandomHireDate()
    {
        return now()->subYears(rand(1, 15))->subDays(rand(1, 365));
    }

    private function getRandomEmployeeType()
    {
        $types = ['Tetap', 'Kontrak', 'Honorer'];
        $weights = [40, 30, 30]; // 40% Tetap, 30% Kontrak, 30% Honorer
        
        $rand = rand(1, 100);
        if ($rand <= 40) return 'Tetap';
        if ($rand <= 70) return 'Kontrak';
        return 'Honorer';
    }
}
