<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Carbon;

class TransaksiExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = Transaksi::with(['santri', 'kategoriKeuangan', 'metodePembayaran']);

        if (!empty($this->filters['tanggal_mulai']) && !empty($this->filters['tanggal_selesai'])) {
            $query->whereBetween('tanggal', [$this->filters['tanggal_mulai'], $this->filters['tanggal_selesai']]);
        }

        if (!empty($this->filters['kategori_id'])) {
            $query->where('keuangan_kategori_id', $this->filters['kategori_id']);
        }

        if (!empty($this->filters['metode_id'])) {
            $query->where('keuangan_metode_id', $this->filters['metode_id']);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'NIS Santri',
            'Nama Santri',
            'Kategori Pembayaran',
            'Metode Pembayaran',
            'Tipe Pembayaran',
            'Nominal',
            'Tanggal Pembayaran',
            'Keterangan',
            'Dibuat Pada',
        ];
    }

    public function map($transaksi): array
    {
        return [
            $transaksi->id,
            $transaksi->santri->nis ?? 'N/A',
            $transaksi->santri->nama_lengkap ?? 'N/A',
            $transaksi->kategoriKeuangan->nama_kategori ?? 'N/A',
            $transaksi->metodePembayaran->nama_metode ?? 'N/A',
            ucfirst($transaksi->tipe_pembayaran),
            $transaksi->nominal,
            Carbon::parse($transaksi->tanggal)->format('d-m-Y'),
            $transaksi->keterangan,
            Carbon::parse($transaksi->created_at)->format('d-m-Y H:i:s'),
        ];
    }
}
