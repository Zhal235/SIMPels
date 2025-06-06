<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tunggakan {{ $santri->nama_santri }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
        }
        .santri-info {
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .info-section {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .info-section h3 {
            margin-top: 0;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-item span {
            display: block;
            color: #666;
            font-size: 10px;
        }
        .info-item p {
            margin: 2px 0;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
        }
        .status-lunas {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-sebagian {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-belum {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DETAIL TUNGGAKAN SANTRI</h1>
        <p>Tahun Ajaran {{ $tahunAjaran->nama_tahun_ajaran }}</p>
        <p>{{ date('d F Y') }}</p>
    </div>
    
    <div class="santri-info">
        <div class="info-section">
            <h3>Data Santri</h3>
            <div class="info-item">
                <span>NIS:</span>
                <p>{{ $santri->nis }}</p>
            </div>
            <div class="info-item">
                <span>Nama:</span>
                <p>{{ $santri->nama_santri }}</p>
            </div>
            <div class="info-item">
                <span>Status:</span>
                <p>{{ ucfirst($santri->status) }}</p>
            </div>
        </div>
        
        <div class="info-section">
            <h3>Data Akademik</h3>
            <div class="info-item">
                <span>Kelas:</span>
                <p>{{ $santri->kelasRelasi->pluck('nama')->join(', ') ?: '-' }}</p>
            </div>
            <div class="info-item">
                <span>Asrama:</span>
                <p>{{ $santri->asrama_anggota_terakhir->asrama->nama ?? '-' }}</p>
            </div>
            @if($santri->status === 'mutasi' && $santri->mutasi)
            <div class="info-item">
                <span>Tanggal Mutasi:</span>
                <p>{{ \Carbon\Carbon::parse($santri->mutasi->tanggal_mutasi)->format('d/m/Y') }}</p>
            </div>
            <div class="info-item">
                <span>Tujuan Mutasi:</span>
                <p>{{ $santri->mutasi->sekolah_tujuan }}</p>
            </div>
            @endif
        </div>
        
        <div class="info-section">
            <h3>Ringkasan Tunggakan</h3>
            <div class="info-item">
                <span>Total Tunggakan:</span>
                <p style="font-size: 14px; color: #b91c1c;">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</p>
            </div>
            <div class="info-item">
                <span>Jumlah Tagihan:</span>
                <p>{{ $tagihan->count() }} tagihan</p>
            </div>
        </div>
    </div>
    
    <h3>Detail Tagihan</h3>
    @if($tagihan->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis Tagihan</th>
                <th>Periode</th>
                <th>Nominal</th>
                <th>Sudah Dibayar</th>
                <th>Sisa Tagihan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tagihan as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_tagihan)->format('d/m/Y') }}</td>
                <td>{{ $item->jenisTagihan->nama }}</td>
                <td>{{ $item->bulan_tahun }}</td>
                <td class="text-right">Rp {{ number_format($item->nominal_tagihan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->nominal_dibayar, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</td>
                <td>
                    @if($item->status_pembayaran == 'lunas')
                        <span class="status-badge status-lunas">Lunas</span>
                    @elseif($item->status_pembayaran == 'sebagian')
                        <span class="status-badge status-sebagian">Sebagian</span>
                    @else
                        <span class="status-badge status-belum">Belum Bayar</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">Total Tunggakan:</td>
                <td class="text-right">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @else
    <p>Tidak ada data tunggakan untuk santri ini.</p>
    @endif
    
    <div class="footer">
        <p>Dicetak pada: {{ date('d-m-Y H:i:s') }}</p>
        <p style="margin-top: 40px;">(.................................)</p>
        <p>Petugas/Bendahara</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
