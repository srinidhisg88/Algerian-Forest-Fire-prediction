<?php
// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['SUPABASE_HOST'];
$db = $_ENV['SUPABASE_DB'];
$user = $_ENV['SUPABASE_USER'];
$password = $_ENV['SUPABASE_PASSWORD'];
$port = $_ENV['SUPABASE_PORT'];

// Connect to PostgreSQL
$conn = pg_connect("host=$host dbname=$db user=$user password=$password port=$port");
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get POST data (expects JSON)
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Prepare and sanitize fields
$Temperature = floatval($data['Temperature'] ?? 0);
$RH = floatval($data['RH'] ?? 0);
$Ws = floatval($data['Ws'] ?? 0);
$Rain = floatval($data['Rain'] ?? 0);
$FFMC = floatval($data['FFMC'] ?? 0);
$DMC = floatval($data['DMC'] ?? 0);
$ISI = floatval($data['ISI'] ?? 0);
$Classes = floatval($data['Classes'] ?? 0);
$Region = floatval($data['Region'] ?? 1);
$FWI = floatval($data['FWI'] ?? 0);

// Insert into database (table: predictions)
$sql = "INSERT INTO predictions (temperature, rh, ws, rain, ffmc, dmc, isi, classes, region, fwi, created_at) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,NOW())";
$result = pg_query_params($conn, $sql, [
    $Temperature, $RH, $Ws, $Rain, $FFMC, $DMC, $ISI, $Classes, $Region, $FWI
]);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Insert failed']);
}

pg_close($conn);
