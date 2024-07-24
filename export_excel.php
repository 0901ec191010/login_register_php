<?php
require_once "database.php";

header("Content-Type: application/xlsx");
header("Content-Disposition: attachment; filename=users.xlsx");

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql = "SELECT id, full_name, email FROM users";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Full Name');
$sheet->setCellValue('C1', 'Email');

$rowCount = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowCount, $row['id']);
    $sheet->setCellValue('B' . $rowCount, $row['full_name']);
    $sheet->setCellValue('C' . $rowCount, $row['email']);
    $rowCount++;
}

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");

mysqli_close($conn);
exit();
?>
