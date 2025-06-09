<?php

namespace App\Models\Services;

use App\Models\KeringananTagihan;
use App\Models\Santri;
use App\Models\TagihanSantri;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use Illuminate\Support\Facades\DB;

class KeringananTagihanService
{
    /**
     * Tambah keringanan pembayaran untuk santri
     * 
     * @param array $data
     * @return KeringananTagihan
     */
    public function tambahKeringanan($data)
    {
        DB::beginTransaction();
        
        try {
            // Siapkan data untuk keringanan
            $nilai_potongan = 0;
            
            // Set nilai_potongan berdasarkan jenis keringanan
            if (in_array($data['jenis_keringanan'], ['potongan_persen', 'potongan_nominal'])) {
                $nilai_potongan = $data['nilai_potongan'] ?? 0;
            } elseif (in_array($data['jenis_keringanan'], ['pembebasan', 'bayar_satu_gratis_satu'])) {
                $nilai_potongan = 0; // Nilai 0 untuk pembebasan biaya dan 2 santri bayar 1
            }
            
            // Buat data keringanan
            $keringanan = KeringananTagihan::create([
                'santri_id' => $data['santri_id'],
                'jenis_tagihan_id' => $data['jenis_tagihan_id'] ?? null,
                'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                'jenis_keringanan' => $data['jenis_keringanan'],
                'nilai_potongan' => $nilai_potongan,
                'keterangan' => $data['keterangan'] ?? null,
                'status' => 'aktif',
                'santri_tertanggung_id' => $data['santri_tertanggung_id'] ?? null,
                'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $data['tanggal_selesai'] ?? null
            ]);
            
            // Aplikasikan keringanan ke tagihan yang sudah ada
            $this->aplikasikanKeringanan($keringanan);
            
            DB::commit();
            return $keringanan;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Aplikasikan keringanan ke tagihan santri
     * 
     * @param KeringananTagihan $keringanan
     * @return bool
     */
    public function aplikasikanKeringanan(KeringananTagihan $keringanan)
    {
        // Query untuk mengambil tagihan yang sesuai dengan kriteria keringanan
        $query = TagihanSantri::where('santri_id', $keringanan->santri_id)
            ->where('tahun_ajaran_id', $keringanan->tahun_ajaran_id)
            ->where('status', 'aktif');
            
        // Filter berdasarkan jenis tagihan jika ada
        if ($keringanan->jenis_tagihan_id) {
            $query->where('jenis_tagihan_id', $keringanan->jenis_tagihan_id);
        }
        
        // Filter berdasarkan tanggal jika ada
        if ($keringanan->tanggal_mulai) {
            $query->whereRaw("STR_TO_DATE(CONCAT(SUBSTRING(bulan, 1, 7), '-01'), '%Y-%m-%d') >= ?", [$keringanan->tanggal_mulai]);
        }
        
        if ($keringanan->tanggal_selesai) {
            $query->whereRaw("STR_TO_DATE(CONCAT(SUBSTRING(bulan, 1, 7), '-01'), '%Y-%m-%d') <= ?", [$keringanan->tanggal_selesai]);
        }
        
        // Hanya berlaku untuk tagihan yang belum lunas
        $query->whereRaw('nominal_dibayar < nominal_tagihan');
        
        // Ambil tagihan yang sesuai
        $tagihan = $query->get();
        
        // Update nominal keringanan untuk setiap tagihan
        foreach ($tagihan as $item) {
            $nominalKeringanan = $keringanan->hitungNilaiKeringanan($item->nominal_tagihan);
            $item->updateNominalKeringanan($nominalKeringanan);
        }
        
        return true;
    }
    
    /**
     * Batalkan keringanan pembayaran
     * 
     * @param int $keringananId
     * @return bool
     */
    public function batalkanKeringanan($keringananId)
    {
        DB::beginTransaction();
        
        try {
            $keringanan = KeringananTagihan::findOrFail($keringananId);
            
            // Update status keringanan
            $keringanan->update(['status' => 'nonaktif']);
            
            // Reset nominal keringanan di tagihan
            $query = TagihanSantri::where('santri_id', $keringanan->santri_id)
                ->where('tahun_ajaran_id', $keringanan->tahun_ajaran_id);
                
            // Filter berdasarkan jenis tagihan jika ada
            if ($keringanan->jenis_tagihan_id) {
                $query->where('jenis_tagihan_id', $keringanan->jenis_tagihan_id);
            }
            
            // Reset nominal keringanan
            $query->update(['nominal_keringanan' => 0]);
            
            // Jika ada keringanan lain yang aktif, aplikasikan kembali
            $keringananLain = KeringananTagihan::where('santri_id', $keringanan->santri_id)
                ->where('id', '!=', $keringanan->id)
                ->where('status', 'aktif')
                ->get();
                
            foreach ($keringananLain as $k) {
                $this->aplikasikanKeringanan($k);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Ambil daftar keringanan untuk santri
     * 
     * @param int $santriId
     * @param int|null $tahunAjaranId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getKeringananSantri($santriId, $tahunAjaranId = null)
    {
        $query = KeringananTagihan::where('santri_id', $santriId);
        
        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }
        
        return $query->with(['jenisTagihan', 'tahunAjaran', 'santriTertanggung'])->get();
    }
    
    /**
     * Ambil keringanan yang mencakup untuk tagihan tertentu
     * 
     * @param TagihanSantri $tagihan
     * @return KeringananTagihan|null
     */
    public function getKeringananUntukTagihan(TagihanSantri $tagihan)
    {
        return KeringananTagihan::where('santri_id', $tagihan->santri_id)
            ->where('tahun_ajaran_id', $tagihan->tahun_ajaran_id)
            ->where('status', 'aktif')
            ->where(function($query) use ($tagihan) {
                $query->whereNull('jenis_tagihan_id')
                    ->orWhere('jenis_tagihan_id', $tagihan->jenis_tagihan_id);
            })
            ->first();
    }
    
    /**
     * Hitungkan kembali semua keringanan untuk santri
     * 
     * @param int $santriId
     * @return bool
     */
    public function recalculateKeringanan($santriId)
    {
        // Reset semua nominal keringanan dulu
        TagihanSantri::where('santri_id', $santriId)->update([
            'nominal_keringanan' => 0
        ]);
        
        // Ambil semua keringanan aktif
        $keringanan = KeringananTagihan::where('santri_id', $santriId)
            ->where('status', 'aktif')
            ->get();
            
        // Aplikasikan satu per satu
        foreach ($keringanan as $k) {
            $this->aplikasikanKeringanan($k);
        }
        
        return true;
    }
}
