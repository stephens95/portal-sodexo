<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SummarySalesOrderController extends BaseController
{
    protected $cache;
    protected $apiUrl;

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
        $this->apiUrl = env('sap.api.summaryso.url');
    }

    public function index()
    {
        $data['title'] = 'Addtional Document Invoice';
        $data['segment1'] = 'Report';
        return view('report/sd/summary', $data);
    }

    // public function index()
    // {
    //     $client = \Config\Services::curlrequest();

    //     try {
    //         $response = $client->get($this->apiUrl);
    //         $result = json_decode($response->getBody(), true);

    //         // siapkan data untuk view
    //         $data['title'] = 'Summary Sales Order Report';
    //         $data['segment1'] = 'Report';
    //         $data['summary'] = $result; // kirim hasil API

    //     } catch (\Exception $e) {
    //         // kalau error API
    //         $data['title'] = 'Summary Sales Order Report';
    //         $data['segment1'] = 'Report';
    //         $data['summary'] = [];
    //         $data['error'] = $e->getMessage();
    //     }

    //     return view('report/sd/summary', $data);
    // }

    // public function getData()
    // {
    //     $request = service('request');
    //     $draw   = $request->getVar('draw');
    //     $start  = $request->getVar('start');
    //     $length = $request->getVar('length');
    //     $search = $request->getVar('search[value]');

    //     // ambil data dari API SAP
    //     $client   = \Config\Services::curlrequest();
    //     $response = $client->get($this->apiUrl);
    //     $result   = json_decode($response->getBody(), true);

    //     // kalau nested
    //     if (isset($result['data'])) {
    //         $rows = $result['data'];
    //     } else {
    //         $rows = $result;
    //     }

    //     // filter manual kalau ada search
    //     if (!empty($search)) {
    //         $rows = array_filter($rows, function ($row) use ($search) {
    //             return stripos($row['INV_NO'], $search) !== false
    //                 || stripos($row['END_CUSTOMER'], $search) !== false;
    //         });
    //     }

    //     $total = count($rows);

    //     // paging
    //     $rows = array_slice($rows, $start, $length);

    //     // format sesuai DataTables
    //     $data = [];
    //     foreach ($rows as $row) {
    //         $data[] = [
    //             $row['INV_NO'] ?? '',
    //             $row['END_CUSTOMER'] ?? '',
    //         ];
    //     }

    //     return $this->response->setJSON([
    //         'draw'            => intval($draw),
    //         'recordsTotal'    => $total,
    //         'recordsFiltered' => $total,
    //         'data'            => $data,
    //     ]);
    // }

    public function getData()
    {
        $request = service('request');
        $draw   = $request->getVar('draw');
        $start  = $request->getVar('start');
        $length = $request->getVar('length');
        $search = $request->getVar('search[value]');

        // ambil data dari API SAP
        $client   = \Config\Services::curlrequest();
        $response = $client->get($this->apiUrl);
        $result   = json_decode($response->getBody(), true);

        // kalau nested
        $rows = $result['data'] ?? $result;

        // filter manual kalau ada search
        if (!empty($search)) {
            $rows = array_filter($rows, function ($row) use ($search) {
                return stripos($row['INV_NO'], $search) !== false
                    || stripos($row['END_CUSTOMER'], $search) !== false;
            });
        }

        $total = count($rows);

        // paging
        $rows = array_slice($rows, $start, $length);

        // Ambil semua invoice untuk batch query
        $invoiceNos = array_column($rows, 'INV_NO');

        $db = \Config\Database::connect();
        $builder = $db->table('so_summary');
        $builder->select('invoice, inv, pl, bl, coo, ins, note');
        if (!empty($invoiceNos)) {
            $builder->whereIn('invoice', $invoiceNos);
        }
        $summaryData = $builder->get()->getResultArray();

        // ubah jadi associative array untuk akses cepat
        $summaryMap = [];
        foreach ($summaryData as $s) {
            $summaryMap[$s['invoice']] = $s;
        }

        // format sesuai DataTables
        $data = [];
        foreach ($rows as $row) {
            $invNo = $row['INV_NO'] ?? '';
            $summary = $summaryMap[$invNo] ?? [
                'inv' => null,
                'pl'  => null,
                'bl'  => null,
                'coo' => null,
                'ins' => null,
                'note' => null,
            ];

            $data[] = [
                $invNo,
                // $row['INV_DATE'] ?? '',
                !empty($row['INV_DATE'])
                    ? (new \DateTime(preg_replace('/\.\d+$/', '', $row['INV_DATE'])))->format('d M y')
                    : '',
                $row['END_CUSTOMER'] ?? '',
                !empty($summary['inv']) ? (new \DateTime(preg_replace('/\.\d+$/', '', $summary['inv'])))->format('d M y - H:i') : null,
                !empty($summary['pl'])  ? (new \DateTime(preg_replace('/\.\d+$/', '', $summary['pl'])))->format('d M y - H:i') : null,
                !empty($summary['bl'])  ? (new \DateTime(preg_replace('/\.\d+$/', '', $summary['bl'])))->format('d M y - H:i') : null,
                !empty($summary['coo']) ? (new \DateTime(preg_replace('/\.\d+$/', '', $summary['coo'])))->format('d M y - H:i') : null,
                !empty($summary['ins']) ? (new \DateTime(preg_replace('/\.\d+$/', '', $summary['ins'])))->format('d M y - H:i') : null,
                $summary['note'] ?? '',
            ];
        }

        return $this->response->setJSON([
            'draw'            => intval($draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data,
        ]);
    }


    // public function uploadDocument()
    // {
    //     $invoice     = $this->request->getPost('invoice');
    //     $docType     = $this->request->getPost('doc_type');
    //     $endCustomer = $this->request->getPost('end_customer'); // pastikan dikirim dari front-end
    //     $file        = $this->request->getFile('document');

    //     if ($file && $file->isValid() && !$file->hasMoved()) {
    //         if ($file->getSize() > 3 * 1024 * 1024) { // 3MB
    //             return $this->response->setStatusCode(400)->setBody('File terlalu besar.');
    //         }

    //         // simpan file
    //         // $newName = $invoice . '_' . $docType . '_' . time() . '.' . $file->getExtension();
    //         $newName = $invoice . '_' . $docType . '_' . time() . '.' . $file->getExtension();
    //         // $file->move(WRITEPATH . 'uploads/docs', $newName);
    //         $file->move(WRITEPATH . 'uploads/docs', $newName, true); // true = overwrite

    //         // tentukan field mana yang diupdate
    //         $updateData = [];
    //         if (strtolower($docType) === 'invoice') {
    //             $updateData['invoice'] = 1;
    //         } elseif (strtolower($docType) === 'packing list') {
    //             $updateData['packing_list'] = 1;
    //         }

    //         // update / insert ke tabel so_summary
    //         $db      = \Config\Database::connect();
    //         $builder = $db->table('so_summary');

    //         // cek apakah sudah ada row sesuai invoice & end customer
    //         $exists = $builder->where('invoice', $invoice)
    //             ->where('end_customer', $endCustomer)
    //             ->get()
    //             ->getRow();

    //         if ($exists) {
    //             // update row yang ada
    //             $builder->where('id', $exists->id)->update($updateData);
    //         } else {
    //             // insert baru
    //             $insertData = array_merge([
    //                 'invoice'      => $invoice,
    //                 'end_customer' => $endCustomer
    //                 // 'created_at'   => date('Y-m-d H:i:s')
    //             ], $updateData);

    //             $builder->insert($insertData);
    //         }

    //         return $this->response->setJSON([
    //             'status'  => 'success',
    //             'message' => 'File uploaded & summary updated',
    //             'file'    => $newName
    //         ]);
    //     }

    //     return $this->response->setStatusCode(400)->setBody('File tidak valid.');
    // }

    public function uploadDocument()
    {
        $invoice     = $this->request->getPost('invoice');
        $docType     = $this->request->getPost('doc_type'); // lowercase biar aman
        $endCustomer = $this->request->getPost('end_customer');
        $file        = $this->request->getFile('document');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($file->getSize() > 3 * 1024 * 1024) { // 3MB
                return $this->response->setStatusCode(400)->setBody('File terlalu besar.');
            }

            // validasi ekstensi
            $ext = strtolower($file->getExtension());
            $allowed = ['pdf']; // default

            if ($docType === 'PL') {
                $allowed = ['pdf', 'xls', 'xlsx'];
            }

            if (!in_array($ext, $allowed)) {
                return $this->response->setStatusCode(400)
                    ->setBody("Format tidak valid untuk {$docType}. Hanya boleh: " . implode(', ', $allowed));
            }

            $docsPath = WRITEPATH . 'uploads/docs/';

            // pola file lama (bisa pdf/xlsx/xls)
            $pattern = $docsPath . $invoice . '_' . $docType . '.*';
            foreach (glob($pattern) as $oldFile) {
                @unlink($oldFile); // hapus semua versi lama
            }

            // nama file konsisten
            $newName = $invoice . '_' . $docType . '.' . $file->getExtension();
            // $file->move(WRITEPATH . 'uploads/docs', $newName, true); // overwrite jika ada
            $file->move($docsPath, $newName); // sekarang aman, file lama sudah dibersihkan

            // tentukan field update sesuai docType
            $updateData = [];
            $now = date('Y-m-d H:i:s');

            switch ($docType) {
                case 'INV':
                    $updateData['inv'] = $now;
                    break;

                case 'PL':
                    $updateData['pl'] = $now;
                    break;

                case 'BL_RW':
                    $updateData['bl'] = $now;
                    break;

                case 'COO':
                    $updateData['coo'] = $now;
                    break;

                case 'INS':
                    $updateData['ins'] = $now;
                    break;

                default:
                    return $this->response->setStatusCode(400)->setBody('DocType tidak dikenali.');
            }

            // update / insert ke tabel so_summary
            $db      = \Config\Database::connect();
            $builder = $db->table('so_summary');

            $exists = $builder->where('invoice', $invoice)
                ->where('end_customer', $endCustomer)
                ->get()
                ->getRow();

            if ($exists) {
                $builder->where('id', $exists->id)->update($updateData);
            } else {
                $insertData = array_merge([
                    'invoice'      => $invoice,
                    'end_customer' => $endCustomer,
                ], $updateData);

                $builder->insert($insertData);
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'File uploaded & summary updated',
                'file'    => $newName
            ]);
        }

        return $this->response->setStatusCode(400)->setBody('File tidak valid.');
    }

    public function downloadAll()
    {
        $invoice     = $this->request->getGet('invoice');
        $endCustomer = $this->request->getGet('end_customer');

        $zipDir = WRITEPATH . 'uploads/zip/';
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0777, true);
        }

        // Sanitasi nama file
        $zipName  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $invoice . '_' . $endCustomer) . '.zip';
        $filePath = $zipDir . $zipName;

        $zip = new \ZipArchive();
        if ($zip->open($filePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $docsPath = WRITEPATH . 'uploads/docs/';
            $pattern  = $invoice . '_*';

            foreach (glob($docsPath . $pattern) as $file) {
                $zip->addFile($file, basename($file));
            }

            $zip->close();
        }

        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(500)->setBody('ZIP gagal dibuat.');
        }

        return $this->response->download($filePath, null)->setFileName($zipName);
    }

    public function getNote()
    {
        $invoice     = $this->request->getGet('invoice');
        $endCustomer = $this->request->getGet('end_customer');

        $db      = \Config\Database::connect();
        $builder = $db->table('so_summary');

        $row = $builder->select('note')
            ->where('invoice', $invoice)
            ->where('end_customer', $endCustomer)
            ->get()
            ->getRow();

        return $this->response->setJSON([
            'status' => 'success',
            'note'   => $row->note ?? ''
        ]);
    }

    public function saveNote()
    {
        $invoice     = $this->request->getPost('invoice');
        $endCustomer = $this->request->getPost('end_customer');
        $note        = $this->request->getPost('note');

        // validasi
        if (!$invoice || !$endCustomer) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Invoice dan End Customer wajib diisi.'
            ]);
        }

        if (strlen($note) > 1000) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Catatan terlalu panjang, maksimal 1000 karakter.'
            ]);
        }

        // koneksi DB
        $db = \Config\Database::connect();
        $builder = $db->table('so_summary');

        // cek apakah sudah ada row sesuai invoice & end customer
        $exists = $builder->where('invoice', $invoice)
            ->where('end_customer', $endCustomer)
            ->get()
            ->getRow();

        if ($exists) {
            // update row
            $builder->where('id', $exists->id)->update([
                'note' => $note
            ]);
        } else {
            // insert baru
            $builder->insert([
                'invoice'      => $invoice,
                'end_customer' => $endCustomer,
                'note'         => $note
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Catatan berhasil disimpan.'
        ]);
    }

    public function exportExcelByDate()
    {
        $request   = service('request');
        $startDate = $request->getGet('start_date');
        $endDate   = $request->getGet('end_date');

        // ambil data dari API SAP
        $client   = \Config\Services::curlrequest();
        $response = $client->get($this->apiUrl);
        $result   = json_decode($response->getBody(), true);
        $rows     = $result['data'] ?? $result;

        // filter berdasarkan range tanggal
        $rows = array_filter($rows, function ($row) use ($startDate, $endDate) {
            if (empty($row['INV_DATE'])) return false;
            $date = (new \DateTime(preg_replace('/\.\d+$/', '', $row['INV_DATE'])))->format('Y-m-d');
            return $date >= $startDate && $date <= $endDate;
        });

        // ambil semua invoice untuk batch query DB
        $invoiceNos = array_column($rows, 'INV_NO');
        $db = \Config\Database::connect();
        $builder = $db->table('so_summary');
        $builder->select('invoice, inv, pl, bl, coo, ins, note');
        if (!empty($invoiceNos)) {
            $builder->whereIn('invoice', $invoiceNos);
        }
        $summaryData = $builder->get()->getResultArray();

        // map hasil DB
        $summaryMap = [];
        foreach ($summaryData as $s) {
            $summaryMap[$s['invoice']] = $s;
        }

        // generate excel pakai PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // header
        $sheet->setCellValue('A1', 'Invoice No');
        $sheet->setCellValue('B1', 'Invoice Date');
        $sheet->setCellValue('C1', 'End Customer');
        $sheet->setCellValue('D1', 'Doc Inv');
        $sheet->setCellValue('E1', 'Doc PL');
        $sheet->setCellValue('F1', 'Doc AW Bill');
        $sheet->setCellValue('G1', 'Doc COO');
        $sheet->setCellValue('H1', 'Doc Ins');
        $sheet->setCellValue('I1', 'Note');

        $rowIndex = 2;
        foreach ($rows as $row) {
            $invNo   = $row['INV_NO'] ?? '';
            $summary = $summaryMap[$invNo] ?? [];

            $sheet->setCellValue("A{$rowIndex}", $invNo);
            $sheet->setCellValue(
                "B{$rowIndex}",
                !empty($row['INV_DATE'])
                    ? (new \DateTime(preg_replace('/\.\d+$/', '', $row['INV_DATE'])))->format('d M Y')
                    : ''
            );
            $sheet->setCellValue("C{$rowIndex}", $row['END_CUSTOMER'] ?? '');
            $sheet->setCellValue("D{$rowIndex}", !empty($summary['inv']) ? (new \DateTime($summary['inv']))->format('d M y - H:i') : '');
            $sheet->setCellValue("E{$rowIndex}", !empty($summary['pl'])  ? (new \DateTime($summary['pl']))->format('d M y - H:i')  : '');
            $sheet->setCellValue("F{$rowIndex}", !empty($summary['bl'])  ? (new \DateTime($summary['bl']))->format('d M y - H:i')  : '');
            $sheet->setCellValue("G{$rowIndex}", !empty($summary['coo']) ? (new \DateTime($summary['coo']))->format('d M y - H:i') : '');
            $sheet->setCellValue("H{$rowIndex}", !empty($summary['ins']) ? (new \DateTime($summary['ins']))->format('d M y - H:i') : '');
            $sheet->setCellValue("I{$rowIndex}", $summary['note'] ?? '');
            $rowIndex++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'invoice_export_' . date('YmdHis') . '.xlsx';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment;filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody((function () use ($writer) {
                ob_start();
                $writer->save('php://output');
                return ob_get_clean();
            })());
    }
}
