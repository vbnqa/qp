<?php
header('Content-Type: application/json; charset=utf-8');

// معلومات الاتصال بقاعدة البيانات
$servername = "box4172";
$username = " xoieavmy_bowling";
$password = "WirKEBY?VcaV"; // استبدل بكلمة المرور الحقيقية
$dbname = "xoieavmy_bowling_players"; // استبدل باسم قاعدة البيانات الحقيقية
$port = 3306;

// إنشاء اتصال
$conn = new mysqli($servername, $username, $password, $dbname, $port);
mysqli_set_charset($conn, "utf8");

// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// استقبال البيانات من الطلب
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($data['username']) && isset($data['password'])) {
    $username = $data['username'];
    $password = $data['password'];

    // استخدام Prepared Statements لتجنب SQL Injection
    $sql = "SELECT id, username, password FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        // التحقق من كلمة المرور المشفرة
        if (password_verify($password, $admin['password'])) {
            // تسجيل الدخول ناجح
            echo json_encode(["success" => true]);
        } else {
            // كلمة المرور غير صحيحة
            echo json_encode(["success" => false, "message" => "اسم المستخدم أو كلمة المرور غير صحيحة"]);
        }
    } else {
        // المستخدم غير موجود
        echo json_encode(["success" => false, "message" => "اسم المستخدم أو كلمة المرور غير صحيحة"]);
    }

    $stmt->close();
} else {
    // طلب غير صالح
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "طلب غير صالح"]);
}

$conn->close();
?>