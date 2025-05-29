<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PembayaranSantriTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection(): Collection
    {
        // Return empty collection for template
        return collect([
            [
                '12345', // nis
                'SPP', // jenis_pembayaran
                '500000', // jumlah
                '2024-01-15', // tanggal_pembayaran (YYYY-MM-DD)
                'Transfer', // metode_pembayaran
                'Pembayaran SPP Januari 2024' // keterangan
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Jenis Pembayaran',
            'Jumlah',
            'Tanggal Pembayaran (YYYY-MM-DD)',
            'Metode Pembayaran',
            'Keterangan'
        ];
    }
}