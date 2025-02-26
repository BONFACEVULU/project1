<?php
require_once '../config/dbconnection.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

$query = "SELECT b.*, u.name AS user_name, c.name AS class_name, u.email AS user_email, 
          i.name AS instructor_name, c.price AS class_price 
          FROM bookings b 
          JOIN users u ON b.user_id = u.id 
          JOIN classes c ON b.class_id = c.id 
          JOIN instructors i ON c.instructor_id = i.id";
$result = $db->query($query);
$bookings = $result->fetchAll(PDO::FETCH_ASSOC);

if ($_GET['format'] === 'pdf') {
    // Generate PDF
    $html = '<h1>Bookings Report</h1>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0">';
    $html .= '<thead><tr><th>ID</th><th>Client Name</th><th>Instructor Name</th><th>Class Name</th><th>Amount</th><th>Booking Date</th><th>Payment Reference</th><th>Status</th></tr></thead>';
    $html .= '<tbody>';
    foreach ($bookings as $index => $booking) {
        $html .= '<tr>';
        $html .= '<td>' . ($index + 1) . '</td>';
        $html .= '<td>' . htmlspecialchars($booking['user_name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($booking['instructor_name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($booking['class_name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($booking['class_price']) . '</td>';
        $html .= '<td>' . htmlspecialchars($booking['booking_date']) . '</td>';
        $html .= '<td>' . htmlspecialchars($booking['payment_reference']) . '</td>';
        $html .= '<td>' . htmlspecialchars($booking['status']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('bookings_report.pdf');
} elseif ($_GET['format'] === 'excel') {
    // Generate Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Bookings Report');

    // Set header
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Client Name');
    $sheet->setCellValue('C1', 'Instructor Name');
    $sheet->setCellValue('D1', 'Class Name');
    $sheet->setCellValue('E1', 'Amount');
    $sheet->setCellValue('F1', 'Booking Date');
    $sheet->setCellValue('G1', 'Payment Reference');
    $sheet->setCellValue('H1', 'Status');

    // Set data
    foreach ($bookings as $index => $booking) {
        $sheet->setCellValue('A' . ($index + 2), $index + 1);
        $sheet->setCellValue('B' . ($index + 2), $booking['user_name']);
        $sheet->setCellValue('C' . ($index + 2), $booking['instructor_name']);
        $sheet->setCellValue('D' . ($index + 2), $booking['class_name']);
        $sheet->setCellValue('E' . ($index + 2), $booking['class_price']);
        $sheet->setCellValue('F' . ($index + 2), $booking['booking_date']);
        $sheet->setCellValue('G' . ($index + 2), $booking['payment_reference']);
        $sheet->setCellValue('H' . ($index + 2), $booking['status']);
    }

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="bookings_report.xlsx"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}
?>
