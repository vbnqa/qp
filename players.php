<?php
header('Content-Type: application/json; charset=utf-8'); // إضافة charset=utf-8 لدعم اللغة العربية

// معلومات الاتصال بقاعدة البيانات
$servername = "sql209.thsite.top";
$username = "thsi_38393066";
$password = "your_mysql_password"; // استبدل بكلمة المرور الحقيقية
$dbname = "your_database_name"; // استبدل باسم قاعدة البيانات الحقيقية
$port = 3306;

// إنشاء اتصال
$conn = new mysqli($servername, $username, $password, $dbname, $port);
mysqli_set_charset($conn, "utf8"); // تعيين ترميز الاتصال إلى UTF-8

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// الحصول على معرف اللاعب من الرابط (إذا وجد)
$playerId = isset($_GET['id']) ? $_GET['id'] : null;

// التعامل مع طلبات GET (جلب لاعب واحد أو كل اللاعبين)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($playerId) {
        // جلب لاعب واحد
        $sql = "SELECT * FROM players WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $playerId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $player = $result->fetch_assoc();
            echo json_encode($player, JSON_UNESCAPED_UNICODE); // عرض البيانات كـ JSON مع دعم اللغة العربية
        } else {
            http_response_code(404); // إرجاع رمز 404 إذا لم يتم العثور على اللاعب
            echo json_encode(["message" => "Player not found"]);
        }
        $stmt->close();
    } else {
        // جلب جميع اللاعبين
        $sql = "SELECT * FROM players";
        $result = $conn