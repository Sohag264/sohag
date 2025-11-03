<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>সকল মামলার সার্চ তালিকা</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
     <!-- Include Header -->
     <?php include 'header.php'; ?>

<!-- Include Navigation -->
<?php include 'nav.php'; ?>

    <!-- Main Content -->
    <div class="container mt-4">
        <h2 class="mb-4">সার্চ করুন এখানে</h2>
        <div class="form-group">
            <input type="text" class="form-control" id="searchInput" placeholder="Search for items...">
        </div>
        <ul class="list-group" id="itemList">
            <li class="list-group-item"><a href="drugscase.php" class="text-dark">মাদক সংক্রান্ত মামলা</a></li>
            <li class="list-group-item"><a href="robberycase.php" class="text-dark">দস্যুতা সংক্রান্ত মামলা</a></li>
            <li class="list-group-item"><a href="fightingcase.php" class="text-dark">মারামারি সংক্রান্ত মামলা</a></li>
            <li class="list-group-item"><a href="narishishucase.php" class="text-dark">নারী ও শিশু নির্যাতন সংক্রান্ত মামলা</a></li>
            <li class="list-group-item"><a href="crcase.php" class="text-dark">সিআর মামলা</a></li>
            <li class="list-group-item"><a href="udcase.php" class="text-dark">অপমৃত্যু মামলা</a></li>
            <li class="list-group-item"><a href="thiftcase.php" class="text-dark">চুরি মামলা</a></li>
        </ul>
    </div>

     <!-- Include Footer -->
     <?php include 'footer.php'; ?>

<!-- Include JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="script.js"></script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let items = document.getElementById('itemList').getElementsByTagName('li');
            
            Array.from(items).forEach(function(item) {
                let text = item.textContent || item.innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
