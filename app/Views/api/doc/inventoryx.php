<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>API Documentation - Inventory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">

    <div class="container py-5">
        <h1 class="mb-4">📘 API Documentation - Inventory</h1>

        <!-- Token Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Authentication
            </div>
            <div class="card-body">
                <p>Gunakan token untuk mengakses semua endpoint API. Token berlaku selama <strong>24 jam</strong>.</p>
                <button id="btnGenerate" class="btn btn-success">Generate Token</button>
                <div id="tokenResult" class="mt-3"></div>
            </div>
        </div>

        <!-- Endpoint Section -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                Endpoint: Get Inventory
            </div>
            <div class="card-body">
                <p><strong>URL:</strong> <code>GET /get-inventory</code></p>
                <p><strong>Headers:</strong></p>
                <ul>
                    <li><code>Authorization: Bearer &lt;token&gt;</code></li>
                    <li><code>Accept: application/json</code></li>
                </ul>

                <p><strong>Query Parameters (opsional):</strong></p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Field API</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>FCASTQONO</td>
                            <td>FORECAST_QUOTATION</td>
                            <td>No. Forecast Quotation</td>
                        </tr>
                        <tr>
                            <td>FCASTSONO</td>
                            <td>SO_FORECAST</td>
                            <td>No. SO Forecast</td>
                        </tr>
                        <tr>
                            <td>ALCTDSONO</td>
                            <td>SO_ACTUAL</td>
                            <td>No. SO Actual</td>
                        </tr>
                        <tr>
                            <td>CUSTNAME</td>
                            <td>CUSTOMER_NAME</td>
                            <td>Nama Customer</td>
                        </tr>
                        <tr>
                            <td>STYLE</td>
                            <td>STYLE</td>
                            <td>Nama Style Produk</td>
                        </tr>
                        <tr>
                            <td>COLOUR</td>
                            <td>COLOR</td>
                            <td>Warna Produk</td>
                        </tr>
                        <tr>
                            <td>UNISIZE</td>
                            <td>SIZE</td>
                            <td>Ukuran Produk</td>
                        </tr>
                        <tr>
                            <td>QTY</td>
                            <td>QTY</td>
                            <td>Jumlah Produk</td>
                        </tr>
                        <tr>
                            <td>PRODYEAR</td>
                            <td>PROD_YEAR</td>
                            <td>Tahun Produksi</td>
                        </tr>
                        <tr>
                            <td>COUNTRY</td>
                            <td>COUNTRY_NAME</td>
                            <td>Negara Tujuan</td>
                        </tr>
                    </tbody>
                </table>

                <p><strong>Contoh Request:</strong></p>
                <pre><code>GET /get-inventory?CUSTNAME=Initial%20SAS%20EIM660
Authorization: Bearer &lt;token&gt;
Accept: application/json
</code></pre>

                <p><strong>Contoh Response:</strong></p>
                <pre><code>[
  {
    "FCASTQONO": "786099",
    "FCASTSONO": "1510004963",
    "ALCTDSONO": "",
    "CUSTNAME": "Initial SAS EIM660",
    "ALCTDQONO": "",
    "ALCTDCUSTPONO": "",
    "STYLE": "94866 VES CUIS SODEXO MAR 2070N",
    "COLOUR": "NAVY",
    "UNISIZE": "L",
    "QTY": 15,
    "PRODYEAR": 2025,
    "AGINGDAYS": 1,
    "COUNTRY": "France"
  }
]</code></pre>
            </div>
        </div>
    </div>

    <script>
        $("#btnGenerate").on("click", function() {
            $.get("/generate-token", function(data) {
                if (data.success) {
                    $("#tokenResult").html(
                        `<div class="alert alert-success">
             <strong>Token:</strong> ${data.token}<br>
             <strong>Expired At:</strong> ${data.expired_at}
           </div>`
                    );
                } else {
                    $("#tokenResult").html(
                        `<div class="alert alert-danger">Gagal generate token.</div>`
                    );
                }
            });
        });
    </script>

</body>

</html>