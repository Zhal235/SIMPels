<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TunggakanExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;
    protected $title;
    
    /**
     * Constructor
     * 
     * @param mixed $data
     * @param string $title
     */
    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Check if data is grouped by santri_id (list view)
        if (isset($this->data->first()['santri_id'])) {
            // Single santri detail view
            return $this->data->map(function ($item) {
                return [
                    'tanggal' => \Carbon\Carbon::parse($item->tanggal_tagihan)->format('d/m/Y'),
                    'jenis_tagihan' => $item->jenisTagihan->nama,
                    'periode' => $item->bulan_tahun,
                    'nominal' => $item->nominal_tagihan,
                    'dibayar' => $item->nominal_dibayar,
                    'sisa' => $item->sisa_tagihan,
                    'status' => ucfirst(str_replace('_', ' ', $item->status_pembayaran))
                ];
            });
        } else {
            // List view grouped by santri_id
            $result = collect();
            $no = 1;
            
            foreach ($this->data as $santriId => $tagihanList) {
                $santri = $tagihanList->first()->santri;
                $totalTunggakan = $tagihanList->sum('sisa_tagihan');
                
                if ($santri->status === 'aktif') {
                    $kelas = $santri->kelasRelasi->pluck('nama')->join(', ') ?: '-';
                    $asrama = $santri->asrama_anggota_terakhir->asrama->nama ?? '-';
                    
                    $result->push([
                        'no' => $no++,
                        'nis' => $santri->nis,
                        'nama' => $santri->nama_santri,
                        'kelas' => $kelas,
                        'asrama' => $asrama,
                        'total' => $totalTunggakan,
                        'jumlah_tagihan' => $tagihanList->count()
                    ]);
                } elseif ($santri->status === 'mutasi') {
                    $mutasi = $santri->mutasi ? $santri->mutasi : null;
                    $tanggalMutasi = $mutasi ? \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->format('d-m-Y') : '-';
                    $sekolahTujuan = $mutasi ? $mutasi->sekolah_tujuan : '-';
                    
                    $result->push([
                        'no' => $no++,
                        'nis' => $santri->nis,
                        'nama' => $santri->nama_santri,
                        'total' => $totalTunggakan,
                        'jumlah_tagihan' => $tagihanList->count(),
                        'tanggal_mutasi' => $tanggalMutasi,
                        'sekolah_tujuan' => $sekolahTujuan
                    ]);
                } elseif ($santri->status === 'alumni') {
                    $tanggalLulus = $santri->tanggal_lulus ? \Carbon\Carbon::parse($santri->tanggal_lulus)->format('d-m-Y') : '-';
                    
                    $result->push([
                        'no' => $no++,
                        'nis' => $santri->nis,
                        'nama' => $santri->nama_santri,
                        'total' => $totalTunggakan,
                        'jumlah_tagihan' => $tagihanList->count(),
                        'tanggal_lulus' => $tanggalLulus
                    ]);
                }
            }
            
            return $result;
        }
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Check if data is grouped by santri_id (list view)
        if (isset($this->data->first()['santri_id'])) {
            // Single santri detail view
            return [
                'Tanggal',
                'Jenis Tagihan',
                'Periode',
                'Nominal',
                'Sudah Dibayar',
                'Sisa Tagihan',
                'Status'
            ];
        } else {
            // List view based on santri status
            $firstSantri = $this->data->first()->first()->santri;
            
            if ($firstSantri->status === 'aktif') {
                return [
                    'No',
                    'NIS',
                    'Nama Santri',
                    'Kelas',
                    'Asrama',
                    'Total Tunggakan',
                    'Jumlah Tagihan'
                ];
            } elseif ($firstSantri->status === 'mutasi') {
                return [
                    'No',
                    'NIS',
                    'Nama Santri',
                    'Total Tunggakan',
                    'Jumlah Tagihan',
                    'Tanggal Mutasi',
                    'Sekolah Tujuan'
                ];
            } else {
                return [
                    'No',
                    'NIS',
                    'Nama Santri',
                    'Total Tunggakan',
                    'Jumlah Tagihan',
                    'Tanggal Lulus'
                ];
            }
        }
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
