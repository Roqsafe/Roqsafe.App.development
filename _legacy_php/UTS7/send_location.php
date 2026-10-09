<?php
// send_location.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Include the standard DB connection logic
require_once __DIR__ . '/../lib/db.php';

header('Content-Type: application/json');

// Get JSON raw POST logic
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing JSON payload']);
    exit;
}

// Replace IP if needed (Prefer Server over Client where applicable, but respect client info)
$clientIp = isset($data['ipAddress']) ? $data['ipAddress'] : 'Unknown';
if ($clientIp === 'Unknown') {
    $clientIp = $_SERVER['REMOTE_ADDR'];
}

function getVal($array, $key, $default = 'N/A') {
    return isset($array[$key]) && $array[$key] !== '' && $array[$key] !== null ? $array[$key] : $default;
}

// Build the Location Info block
$locationInfo = "";
if (isset($data['location']) && is_array($data['location'])) {
    $loc = $data['location'];
    $lat = getVal($loc, 'latitude');
    $lon = getVal($loc, 'longitude');
    $acc = getVal($loc, 'accuracy');
    
    $ts_ms = getVal($loc, 'timestamp');
    $locDate = is_numeric($ts_ms) ? date('Y-m-d\TH:i:s.000\Z', $ts_ms / 1000) : $ts_ms; 
    
    $addr = isset($loc['address']) ? $loc['address'] : [];
    $country = getVal($addr, 'country');
    $city = getVal($addr, 'city');
    $street = getVal($addr, 'street');
    $houseNumber = getVal($addr, 'houseNumber');
    $postal = getVal($addr, 'postalCode');
    $formatted = getVal($addr, 'formattedAddress');

    $locationInfo = "
    Location Information:
    - Latitude: $lat
    - Longitude: $lon
    - Accuracy: $acc meters
    - Timestamp: $locDate
    
    Address Information:
    - Country: $country
    - City: $city
    - Street: $street
    - House Number: $houseNumber
    - Postal Code: $postal
    - Full Address: $formatted";
} else {
    $locationInfo = "\nLocation: Not available";
}

$timestamp = getVal($data, 'timestamp');
$browserName = getVal($data['browser'] ?? [], 'name');
$browserVersion = getVal($data['browser'] ?? [], 'version');
$osName = getVal($data['os'] ?? [], 'name');
$osVersion = getVal($data['os'] ?? [], 'version');
$deviceModel = getVal($data['device'] ?? [], 'model');
$deviceType = getVal($data['device'] ?? [], 'type');
$deviceVendor = getVal($data['device'] ?? [], 'vendor');
$screenWidth = getVal($data['screen'] ?? [], 'width');
$screenHeight = getVal($data['screen'] ?? [], 'height');
$colorDepth = getVal($data['screen'] ?? [], 'colorDepth');

$language = getVal($data, 'language');
$platform = getVal($data, 'platform');
$referrer = getVal($data, 'referrer', 'Direct visit');
if ($referrer === 'N/A' || $referrer === '') $referrer = 'Direct visit';
$userAgent = getVal($data, 'userAgent');

// Exact formatted string as requested
$emailBodyText = "
    New User Visit Details:
    
    Timestamp: $timestamp
    IP Address: $clientIp
    $locationInfo
    
    Browser Information:
    - Name: $browserName
    - Version: $browserVersion
    
    Operating System:
    - Name: $osName
    - Version: $osVersion
    
    Device:
    - Model: $deviceModel
    - Type: $deviceType
    - Vendor: $deviceVendor
    
    Screen:
    - Resolution: {$screenWidth}x{$screenHeight}
    - Color Depth: $colorDepth
    
    Additional Information:
    - Language: $language
    - Platform: $platform
    - Referrer: $referrer
    
    User Agent: $userAgent
  ";

// ─────────────────────────────────────────────────────────────
// Save to Roqsafe / SVI Database
// ─────────────────────────────────────────────────────────────
try {
    // Attempt remote roqsafe DB first, fallback to svi_db
    try {
        $pdo = svi_remote_db();
    } catch (Throwable $e) {
        $pdo = svi_db();
    }

    $stmt = $pdo->prepare("INSERT INTO `live_user_active` (
        `ip_address`, `country`, `city`, `street`, `postal_code`, `full_address`, 
        `latitude`, `longitude`, `accuracy`, `location_timestamp`, 
        `os_name`, `os_version`, `device_model`, `device_type`, `device_vendor`, `website_url`
    ) VALUES (
        :ip, :country, :city, :street, :postal, :full_addr,
        :lat, :lon, :acc, :loc_ts,
        :os_name, :os_version, :dev_model, :dev_type, :dev_vendor, :website_url
    )");

    $websiteUrl = getVal($_SERVER, 'HTTP_REFERER', '');
    if ($websiteUrl === 'N/A' || $websiteUrl === '') {
        $websiteUrl = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    }

    $stmt->execute([
        ':ip' => $clientIp,
        ':country' => isset($country) && $country !== 'N/A' ? $country : null,
        ':city' => isset($city) && $city !== 'N/A' ? $city : null,
        ':street' => isset($street) && $street !== 'N/A' ? $street : null,
        ':postal' => isset($postal) && $postal !== 'N/A' ? $postal : null,
        ':full_addr' => isset($formatted) && $formatted !== 'N/A' ? $formatted : null,
        ':lat' => isset($lat) && $lat !== 'N/A' ? $lat : null,
        ':lon' => isset($lon) && $lon !== 'N/A' ? $lon : null,
        ':acc' => isset($acc) && $acc !== 'N/A' ? $acc : null,
        ':loc_ts' => isset($locDate) && $locDate !== 'N/A' ? $locDate : null,
        ':os_name' => $osName !== 'N/A' ? $osName : null,
        ':os_version' => $osVersion !== 'N/A' ? $osVersion : null,
        ':dev_model' => $deviceModel !== 'N/A' ? $deviceModel : null,
        ':dev_type' => $deviceType !== 'N/A' ? $deviceType : null,
        ':dev_vendor' => $deviceVendor !== 'N/A' ? $deviceVendor : null,
        ':website_url' => $websiteUrl
    ]);
} catch (Exception $dbErr) {
    // Silently continue (or log error) if DB insertion fails
    error_log("[UTS7 Database Error]: " . $dbErr->getMessage());
}


// PHPMailer Configuration
$mail = new PHPMailer(true);

try {
    // Server settings
    // $mail->SMTPDebug = 2; // Enable verbose debug output (commented out for production JSON response)
    $mail->isSMTP();
    $mail->Host       = 'mail.privateemail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'mailsupport@ericksonholding.com';
    $mail->Password   = 'Lullie#22';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Equivalent to 'ssl' port 465
    $mail->Port       = 465;

    // Recipients
    $mail->setFrom('mailsupport@ericksonholding.com', 'User Tracking System 7');
    $mail->addAddress('mailsupport@ericksonholding.com');

    // Content
    $mail->isHTML(false); // Text only since it's a raw output requirement
    $mail->Subject = 'New UTS7 Uplink Received - ' . $clientIp;
    $mail->Body    = ltrim($emailBodyText);

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Email sent successfully']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
}
