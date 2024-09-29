<?php
// تضمين المكتبات الضرورية
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // تأكد من أن هذا السطر موجود

// Function to verify email using the selected API
function verifyEmail($email) {
    $apiKey = 'fbcf65394af74bf74c3cfc9ef15f8a1ea0b59576'; // استبدل YOUR_API_KEY بمفتاح API الخاص بك
    $url = "https://api.hunter.io/v2/email-verifier?email={$email}&api_key={$apiKey}";

    $response = file_get_contents($url);
    $result = json_decode($response, true);

    // تحقق مما إذا كان البريد الإلكتروني صالحًا
    return isset($result['data']['status']) && $result['data']['status'] === 'valid';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    // التحقق من صحة الإدخالات
    $errors = [];

    // التحقق من البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@(gmail\.com|yahoo\.com)$/', $email) || !verifyEmail($email)) {
        $errors[] = "البريد الإلكتروني يجب أن يكون من Gmail أو Yahoo وأن يكون عنواناً حقيقياً.";
    }

    // التحقق من رقم الهاتف (يجب أن يكون رقم مصري صالح)
    if (!preg_match('/^01[0-9]{9}$/', $phone)) {
        $errors[] = "رقم الهاتف يجب أن يبدأ بـ 01 ويتكون من 11 رقماً.";
    }

    // التحقق من وجود أخطاء
    if (empty($errors)) {
        // منطق إرسال البريد الإلكتروني باستخدام PHPMailer
        $mail = new PHPMailer(true);

        try {
            // إعدادات SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'amrelamawy712@gmail.com'; // بريدك الإلكتروني
            $mail->Password = 'ygvb oiip bhnx yfpr'; // كلمة مرور التطبيق الخاصة بك
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // المستلم
            $mail->setFrom('your_email@gmail.com', 'Your Name');
            $mail->addAddress('amrelamawy712@gmail.com'); // مستلم البريد

            // المحتوى
            $mail->isHTML(true);
            $mail->Subject = 'New Request from ' . $name;
            $mail->Body = "<strong>Name:</strong> $name<br>
                           <strong>Email:</strong> $email<br>
                           <strong>Phone:</strong> $phone<br>
                           <strong>Message:</strong> $message";

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>
