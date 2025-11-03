<?php
// ডাটাবেজ সংযোগ
$conn = new mysqli('localhost', 'root', '', 'sohag_kit');
if ($conn->connect_error) {
    die('Database Connection Failed: ' . $conn->connect_error);
}

// নতুন সামারি যোগ করা
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $summary = $conn->real_escape_string($_POST['summary']);

    $query = "INSERT INTO summaries (title, summary) VALUES ('$title', '$summary')";
    $conn->query($query);
    header("Location: " . $_SERVER['PHP_SELF']); // পেজ রিফ্রেশ
    exit;
}

// সামারি লোড করা
$query = "SELECT * FROM summaries ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Page</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .title-section, .details-section {
            flex: 1;
            padding: 20px;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }
        .details-section {
            border-right: none;
        }
        .list-item {
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            cursor: pointer;
            background-color: #fff;
        }
        .list-item:hover {
            background-color: #f0f0f0;
        }
        .details-content {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            overflow-y: auto;
        }
        .add-summary-btn {
            padding: 10px 15px;
            margin: 20px 0;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .add-summary-btn:hover {
            background-color: #218838;
        }
        #popupForm {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            width: 70%;
            max-height: 90%;
            overflow-y: auto;
        }
        #popupForm h3 {
            margin-bottom: 15px;
        }
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- টাইটেল লিস্ট সেকশন -->
    <div class="title-section">
        <h3>Title List</h3>
        <button class="add-summary-btn" onclick="showPopup()">Add Summary</button>
        <div id="titleList">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="list-item" onclick="showDetails('<?= addslashes($row['title']) ?>', '<?= htmlspecialchars(addslashes($row['summary'])) ?>')">
                    <?= htmlspecialchars($row['title']) ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- ডিটেইলস সেকশন -->
    <div class="details-section">
        <h3>Details</h3>
        <div id="detailsContent" class="details-content">
            <p>Select a title from the list to view details.</p>
        </div>
    </div>
</div>

<!-- পপআপ ফর্ম -->
<div id="overlay"></div>
<div id="popupForm">
    <h3>Add New Summary</h3>
    <form method="POST">
        <input type="text" name="title" placeholder="Enter Title" required style="width: 100%; margin-bottom: 10px; padding: 10px;">
        <textarea name="summary" id="summernote" required></textarea>
        <div style="margin-top: 10px;">
            <button type="button" class="add-summary-btn" onclick="closePopup()" style="background-color: #dc3545;">Cancel</button>
            <button type="submit" class="add-summary-btn">Save</button>
        </div>
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

    function showDetails(title, summary) {
        const detailsDiv = document.getElementById('detailsContent');
        detailsDiv.innerHTML = `
            <h4>${title}</h4>
            <div>${summary}</div>
        `;
    }

    function showPopup() {
        document.getElementById('popupForm').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    }

    function closePopup() {
        document.getElementById('popupForm').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }
</script>
</body>
</html>
