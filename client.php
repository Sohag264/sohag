<?php
// Include database connection
include 'db.php';

// Insert new data or update existing data
if (isset($_POST['add']) || isset($_POST['update'])) {
    $id = $_POST['id']; // Get the hidden id field
    $name = $_POST['name'];
    $bp = $_POST['bp'];
    $mobile = $_POST['mobile'];

    // Check if id is empty or not. If it's empty, we add a new record, otherwise we update.
    if (empty($id)) {
        // Insert new data
        $sql = "INSERT INTO clients (name, bp, mobile) VALUES ('$name', '$bp', '$mobile')";
        if ($conn->query($sql) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Update existing data
        $sql = "UPDATE clients SET name='$name', bp='$bp', mobile='$mobile' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}

// Fetch data for editing
$edit_id = "";
$edit_name = "";
$edit_bp = "";
$edit_mobile = "";

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $sql = "SELECT * FROM clients WHERE id=$edit_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $edit_name = $row['name'];
        $edit_bp = $row['bp'];
        $edit_mobile = $row['mobile'];
    }
}

// Search functionality
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>সোহাগ কিট</title>

    <!-- Include Bootstrap and custom CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .copy-btn {
            cursor: pointer;
            margin-left: 10px;
            font-size: 0.9em;
        }
        /* Add margin to the bottom of the main content to prevent footer overlap */
        main {
            margin-bottom: 80px; /* Adjust this value as needed */
        }
    </style>
</head>
<body>

 <!-- Include Header -->
 <?php include 'header.php'; ?>

<!-- Include Navigation -->
<?php include 'nav.php'; ?>

<!-- Main Content -->
<main class="container my-4">
    <h3>ক্লায়েন্ট লিস্ট:</h3>

    <!-- Search Form -->
    <form method="post" class="form-inline mb-4">
        <input type="text" name="search" class="form-control mr-sm-2" placeholder="নাম অনুসারে খুঁজুন" value="<?php echo $search; ?>">
        <button type="submit" class="btn btn-primary">সার্চ করুন</button>
        <button type="button" class="btn btn-success ml-2" data-toggle="modal" data-target="#addModal">নতুন ক্লায়েন্ট</button>
    </form>

    <?php
    // Query to get data from the database based on search
    $sql = "SELECT id, name, bp, mobile FROM clients";
    if ($search) {
        $sql .= " WHERE name LIKE '%$search%'";
    }
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data for each row
        echo '<table class="table table-bordered">';
        echo '<thead><tr><th>নাম</th><th>বিপি</th><th>কি</th><th>এডিট</th></tr></thead>';
        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row["name"] . '</td>';
            echo '<td>' . $row["bp"] . '<span class="copy-btn" onclick="copyToClipboard(\'' . $row["bp"] . '\')">[কপি]</span></td>';
            echo '<td>' . $row["mobile"] . '<span class="copy-btn" onclick="copyToClipboard(\'' . $row["mobile"] . '\')">[কপি]</span></td>';
            echo '<td><button class="btn btn-warning btn-sm" onclick="editReport(' . $row["id"] . ', \'' . addslashes($row["name"]) . '\', \'' . addslashes($row["bp"]) . '\', \'' . addslashes($row["mobile"]) . '\')">এডিট</button></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo "কোন ফলাফল পাওয়া যায়নি।";
    }
    ?>

</main>

 <!-- Modal for Adding and Editing Clients -->
 <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">নতুন ক্লায়েন্ট যোগ করুন</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="clientId" value="">
                    <div class="form-group">
                        <label for="name">নাম:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="bp">বিপি:</label>
                        <input type="text" class="form-control" id="bp" name="bp" required>
                    </div>
                    <div class="form-group">
                        <label for="mobile">পাসকোড:</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">বাতিল করুন</button>
                    <button type="submit" class="btn btn-primary" name="add">আপডেট করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>

 <!-- Include Footer -->
 <?php include 'footer.php'; ?>

<!-- JavaScript for Copy to Clipboard and Editing -->
<script>
function copyToClipboard(text) {
    var tempInput = document.createElement("input");
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
}

function editReport(id, name, bp, mobile) {
    document.getElementById('clientId').value = id;
    document.getElementById('name').value = name;
    document.getElementById('bp').value = bp;
    document.getElementById('mobile').value = mobile;
    document.getElementById('addModalLabel').innerText = 'তথ্য এডিট করুন';
    document.querySelector("button[name='add']").setAttribute('name', 'update');
    $('#addModal').modal('show');
}
</script>

<!-- Include Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
