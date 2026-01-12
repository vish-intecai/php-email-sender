<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| Load .env manually
|--------------------------------------------------------------------------
*/
$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '.env file not found'
    ]);
    exit;
}

$env = parse_ini_file($envFile, false, INI_SCANNER_RAW);
foreach ($env as $key => $value) {
    $_ENV[$key] = $value;
}

/*
|--------------------------------------------------------------------------
| Validate required env keys
|--------------------------------------------------------------------------
*/
$requiredEnv = [
    'MAIL_HOST',
    'MAIL_PORT',
    'MAIL_USERNAME',
    'MAIL_PASSWORD',
    'MAIL_FROM_ADDRESS',
    'MAIL_FROM_NAME'
];

foreach ($requiredEnv as $key) {
    if (empty($_ENV[$key])) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => "Missing env key: {$key}"
        ]);
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Request validation
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON body'
    ]);
    exit;
}

$required = ['toEmail', 'toName', 'subject', 'htmlBody'];

foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => "Missing field: {$field}"
        ]);
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Send email
|--------------------------------------------------------------------------
*/
try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_USERNAME'];
    $mail->Password   = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int) $_ENV['MAIL_PORT'];

    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
    $mail->Timeout = 10;

    $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    $mail->addAddress($input['toEmail'], $input['toName']);

    $mail->Subject = $input['subject'];
    $mail->Body    = $input['htmlBody'];
    $mail->AltBody = $input['textBody'] ?? strip_tags($input['htmlBody']);

    $mail->send();

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Email sent'
    ]);
} catch (Exception $e) {
    error_log('Mail Error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Email sending failed'
    ]);
}
