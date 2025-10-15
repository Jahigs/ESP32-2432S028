<?php
/**
 * Simple PHP endpoint for Node-RED integration with ESPHome copy counter.
 * 
 * Expects: 
 *   GET parameter `json` containing a JSON string like:
 *   {"printer":"ZD621","qty":"5","key":"a8371d84f4b28a32ab4b7f8ef5033bec4869090e8fa5caab705a88db10a89c54"}
 * 
 * Returns:
 *   JSON response with success or error message.
 */

header('Content-Type: application/json');

// --- CONFIGURATION ---
$API_KEY = 'a8371d84f4b28a32ab4b7f8ef5033bec4869090e8fa5caab705a88db10a89c54';
$PRINTERS = [
    'ZD621' => '/dev/usb/lp0',   // Adjust for your environment
    // You can add more printers here:
    // 'ZD420' => '/dev/usb/lp1',
];

// --- INPUT VALIDATION ---
if (!isset($_GET['json'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing json parameter']);
    exit;
}

$json = $_GET['json'];
$data = json_decode($json, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// --- SECURITY CHECK ---
if (!isset($data['key']) || $data['key'] !== $API_KEY) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// --- PARAMETER EXTRACTION ---
$printerName = $data['printer'] ?? null;
$qty = intval($data['qty'] ?? 0);

if (!$printerName || $qty < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid parameters']);
    exit;
}

// --- PRINTER VALIDATION ---
if (!array_key_exists($printerName, $PRINTERS)) {
    http_response_code(404);
    echo json_encode(['error' => 'Unknown printer']);
    exit;
}

$printerPath = $PRINTERS[$printerName];

// --- MESSAGE CREATION (ZPL Example) ---
$zplTemplate = "^XA
^FO50,50^A0N,40,40^FDTest Print Job: %d Copies^FS
^XZ";

// --- PRINT LOOP ---
for ($i = 1; $i <= $qty; $i++) {
    $zpl = sprintf($zplTemplate, $i);
    $result = file_put_contents($printerPath, $zpl);
    if ($result === false) {
        http_response_code(500);
        echo json_encode(['error' => "Failed to write to printer on iteration $i"]);
        exit;
    }
}

// --- SUCCESS RESPONSE ---
echo json_encode([
    'status' => 'success',
    'printer' => $printerName,
    'copies_printed' => $qty
]);
?>
