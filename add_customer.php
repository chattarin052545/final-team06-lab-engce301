<?php
// เชื่อมต่อฐานข้อมูล
$servername = "127.0.0.1"; // ที่อยู่เซิร์ฟเวอร์ MySQL
$username = "root"; // ชื่อผู้ใช้
$password = ""; // รหัสผ่าน (ถ้าไม่มีให้เว้นว่าง)
$dbname = "mystore"; // ชื่อฐานข้อมูล

try {
    // สร้างการเชื่อมต่อ PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // ตั้งค่าให้ PDO แจ้งข้อผิดพลาด
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully<br>"; 
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; 
}

// ตรวจสอบเมื่อผู้ใช้ส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $customer_name = $_POST["customer_name"];
    $customer_lastname = $_POST["customer_lastname"];
    $gender = $_POST["gender"];
    $birthdate = $_POST["birthdate"];
    $address = $_POST["address"];
    $province = $_POST["province"];
    $zipcode = $_POST["zipcode"];
    $telephone = $_POST["telephone"];
    $customer_description = $_POST["customer_description"];
    
    // ข้อมูลบัญชี
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    // ตรวจสอบว่า password และ confirm password ตรงกัน
    if ($password !== $confirm_password) {
        echo "รหัสผ่านไม่ตรงกัน!";
    } else {
        // เข้ารหัสรหัสผ่าน
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // สร้างคำสั่ง SQL เพื่อเพิ่มข้อมูล
        $sql = "INSERT INTO customer (Customer_name, Customer_lastname, Gender, Birthdate, Address, Province, Zipcode, Telephone, Customer_description, username, password) 
                VALUES (:customer_name, :customer_lastname, :gender, :birthdate, :address, :province, :zipcode, :telephone, :customer_description, :username, :password)";
        $stmt = $conn->prepare($sql);
        
        // ผูกค่าจากฟอร์มเข้ากับ SQL
        $stmt->bindParam(':customer_name', $customer_name);
        $stmt->bindParam(':customer_lastname', $customer_lastname);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':province', $province);
        $stmt->bindParam(':zipcode', $zipcode);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':customer_description', $customer_description);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        
        // รันคำสั่ง SQL
        if ($stmt->execute()) {
            echo "เพิ่มลูกค้าสำเร็จ!";
        } else {
            echo "เกิดข้อผิดพลาดในการเพิ่มลูกค้า";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มลูกค้า</title>
    <script>
        function validateForm() {
            let valid = true;

            // ตรวจสอบข้อมูลที่กรอก
            const customerName = document.forms["customerForm"]["customer_name"];
            const customerLastname = document.forms["customerForm"]["customer_lastname"];
            const address = document.forms["customerForm"]["address"];
            const zipcode = document.forms["customerForm"]["zipcode"];
            const telephone = document.forms["customerForm"]["telephone"];
            const username = document.forms["customerForm"]["username"];
            const password = document.forms["customerForm"]["password"];

            // ชื่อ / นามสกุล / ที่อยู่: กรอกน้อยกว่า 3 ตัวอักษร
            if (customerName.value.length < 3) {
                alert("กรุณากรอกชื่อให้ครบอย่างน้อย 3 ตัวอักษร");
                customerName.focus();
                valid = false;
            }
            if (customerLastname.value.length < 3) {
                alert("กรุณากรอกนามสกุลให้ครบอย่างน้อย 3 ตัวอักษร");
                customerLastname.focus();
                valid = false;
            }
            if (address.value.length < 3) {
                alert("กรุณากรอกที่อยู่ให้ครบอย่างน้อย 3 ตัวอักษร");
                address.focus();
                valid = false;
            }

            // รหัสไปรษณีย์ต้องเป็นตัวเลข 5 ตัว
            if (!/^\d{5}$/.test(zipcode.value)) {
                alert("กรุณากรอกรหัสไปรษณีย์ให้ถูกต้อง (5 ตัว)");
                zipcode.focus();
                valid = false;
            }

            // เบอร์โทรศัพท์ต้องเป็นตัวเลข 10 ตัว
            if (!/^\d{10}$/.test(telephone.value)) {
                alert("กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (10 ตัว)");
                telephone.focus();
                valid = false;
            }

            // Username: กรอกน้อยกว่า 5 ตัวอักษร
            if (username.value.length < 5) {
                alert("กรุณากรอก Username อย่างน้อย 5 ตัวอักษร");
                username.focus();
                valid = false;
            }

            // Password: กรอกน้อยกว่า 8 ตัวอักษร
            if (password.value.length < 8) {
                alert("กรุณากรอกรหัสผ่านอย่างน้อย 8 ตัวอักษร");
                password.focus();
                valid = false;
            }

            return valid;
        }

        // ตรวจสอบเฉพาะเบอร์โทรศัพท์ / รหัสไปรษณีย์ขณะกดปุ่ม
        function onlyNumericInput(event) {
            const key = event.key;
            if (!/\d/.test(key) && key !== "Backspace") {
                alert("กรุณากรอกเฉพาะตัวเลข");
                event.preventDefault();
            }
        }
    </script>
</head>
<body>
    <h2>เพิ่มลูกค้า</h2>
    <form name="customerForm" method="POST" action="add_customer.php" onsubmit="return validateForm();">
        <h3>ข้อมูลลูกค้า</h3>
        <label>ชื่อ: </label><input type="text" name="customer_name" onblur="validateForm();" required><br>
        <label>นามสกุล: </label><input type="text" name="customer_lastname" onblur="validateForm();" required><br>
        <label>เพศ: </label>
        <select name="gender" required>
            <option value="">เลือกเพศ</option>
            <option value="ชาย">ชาย</option>
            <option value="หญิง">หญิง</option>
            <option value="อื่นๆ">อื่นๆ</option>
        </select><br>
        <label>วันเดือนปีเกิด: </label><input type="date" name="birthdate" required><br>
        <label>ที่อยู่: </label><input type="text" name="address" onblur="validateForm();" required><br>
        
        <label>จังหวัด: </label>
        <select name="province" required>
            <option value="">เลือกจังหวัด</option>
            <option value="เชียงราย">เชียงราย</option>
            <option value="เชียงใหม่">เชียงใหม่</option>
            <option value="น่าน">น่าน</option>
            <option value="พะเยา">พะเยา</option>
            <option value="แพร่">แพร่</option>
            <option value="แม่ฮ่องสอน">แม่ฮ่องสอน</option>
            <option value="ลำปาง">ลำปาง</option>
            <option value="ลำพูน">ลำพูน</option>
            <option value="อุตรดิตถ์">อุตรดิตถ์</option>
        </select><br>

        <label>รหัสไปรษณีย์: </label><input type="text" name="zipcode" onblur="validateForm();" onkeyup="onlyNumericInput(event);" required><br>
        <label>เบอร์โทร: </label><input type="text" name="telephone" onblur="validateForm();" onkeyup="onlyNumericInput(event);" required><br>
        <label>รายละเอียดอื่นๆ: </label><textarea name="customer_description"></textarea><br>

        <h3>ข้อมูลบัญชี</h3>
        <label>Username: </label><input type="text" name="username" onblur="validateForm();" required><br>
        <label>Password: </label><input type="password" name="password" onblur="validateForm();" required><br>
        <label>Confirm Password: </label><input type="password" name="confirm_password" required><br>
        
        <!-- ปุ่ม Submit และ ยกเลิก -->
        <input type="submit" value="เพิ่มลูกค้า">
        <button type="button" onclick="window.location.href='show_customer.php';">ยกเลิก</button>
    </form>
</body>
</html>

