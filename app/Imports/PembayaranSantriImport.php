<?php

namespace App\Imports;

use App\Models\PembayaranSantri;
use App\Models\Santri;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PembayaranSantriImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari santri berdasarkan NIS
        $santri = Santri::where('nis', $row['nis'])->first();
        
        if (!$santri) {
            throw new \Exception("Santri dengan NIS {$row['nis']} tidak ditemukan.");
        }

        // Convert tanggal pembayaran
        $tanggal_pembayaran = $this->convertExcelDate($row['tanggal_pembayaran_yyyy_mm_dd'] ?? null);

        return new PembayaranSantri([
            'santri_id' => $santri->id,
            'jenis_pembayaran' => $row['jenis_pembayaran'] ?? null,
            'jumlah' => $row['jumlah'] ?? 0,
            'tanggal_pembayaran' => $tanggal_pembayaran,
            'metode_pembayaran' => $row['metode_pembayaran'] ?? null,
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nis' => 'required',
            'jenis_pembayaran' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_pembayaran_yyyy_mm_dd' => 'required',
            'metode_pembayaran' => 'required|string|max:255',
        ];
    }

    private function convertExcelDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Jika sudah dalam format string tanggal
        if (is_string($value)) {
            try {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            }
        }

        // Jika dalam format Excel date (numeric)
        if (is_numeric($value)) {
            try {
                return \Carbon\Carbon::instance(Date::excelToDateTimeObject($value))->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}