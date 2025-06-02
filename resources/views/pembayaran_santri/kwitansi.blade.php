<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - SIMPelS</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #fff;
        }
        .receipt-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .receipt-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .receipt-header h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        .receipt-header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .receipt-title {
            text-align: center;
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
        }
        .receipt-info {
            margin-bottom: 20px;
        }
        .receipt-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-info table td {
            padding: 5px;
            vertical-align: top;
        }
        .receipt-info table td:first-child {
            width: 150px;
        }
        .receipt-items {
            margin-bottom: 20px;
        }
        .receipt-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-items table th,
        .receipt-items table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .receipt-items table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .receipt-total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
        .receipt-footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .receipt-signature {
            text-align: center;
            width: 200px;
        }
        .receipt-signature .line {
            margin-top: 80px;
            border-top: 1px solid #333;
        }
        .receipt-notes {
            margin-top: 20px;
            font-size: 12px;
            font-style: italic;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .wallet-info {
            margin-top: 10px;
            padding: 10px;
            border: 1px dashed #333;
            background-color: #f9f9f9;
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>PONDOK PESANTREN MODERN</h1>
            <h2>SIMPelS - Sistem Informasi Manajemen Pesantren</h2>
            <p>Jl. Pesantren No. 123, Kota Santri, Indonesia 12345</p>
            <p>Telp: (021) 123-4567 | Email: info@simpels.ac.id</p>
        </div>

        <div class="receipt-title">
            KWITANSI PEMBAYARAN
        </div>

        <div class="receipt-info">
            <table>
                <tr>
                    <td>No. Kwitansi</td>
                    <td>: <span id="receipt-number"></span></td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: <span id="receipt-date"></span></td>
                </tr>
                <tr>
                    <td>Nama Santri</td>
                    <td>: <span id="student-name"></span></td>
                </tr>
                <tr>
                    <td>NIS</td>
                    <td>: <span id="student-nis"></span></td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>: <span id="student-class"></span></td>
                </tr>
            </table>
        </div>

        <div class="receipt-items">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Pembayaran</th>
                        <th>Bulan/Periode</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Dibayar (Transaksi Ini)</th>
                        <th class="text-right">Total Telah Dibayar</th>
                        <th class="text-right">Sisa Tagihan</th>
                    </tr>
                </thead>
                <tbody id="payment-items">
                    <!-- Payment items will be inserted here dynamically -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-right font-bold">Total Tagihan Transaksi Ini</td>
                        <td class="font-bold text-right" id="payment-total"></td>
                    </tr>
                    <tr id="cash-row">
                        <td colspan="6" class="text-right">Jumlah Bayar</td>
                        <td class="text-right" id="cash-amount"></td>
                    </tr>
                    <tr id="change-row">
                        <td colspan="6" class="text-right">Kembalian</td>
                        <td class="text-right" id="change-amount"></td>
                    </tr>
                    <tr id="wallet-row" style="display: none;">
                        <td colspan="4">
                            <div class="wallet-info">
                                <p class="font-bold">Kembalian disimpan ke dompet santri:</p>
                                <p>Jumlah: <span id="wallet-amount"></span></p>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="receipt-notes">
            <p>Catatan:</p>
            <p>1. Kwitansi ini adalah bukti pembayaran yang sah.</p>
            <p>2. Simpan kwitansi ini sebagai bukti pembayaran.</p>
            <p>3. Keluhan terkait pembayaran dapat disampaikan maksimal 7 hari setelah tanggal pembayaran.</p>
        </div>

        <div class="receipt-footer">
            <div class="receipt-signature">
                <p>Santri</p>
                <div class="line"></div>
                <p id="student-name-footer"></p>
            </div>
            <div class="receipt-signature">
                <p>Petugas</p>
                <div class="line"></div>
                <p id="admin-name"></p>
            </div>
        </div>

        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Cetak Kwitansi
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
                Tutup
            </button>
        </div>
    </div>

    <script>
        // Data will be passed from the parent window
        document.addEventListener('DOMContentLoaded', function() {
            // Get data from localStorage (set by the parent window)
            const receiptData = JSON.parse(localStorage.getItem('receiptData'));
            if (!receiptData) {
                document.body.innerHTML = '<div style="text-align: center; margin-top: 100px;"><h2>Data kwitansi tidak ditemukan</h2><p>Silakan kembali ke halaman pembayaran</p></div>';
                return;
            }

            // Clear the localStorage after getting the data
            localStorage.removeItem('receiptData');

            // Fill receipt information
            document.getElementById('receipt-number').textContent = receiptData.receiptNumber;
            document.getElementById('receipt-date').textContent = receiptData.date;
            document.getElementById('student-name').textContent = receiptData.studentName;
            document.getElementById('student-nis').textContent = receiptData.studentNIS;
            document.getElementById('student-class').textContent = receiptData.studentClass;
            document.getElementById('student-name-footer').textContent = receiptData.studentName;
            document.getElementById('admin-name').textContent = receiptData.adminName;

            // Fill payment items
            const paymentItemsContainer = document.getElementById('payment-items');
            let itemsHtml = '';
            receiptData.items.forEach((item, index) => {
                itemsHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.name}</td>
                        <td>${item.period ? item.period : '-'}</td>
                        <td class="text-center">${item.status ? item.status.charAt(0).toUpperCase() + item.status.slice(1) : '-'}</td>
                        <td class="text-right">${item.amount}</td> <!-- Jumlah yang dibayarkan untuk item ini dalam transaksi -->
                        <td class="text-right">${item.paid ? item.paid : '-'}</td> <!-- Total dibayar untuk item ini -->
                        <td class="text-right">${item.remaining ? item.remaining : '-'}</td> <!-- Sisa untuk item ini -->
                    </tr>
                `;
            });
            paymentItemsContainer.innerHTML = itemsHtml;

            // Fill payment total
            document.getElementById('payment-total').textContent = receiptData.total;

            // Fill cash and change information
            document.getElementById('cash-amount').textContent = receiptData.cashAmount;
            
            // Handle change or wallet deposit
            const numericChangeAmount = parseFloat(String(receiptData.changeAmount).replace(/[^0-9,-]+/g, "").replace(',', '.'));
            if (numericChangeAmount > 0) {
                document.getElementById('change-row').style.display = 'table-row';
                document.getElementById('change-amount').textContent = receiptData.changeAmount; // Tetap tampilkan jumlah kembalian
                if (receiptData.walletDeposit) {
                    document.getElementById('wallet-row').style.display = 'table-row';
                    document.getElementById('wallet-amount').textContent = receiptData.changeAmount; // Tampilkan juga di info dompet
                } else {
                    document.getElementById('wallet-row').style.display = 'none';
                }
            } else {
                document.getElementById('change-row').style.display = 'none';
                document.getElementById('wallet-row').style.display = 'none';
            }

            // Auto print if needed
            if (receiptData.autoPrint) {
                setTimeout(() => {
                    window.print();
                }, 1000);
            }
        });
    </script>
</body>
</html>