<?php
// Include database connection
include 'db.php';

// প্রশ্ন ও উত্তর সংরক্ষণ বা আপডেট করা
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    // ডাটা প্রক্রিয়াকরণের জন্য নিরাপত্তা
    $question = $conn->real_escape_string($question);
    $answer = $conn->real_escape_string($answer);

    if ($id) {
        // আপডেট প্রশ্ন ও উত্তর
        $sql = "UPDATE questions SET question='$question', answer='$answer' WHERE id=$id";
    } else {
        // নতুন প্রশ্ন ও উত্তর যোগ করা
        $sql = "INSERT INTO questions (question, answer) VALUES ('$question', '$answer')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: madok.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// একটি নির্দিষ্ট প্রশ্ন ও উত্তর নিয়ে আসা
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM questions WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
        exit;
    } else {
        echo json_encode([]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>প্রশ্ন ও উত্তর পেজ - Madok</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .draggable {
            cursor: move;
        }
    </style>
     <!-- Include Header -->
     <?php include 'header.php'; ?>

<!-- Include Navigation -->
<?php include 'nav.php'; ?>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4"></h1>
        <div class="d-flex justify-content-between mb-3">
            <input type="text" class="form-control mr-2" id="search" placeholder="প্রশ্ন খুঁজুন...">
            <button class="btn btn-primary" onclick="searchQuestion()">সার্চ করুন</button>
            <button class="btn btn-success ml-2" onclick="openAddModal()">নতুন প্রশ্ন ও উত্তর যোগ করুন</button>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h3>প্রশ্নের তালিকা:</h3>
                <div id="question-list" class="draggable-list">
                    <?php
                    $sql = "SELECT * FROM questions";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo '<ul class="list-group">';
                        while($row = $result->fetch_assoc()) {
                            echo '<li class="list-group-item question-item" data-id="' . $row['id'] . '">';
                            echo '<span class="question-text">' . $row['question'] . '</span>';
                            echo '<button class="btn btn-secondary btn-sm float-right ml-2" onclick="moveToSelected(this)">নির্বাচিত করুন</button>';
                            echo '<button class="btn btn-warning btn-sm float-right" onclick="editQuestion(' . $row['id'] . ')">এডিট করুন</button>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo "<div class='alert alert-info'>কোন প্রশ্ন পাওয়া যায়নি।</div>";
                    }
                    ?>
                </div>
            </div>

            <div class="col-md-6">
                <h3>নির্বাচিত প্রশ্ন:</h3>
                <ul id="selected-questions" class="list-group mb-4 draggable-list"></ul>
                <button class="btn btn-warning" id="get-results" onclick="getResults()">গেট রেজাল্ট</button>
                <button class="btn btn-danger mt-2" id="delete-selected" onclick="deleteSelected()">নির্বাচিত প্রশ্ন মুছুন</button>
            </div>
        </div>

        <!-- উত্তর দেখানোর জন্য পপআপ -->
        <div id="result-modal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">উত্তর</h5>
                        <button type="button" class="close" onclick="closeResultModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="answer-content" class="border p-3 bg-light"></div>
                        <button class="btn btn-primary" onclick="copyAllAnswers()">কপি করুন</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- প্রশ্ন যোগ করার জন্য পপআপ মডাল -->
        <div id="add-question-modal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">নতুন প্রশ্ন ও উত্তর যোগ করুন</h5>
                        <button type="button" class="close" onclick="closeAddModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="add-question-form" method="POST" onsubmit="return handleAddFormSubmit()">
                            <div class="form-group">
                                <label for="new-question">প্রশ্ন:</label>
                                <input type="text" class="form-control" id="new-question" name="question" required>
                            </div>
                            <div class="form-group">
                                <label for="new-answer">উত্তর:</label>
                                <div id="new-editor"></div>
                                <input type="hidden" name="answer" id="new-answer">
                            </div>
                            <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- প্রশ্ন এডিট করার জন্য পপআপ মডাল -->
        <div id="edit-question-modal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">প্রশ্ন ও উত্তর এডিট করুন</h5>
                        <button type="button" class="close" onclick="closeEditModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-question-form" method="POST" onsubmit="return handleEditFormSubmit()">
                            <input type="hidden" id="edit-question-id" name="id">
                            <div class="form-group">
                                <label for="edit-question">প্রশ্ন:</label>
                                <input type="text" class="form-control" id="edit-question" name="question" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-answer">উত্তর:</label>
                                <div id="edit-editor"></div>
                                <input type="hidden" name="answer" id="edit-answer">
                            </div>
                            <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        // Quill Editors Initialization
        var newQuill = new Quill('#new-editor', {
            theme: 'snow'
        });
        
        var editQuill = new Quill('#edit-editor', {
            theme: 'snow'
        });

        // Open Add Question Modal
        function openAddModal() {
            $('#add-question-modal').modal('show');
            document.getElementById('new-question').value = '';
            newQuill.setText('');
        }

        // Close Add Question Modal
        function closeAddModal() {
            $('#add-question-modal').modal('hide');
        }

        // Open Edit Question Modal
        function openEditModal() {
            $('#edit-question-modal').modal('show');
        }

        // Close Edit Question Modal
        function closeEditModal() {
            $('#edit-question-modal').modal('hide');
        }

        // Copy All Answers
        function copyAllAnswers() {
            var answers = $('#answer-content').text();
            navigator.clipboard.writeText(answers)
                .then(() => {
                    alert('উত্তর কপি করা হয়েছে।');
                })
                .catch(err => {
                    alert('কপি করতে সমস্যা হয়েছে:', err);
                });
        }

        // Move selected question to selected list
        function moveToSelected(button) {
            var questionItem = $(button).closest('.question-item');
            var questionText = questionItem.find('.question-text').text();
            var questionId = questionItem.data('id');

            // Remove question from the list
            questionItem.remove();

            // Add question to selected questions
            $('#selected-questions').append(`
                <li class="list-group-item question-item draggable" data-id="${questionId}">
                    <span class="question-text">${questionText}</span>
                    <button class="btn btn-danger btn-sm float-right" onclick="removeFromSelected(this)">মুছুন</button>
                </li>
            `);
            makeSortable('#selected-questions'); // Make the selected questions sortable
        }

        // Remove question from selected questions
        function removeFromSelected(button) {
            var questionItem = $(button).closest('.question-item');
            var questionText = questionItem.find('.question-text').text();
            var questionId = questionItem.data('id');

            // Remove question from selected questions
            questionItem.remove();

            // Add question back to the question list
            $('#question-list').append(`
                <li class="list-group-item question-item draggable" data-id="${questionId}">
                    <span class="question-text">${questionText}</span>
                    <button class="btn btn-secondary btn-sm float-right ml-2" onclick="moveToSelected(this)">নির্বাচিত করুন</button>
                    <button class="btn btn-warning btn-sm float-right" onclick="editQuestion(${questionId})">এডিট করুন</button>
                </li>
            `);
            makeSortable('#question-list'); // Make the question list sortable
        }

        // Edit Question and Answer
        function editQuestion(id) {
            fetch('madok.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit-question-id').value = data.id;
                    document.getElementById('edit-question').value = data.question;
                    editQuill.root.innerHTML = data.answer; // Set the Quill editor content
                    openEditModal();
                });
        }

        // Delete selected questions
        function deleteSelected() {
            $('#selected-questions').empty(); // Clear the selected questions list
        }

        // Handle form submission for Add Question
        function handleAddFormSubmit() {
            document.getElementById('new-answer').value = newQuill.root.innerHTML; // Set answer from Quill
            return true; // Continue with form submission
        }

        // Handle form submission for Edit Question
        function handleEditFormSubmit() {
            document.getElementById('edit-answer').value = editQuill.root.innerHTML; // Set answer from Quill
            return true; // Continue with form submission
        }

        // Get Results and show answers in a modal
        function getResults() {
            var selectedQuestions = $('#selected-questions .question-item');
            var answerContent = '';
            var serial = 1; // Serial number

            selectedQuestions.each(function() {
                var questionId = $(this).data('id');
                var questionText = $(this).find('.question-text').text();

                // Fetch answer based on question ID
                fetch('madok.php?id=' + questionId)
                    .then(response => response.json())
                    .then(data => {
                        answerContent += `<strong>${serial}. ${questionText}</strong><br>${data.answer}<br><br>`;
                        serial++;

                        // Show answers once all have been fetched
                        if (serial > selectedQuestions.length) {
                            $('#answer-content').html(answerContent);
                            $('#result-modal').modal('show');
                        }
                    });
            });
        }

        // Close Result Modal
        function closeResultModal() {
            $('#result-modal').modal('hide');
        }

        // Make list sortable
        function makeSortable(selector) {
            $(selector).sortable({
                items: ".question-item",
                placeholder: "ui-state-highlight",
                update: function(event, ui) {
                    // Handle the order change if needed
                }
            });
        }

        // Initialize sortable for both lists
        $(document).ready(function() {
            makeSortable('#selected-questions');
            makeSortable('#question-list');
        });
    </script>
     <!-- Include Footer -->
     <?php include 'footer.php'; ?>
</body>
</html>
