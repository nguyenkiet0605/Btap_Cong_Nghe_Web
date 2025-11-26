<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hiển thị danh sách sinh viên</title>
    <style>
        /* CSS đơn giản để tạo bảng giống Excel */
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            /* Màu nền cho tiêu đề */
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
            /* Màu xen kẽ cho dễ nhìn */
        }
    </style>
</head>

<body>

    <h2 style="text-align:center">Danh sách sinh viên (Từ file CSV)</h2>

    <?php
    // 1. Tên file cần đọc (đúng tên file bạn gửi)
    $filename = '65HTTT_Danh_sach_diem_danh.csv';

    // 2. Kiểm tra file có tồn tại không
    if (file_exists($filename)) {

        // 3. Mở file chế độ đọc ('r')
        $file = fopen($filename, "r");

        echo "<table>";

        // --- PHẦN 1: ĐỌC TIÊU ĐỀ (Dòng đầu tiên) ---
        // fgetcsv sẽ lấy dòng đầu và con trỏ file tự động xuống dòng tiếp theo
        $header = fgetcsv($file);
        if ($header) {
            echo "<thead><tr>";
            foreach ($header as $col_name) {
                echo "<th>" . htmlspecialchars($col_name) . "</th>";
            }
            echo "</tr></thead>";
        }

        // --- PHẦN 2: ĐỌC DỮ LIỆU (Các dòng còn lại) ---
        echo "<tbody>";
        while (($row = fgetcsv($file)) !== false) {
            // $row là một mảng chứa dữ liệu của 1 sinh viên (1 dòng)
            // Ví dụ: $row[0] là username, $row[2] là họ...

            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";

            // --- QUAN TRỌNG: ĐÂY LÀ CHỖ ĐỂ LƯU VÀO CSDL ---
            // Tại đây bạn có thể viết câu lệnh SQL INSERT.
            // Ví dụ: $sql = "INSERT INTO users VALUES ('$row[0]', '$row[1]', ...)";
        }
        echo "</tbody>";
        echo "</table>";

        // 4. Đóng file
        fclose($file);
    } else {
        echo "<p style='color:red'>Lỗi: Không tìm thấy file $filename</p>";
    }
    ?>

</body>

</html>