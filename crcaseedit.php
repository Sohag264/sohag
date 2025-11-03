<?php
// ডাটাবেজ সংযোগ
$conn = new mysqli('localhost', 'root', '', 'sohag_kit');
if ($conn->connect_error) {
    die('Database Connection Failed: ' . $conn->connect_error);
}

// ডাটা লোড করা
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM crcase WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $summary = $result->fetch_assoc();
    $stmt->close();

    if (!$summary) {
        die('No record found with ID: ' . $id);
    }
} else {
    die('Invalid ID');
}

// ডাটা আপডেট করা
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $summaryContent = $conn->real_escape_string($_POST['summary']);

    $stmt = $conn->prepare("UPDATE crcase SET title = ?, summary = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $summaryContent, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: crcase.php"); // মূল পেজে ফিরে যান
    exit;
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Language" content="bn">
    <title>ডাটা এডিট করুন</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        .container {
            margin-top: 50px;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>ডাটা এডিট করুন</h2>
        <form method="POST">
            <div class="form-group">
                <label for="title">টাইটেল</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($summary['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="summary">সামারি</label>
                <textarea name="summary" id="summernote" required><?= htmlspecialchars($summary['summary']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">আপডেট করুন</button>
            <a href="index.php" class="btn btn-secondary">বাতিল করুন</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 300,
                tabsize: 2,
                placeholder: 'Enter your summary here...',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
</body>
</html>
