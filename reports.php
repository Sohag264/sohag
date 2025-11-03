<?php
// Include database connection
include 'db.php';

// Initialize variables
$title = "";
$details = "";
$edit_id = "";

// Handle form submission for adding or updating reports
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add']) || isset($_POST['update'])) {
        $title = $_POST['title'];
        $details = $_POST['details'];

        if (isset($_POST['add'])) {
            // Add new report
            $sql = "INSERT INTO reports (title, details) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $title, $details);
        } else {
            // Update existing report
            $edit_id = $_POST['id'];
            $sql = "UPDATE reports SET title=?, details=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $title, $details, $edit_id);
        }

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Fetch data for editing
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $sql = "SELECT * FROM reports WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $details = $row['details'];
    }
}

// Search functionality
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Prepare the search query
$sql = "SELECT id, title, details FROM reports";
if ($search) {
    $sql .= " WHERE title LIKE '%$search%'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>আবেদন/প্রতিবেদন</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .details { display: none; margin-top: 10px; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'nav.php'; ?>

    <main class="container my-4">
        <h2>আবেদন/প্রতিবেদন:</h2>

        <form method="post" class="form-inline mb-4">
            <input type="text" name="search" class="form-control mr-sm-2" placeholder="শিরুনাম অনুসারে খুঁজুন" value="<?php echo $search; ?>">
            <button type="submit" class="btn btn-primary">সার্চ করুন</button>
            <button type="button" class="btn btn-success ml-2" data-toggle="modal" data-target="#addModal">নতুন আবেদন প্রতিবেদন যোগ করুন</button>
        </form>

        <?php
        if ($result && $result->num_rows > 0) {
            echo '<ul class="list-group">';
            while ($row = $result->fetch_assoc()) {
                echo '<li class="list-group-item">';
                echo '<strong>' . $row["title"] . '</strong>';
                echo '<button class="btn btn-link" onclick="toggleDetails(' . $row["id"] . ')">বিস্তারিত দেখুন</button>';
                echo '<div class="details" id="details-' . $row["id"] . '">';
                echo '<p>' . $row["details"] . '</p>';
                echo '<button class="copy-btn" onclick="copyToClipboard(`' . addslashes(strip_tags($row["details"])) . '`)">[কপি]</button>'; // Copy without HTML tags
                echo '<button class="btn btn-warning btn-sm float-right" onclick="editReport(' . $row["id"] . ', \'' . addslashes($row["title"]) . '\', \'' . addslashes($row["details"]) . '\')">এডিট</button>';
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo "কোন ফলাফল পাওয়া যায়নি।";
        }
        ?>

        <!-- Modal for Adding and Editing Reports -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">আবেদন প্রতিবেদন যোগ করুন</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" onsubmit="return setDetails()">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="reportId" value="<?php echo $edit_id; ?>">
                            <div class="form-group">
                                <label for="title">শিরুনাম:</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="details">বিস্তারিত:</label>
                                <div id="editor"></div>
                                <input type="hidden" name="details" id="details" value="<?php echo $details; ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">বাতিল করুন</button>
                            <button type="submit" class="btn btn-primary" name="<?php echo $edit_id ? 'update' : 'add'; ?>">
                                <?php echo $edit_id ? 'আপডেট করুন' : 'জমা দিন'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.min.js"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow'
        });

        function toggleDetails(id) {
            var detailsElement = document.getElementById('details-' + id);
            detailsElement.style.display = detailsElement.style.display === 'none' || detailsElement.style.display === '' ? 'block' : 'none';
        }

        function copyToClipboard(text) {
            var tempInput = document.createElement("input");
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
        }

        function editReport(id, title, details) {
            document.getElementById('reportId').value = id;
            document.getElementById('title').value = title;
            quill.root.innerHTML = details; // Set the editor content to details
            document.getElementById('addModalLabel').innerText = 'আবেদন প্রতিবেদন এডিট করুন';
            $('#addModal').modal('show');
        }

        function setDetails() {
            var details = quill.root.innerHTML;
            document.getElementById('details').value = details;
            return true; // Allow form submission
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
