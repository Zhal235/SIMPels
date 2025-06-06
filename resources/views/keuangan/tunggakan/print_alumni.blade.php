<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tunggakan Santri Alumni</title>
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
        <h1>DAFTAR TUNGGAKAN SANTRI ALUMNI</h1>
        <p>Tahun Ajaran {{ $tahunAjaran->nama_tahun_ajaran }}</p>
        <p>{{ date('d F Y') }}</p>
    </div>
    
    @if($tunggakan->count() > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Santri</th>
                <th>Total Tunggakan</th>
                <th>Jumlah Tagihan</th>
                <th>Tanggal Lulus</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; $no = 1; @endphp
            @foreach($tunggakan as $santri_id => $tagihanList)
            @php
                $santri = $tagihanList->first()->santri;
                $totalTunggakan = $tagihanList->sum('sisa_tagihan');
                $tanggalLulus = $santri->tanggal_lulus ? \Carbon\Carbon::parse($santri->tanggal_lulus)->format('d-m-Y') : '-';
                $total += $totalTunggakan;
            @endphp
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $santri->nis }}</td>
                <td>{{ $santri->nama_santri }}</td>
                <td class="text-right">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
                <td>{{ $tagihanList->count() }} tagihan</td>
                <td>{{ $tanggalLulus }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL TUNGGAKAN:</td>
                <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    @else
    <p>Tidak ada data tunggakan untuk santri alumni.</p>
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
