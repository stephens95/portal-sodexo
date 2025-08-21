<!-- app/Views/api_documentation.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>API Documentation - Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f6f9;
            color: #333;
        }

        header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 28px;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2c3e50;
            margin-top: 30px;
        }

        .endpoint {
            background: #ecf0f1;
            border-left: 5px solid #3498db;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .method {
            display: inline-block;
            font-weight: bold;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            margin-right: 10px;
            font-size: 13px;
        }

        .get {
            background: #27ae60;
        }

        .post {
            background: #2980b9;
        }

        .put {
            background: #f39c12;
        }

        .delete {
            background: #c0392b;
        }

        pre {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }

        footer {
            text-align: center;
            padding: 20px;
            margin-top: 30px;
            font-size: 14px;
            background: #ecf0f1;
            color: #555;
        }
    </style>
</head>

<body>
    <header>
        <h1>Inventory - API Documentation</h1>
        <p>Example usage and endpoint guide</p>
    </header>

    <div class="container">
        <h2>Base URL</h2>
        <p><code>http://yourdomain.com/api/</code></p>

        <h2>Endpoints</h2>

        <div class="endpoint">
            <span class="method get">GET</span>
            <strong>/inventory</strong>
            <p>Retrieve all inventory data.</p>
            <h4>Example Request (PHP cURL)</h4>
            <pre>
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://yourdomain.com/api/inventory");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;
      </pre>
        </div>

        <!-- <div class="endpoint">
            <span class="method post">POST</span>
            <strong>/inventory/add</strong>
            <p>Create a new inventory record.</p>
            <h4>Parameters</h4>
            <ul>
                <li><strong>material</strong> (string) – Material code</li>
                <li><strong>qty</strong> (int) – Quantity</li>
            </ul>
            <h4>Example Request (PHP cURL)</h4>
            <pre>
$data = [
    "material" => "MAT001",
    "qty" => 10
];
$ch = curl_init("http://yourdomain.com/api/inventory/add");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;
      </pre>
        </div> -->

        <h2>Response Format</h2>
        <pre>
[
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
    }
]
    </pre>
    </div>

    <footer>
        &copy; <?= date('Y'); ?> Argo Manunggal Triasta - API Documentation
    </footer>
</body>

</html>