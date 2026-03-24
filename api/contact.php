<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// ===================================
// GÜVENLİK KATMANI 1: Rate Limiting
// ===================================
$userIP = getUserIP();
$rateLimit = checkRateLimit($userIP);

if (!$rateLimit['allowed']) {
    echo json_encode([
        'success' => false,
        'message' => $rateLimit['message']
    ]);
    exit;
}

// ===================================
// GÜVENLİK KATMANI 2: Honeypot Field
// ===================================
$honeypot = $_POST['website'] ?? '';
if (!empty($honeypot)) {
    // Bot tespit edildi - sessizce reddet
    sleep(2); // Bot'u yanıltmak için gecikme
    echo json_encode([
        'success' => true,
        'message' => 'Your message has been sent successfully!' // Fake success
    ]);
    
    // IP'yi engelle
    blockIP($userIP, 86400); // 24 saat engelle
    exit;
}

// ===================================
// GÜVENLİK KATMANI 3: Time Check
// ===================================
$formLoadTime = intval($_POST['form_time'] ?? 0);
$currentTime = time();
$timeDiff = $currentTime - $formLoadTime;

if ($timeDiff < 3) {
    // 3 saniyeden hızlı gönderim - muhtemelen bot
    echo json_encode([
        'success' => false,
        'message' => 'Please submit the form after filling it out.'
    ]);
    exit;
}

// Form verilerini al ve temizle
$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$message = sanitize($_POST['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields!']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address!']);
    exit;
}

try {
    // Veritabanına kaydet
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);
    
    // E-posta bildirimi gönder (eğer aktifse)
    $emailNotifications = getSetting('email_notifications', '0');
    $notificationEmail = getSetting('notification_email', '');
    
    if ($emailNotifications == '1' && !empty($notificationEmail)) {
        $emailSubject = "New Contact Message: " . $subject;
        $emailBody = "
        You have received a new contact message!\n\n
        Sender: $name\n
        Email: $email\n
        Subject: $subject\n\n
        Message:\n$message\n\n
        ---\n
        This message was sent from kesicioglu.com.
        ";
        
        $headers = "From: noreply@kesicioglu.com\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        @mail($notificationEmail, $emailSubject, $emailBody, $headers);
    }
    
    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
