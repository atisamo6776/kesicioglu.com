<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$message = sanitize($_POST['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Lütfen tüm alanları doldurun!']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Geçerli bir e-posta adresi girin!']);
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
        $emailSubject = "Yeni İletişim Mesajı: " . $subject;
        $emailBody = "
        Yeni bir iletişim mesajı aldınız!\n\n
        Gönderen: $name\n
        E-posta: $email\n
        Konu: $subject\n\n
        Mesaj:\n$message\n\n
        ---\n
        Bu mesaj kesicioglu.com sitesinden gönderildi.
        ";
        
        $headers = "From: noreply@kesicioglu.com\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        @mail($notificationEmail, $emailSubject, $emailBody, $headers);
    }
    
    echo json_encode(['success' => true, 'message' => 'Mesajınız başarıyla gönderildi!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu. Lütfen tekrar deneyin.']);
}
