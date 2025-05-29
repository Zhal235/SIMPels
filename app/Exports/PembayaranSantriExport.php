<?php

namespace App\Exports;

use App\Models\PembayaranSantri;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PembayaranSantriExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $bulan;

    public function __construct($bulan = null)
    {
        $this->bulan = $bulan;
    }

    public function collection()
    {
        $query = PembayaranSantri::with('santri');

        if ($this->bulan) {
            $query->whereMonth('tanggal_pembayaran', $this->bulan);
        }

        return $query->orderBy('tanggal_pembayaran', 'desc')->get();
    }

    public function map($pembayaran): array
    {
        return [
            $pembayaran->santri->nis ?? '',
            $pembayaran->santri->nama ?? '',
            $pembayaran->jenis_pembayaran,
            $pembayaran->jumlah,
            $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') : '',
            $pembayaran->metode_pembayaran,
            $pembayaran->keterangan ?? '',
            $pembayaran->created_at ? \Carbon\Carbon::parse($pembayaran->created_at)->format('d/m/Y H:i') : ''
        ];
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama Santri',
            'Jenis Pembayaran',
            'Jumlah',
            'Tanggal Pembayaran',
            'Metode Pembayaran',
            'Keterangan',
            'Tanggal Input'
        ];
    }
}