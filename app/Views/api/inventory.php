<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f9fafb;
            color: #111;
        }

        header {
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 15px 30px;
            position: sticky;
            top: 0;
        }

        header h1 {
            margin: 0;
            font-size: 20px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }

        h2 {
            margin-top: 40px;
            font-size: 24px;
        }

        h3 {
            margin-top: 20px;
            font-size: 18px;
        }

        pre {
            background: #111827;
            /* tetap gelap */
            color: #ffffff;
            /* putih jelas */
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 14px;
        }

        pre code {
            color: #ffffff;
            /* pastikan code juga putih */
            font-family: monospace;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
        }

        th {
            background: #f1f1f1;
            text-align: left;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            background: #e5e7eb;
        }

        footer {
            background: #fff;
            border-top: 1px solid #ddd;
            padding: 15px 30px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <header>
        <h1>📘 API Documentation</h1>
    </header>

    <div class="container">
        <section>
            <h2>Quick Start</h2>
            <p>Gunakan perintah berikut untuk menguji koneksi API:</p>
            <pre><code>curl -X GET "https://api.yourdomain.com/v1/health" -H "Accept: application/json"</code></pre>
            <p>Jika berhasil akan mengembalikan:</p>
            <pre><code>{"status": "ok"}</code></pre>
        </section>

        <section>
            <h2>Autentikasi</h2>
            <p>Gunakan header <code>Authorization: Bearer &lt;API_KEY&gt;</code> untuk setiap request.</p>
            <p>Contoh:</p>
            <pre><code>Authorization: Bearer 123456abcdef</code></pre>
        </section>

        <section>
            <h2>Endpoints</h2>

            <h3><span class="badge">GET</span> /inventory</h3>
            <p>Ambil daftar stok dengan pagination.</p>
            <table>
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Tipe</th>
                        <th>Wajib</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>page</td>
                        <td>number</td>
                        <td>Tidak</td>
                        <td>Nomor halaman (default 1)</td>
                    </tr>
                    <tr>
                        <td>limit</td>
                        <td>number</td>
                        <td>Tidak</td>
                        <td>Jumlah data per halaman (default 10)</td>
                    </tr>
                    <tr>
                        <td>search</td>
                        <td>string</td>
                        <td>Tidak</td>
                        <td>Kata kunci pencarian</td>
                    </tr>
                </tbody>
            </table>

            <p><b>Contoh Response:</b></p>
            <pre><code>[
    {
        "PROD_YEAR": 2023,
        "BATCH": "G3230807M1",
        "MATERIAL": "GMSDXPOL2140X006",
        "COLOR": "WHITE",
        "CUSTOMER": "2000000383",
        "CUSTOMER_NAME": "Direct Corporate Clothing B.V",
        "QTY": 1.000,
        "SO": "1510002468",
        "LINE_ITEM": 1906,
        "FORECAST_QUOTATION": "MAF6/PR00000027",
        "PRO": "900000011196",
        "SO_FORECAST": "1510002468",
        "STATUS": "Special Stock",
        "SO_ACTUAL": "-",
        "SO_ITEM_ACTUAL": 0,
        "QUOT_ACTUAL": "-",
        "PO_BUYER": "-",
        "STYLE": "SDX2140XWH Short Sleeved Polo Shirt (Laundry), White",
        "SIZE": "2XL",
        "GR_DATE": "20230824"
    },
]</code></pre>

            <h3><span class="badge">POST</span> /orders</h3>
            <p>Membuat sales order baru.</p>
            <p><b>Request Body:</b></p>
            <pre><code>{
  "customer_code": "2000000002",
  "items": [
    { "material": "SDX2140XWH", "qty": 10, "uom": "PCS" },
    { "material": "SDX9999BLK", "qty": 5, "uom": "PCS" }
  ],
  "notes": "Urgent order"
}</code></pre>

            <p><b>Contoh Request (PHP - CodeIgniter 4):</b></p>
            <pre><code>&lt;?php
$client = \Config\Services::curlrequest();
$response = $client->post('https://api.yourdomain.com/v1/orders', [
  'headers' => [
    'Authorization' => 'Bearer YOUR_API_KEY',
    'Content-Type'  => 'application/json'
  ],
  'json' => [
    'customer_code' => '2000000002',
    'items' => [
      [ 'material' => 'SDX2140XWH', 'qty' => 10, 'uom' => 'PCS' ],
      [ 'material' => 'SDX9999BLK', 'qty' => 5, 'uom' => 'PCS' ]
    ],
    'notes' => 'Urgent order'
  ]
]);

echo $response->getBody();
?&gt;</code></pre>

            <p><b>Contoh Response:</b></p>
            <pre><code>{
  "success": true,
  "so_no": "SO-20250821-0001",
  "created_at": "2025-08-21T02:00:00Z"
}</code></pre>
        </section>

        <section>
            <h2>Format Error</h2>
            <pre><code>{
  "success": false,
  "error": {
    "code": "ERR_CODE",
    "message": "Deskripsi error",
    "details": {}
  }
}</code></pre>
        </section>
    </div>

    <footer>
        © <?php echo date('Y'); ?> Your Company. Hubungi: <a href="mailto:dev@yourdomain.com">dev@yourdomain.com</a>
    </footer>

</body>

</html>