<?php
// Cấu hình tên file dữ liệu
$filename = 'questions.txt';
$questions = [];

// Hàm để đọc và phân tích file
function loadQuestions($filename)
{
    if (!file_exists($filename)) {
        return [];
    }

    $content = file_get_contents($filename);
    // Tách các khối câu hỏi dựa trên dòng trống (double line break)
    // Sử dụng preg_split để xử lý đa nền tảng (Windows/Linux)
    $blocks = preg_split('/\n\s*\n/', trim($content));

    $parsedQuestions = [];

    foreach ($blocks as $block) {
        $lines = explode("\n", trim($block));
        $qData = [
            'question_text' => '',
            'options' => [],
            'correct_answer' => [],
            'type' => 'radio' // Mặc định là chọn 1 (radio), nếu nhiều đáp án thì chuyển sang checkbox
        ];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Kiểm tra xem dòng này có phải là đáp án đúng không (ANSWER: ...)
            if (strpos($line, 'ANSWER:') === 0) {
                $ansString = trim(substr($line, 7)); // Lấy phần sau "ANSWER:"
                // Tách các đáp án đúng (ví dụ: A, B -> ['A', 'B'])
                $qData['correct_answer'] = array_map('trim', explode(',', $ansString));

                // Nếu có nhiều hơn 1 đáp án đúng, chuyển sang loại checkbox
                if (count($qData['correct_answer']) > 1) {
                    $qData['type'] = 'checkbox';
                }
            }
            // Kiểm tra xem dòng này có phải là các lựa chọn A, B, C, D không
            elseif (preg_match('/^([A-Z])\.\s+(.*)$/', $line, $matches)) {
                // $matches[1] là A, B, C...
                // $matches[2] là nội dung đáp án
                $qData['options'][$matches[1]] = $matches[2];
            }
            // Nếu không phải 2 loại trên, thì nó là nội dung câu hỏi (hoặc tiêu đề)
            else {
                if ($line !== "Bài 02: Đọc tệp tin văn bản") { // Bỏ qua dòng tiêu đề nếu có
                    $qData['question_text'] .= $line . " ";
                }
            }
        }

        if (!empty($qData['options']) && !empty($qData['correct_answer'])) {
            $parsedQuestions[] = $qData;
        }
    }

    return $parsedQuestions;
}

// Tải câu hỏi
$questions = loadQuestions($filename);
$isSubmitted = ($_SERVER['REQUEST_METHOD'] === 'POST');
$totalScore = 0;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Tập Trắc Nghiệm Android</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .question-block {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .question-text {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .options label {
            display: block;
            padding: 5px 0;
            cursor: pointer;
        }

        .options input {
            margin-right: 10px;
        }

        /* CSS cho kết quả */
        .result-box {
            text-align: center;
            background: #e8f5e9;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #c8e6c9;
            border-radius: 5px;
            color: #2e7d32;
            font-weight: bold;
            font-size: 1.2em;
        }

        .correct {
            color: green;
            font-weight: bold;
        }

        .incorrect {
            color: red;
            font-weight: bold;
        }

        .explain {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 15px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background: #2980b9;
        }

        /* Highlight đáp án khi đã nộp bài */
        .option-correct-bg {
            background-color: #d4edda;
            padding-left: 5px;
            border-radius: 4px;
        }

        .option-wrong-bg {
            background-color: #f8d7da;
            padding-left: 5px;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Bài Thi Trắc Nghiệm Android</h1>

        <?php if ($isSubmitted): ?>
            <?php
            $correctCount = 0;
            foreach ($questions as $index => $q) {
                $userAnswers = $_POST['q' . $index] ?? [];
                if (!is_array($userAnswers)) $userAnswers = [$userAnswers];

                // So sánh mảng đáp án người dùng và đáp án đúng
                // Sắp xếp để so sánh chính xác (ví dụ A,C == C,A)
                sort($userAnswers);
                $correctAnswers = $q['correct_answer'];
                sort($correctAnswers);

                if ($userAnswers === $correctAnswers) {
                    $correctCount++;
                }
            }
            $scorePercent = round(($correctCount / count($questions)) * 100);
            ?>
            <div class="result-box">
                Kết quả: <?php echo $correctCount; ?> / <?php echo count($questions); ?> câu đúng (<?php echo $scorePercent; ?>%)
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php foreach ($questions as $index => $q): ?>
                <div class="question-block">
                    <div class="question-text">Câu <?php echo $index + 1; ?>: <?php echo htmlspecialchars($q['question_text']); ?></div>

                    <div class="options">
                        <?php
                        // Lấy đáp án người dùng đã chọn (để giữ trạng thái sau khi submit)
                        $userSelected = $_POST['q' . $index] ?? [];
                        if (!is_array($userSelected)) $userSelected = [$userSelected];

                        // Xác định trạng thái đúng sai của câu hỏi này để hiển thị màu
                        $isQuestionCorrect = false;
                        if ($isSubmitted) {
                            $tempUser = $userSelected;
                            sort($tempUser);
                            $tempCorrect = $q['correct_answer'];
                            sort($tempCorrect);
                            $isQuestionCorrect = ($tempUser === $tempCorrect);
                        }
                        ?>

                        <?php foreach ($q['options'] as $key => $text): ?>
                            <?php
                            $inputType = $q['type']; // radio hoặc checkbox
                            $inputName = ($inputType === 'checkbox') ? "q{$index}[]" : "q{$index}";
                            $checked = in_array($key, $userSelected) ? 'checked' : '';

                            // Xử lý CSS highlight kết quả
                            $class = '';
                            if ($isSubmitted) {
                                // Nếu đây là đáp án đúng của hệ thống -> Màu xanh
                                if (in_array($key, $q['correct_answer'])) {
                                    $class = 'option-correct-bg';
                                }
                                // Nếu người dùng chọn đáp án này mà nó sai -> Màu đỏ
                                elseif (in_array($key, $userSelected) && !in_array($key, $q['correct_answer'])) {
                                    $class = 'option-wrong-bg';
                                }
                            }
                            ?>
                            <div class="<?php echo $class; ?>">
                                <label>
                                    <input type="<?php echo $inputType; ?>"
                                        name="<?php echo $inputName; ?>"
                                        value="<?php echo $key; ?>"
                                        <?php echo $checked; ?>>
                                    <strong><?php echo $key; ?>.</strong> <?php echo htmlspecialchars($text); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($isSubmitted): ?>
                        <div class="explain">
                            <?php if ($isQuestionCorrect): ?>
                                <span class="correct">✔ Chính xác</span>
                            <?php else: ?>
                                <span class="incorrect">✘ Sai rồi.</span> Đáp án đúng là: <strong><?php echo implode(', ', $q['correct_answer']); ?></strong>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-submit"><?php echo $isSubmitted ? 'Làm lại bài thi' : 'Nộp bài'; ?></button>
        </form>
    </div>

</body>

</html>