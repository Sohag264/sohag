<?php
// Database Connection
$pdo = new PDO("mysql:host=localhost;dbname=mamla_manager", "root", "");

// Insert Client
if (isset($_POST['add_client'])) {
    $stmt = $pdo->prepare("INSERT INTO clients (name, phone, address, notes) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['address'], $_POST['notes']]);
}

// Insert Case
if (isset($_POST['add_case'])) {
    $stmt = $pdo->prepare("INSERT INTO cases (client_id, case_title, court_name, case_status, case_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['client_id'], $_POST['case_title'], $_POST['court_name'], $_POST['case_status'], $_POST['case_date']]);
}

// Insert Transaction
if (isset($_POST['add_transaction'])) {
    $stmt = $pdo->prepare("INSERT INTO transactions (client_id, amount, paid_on, notes) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['client_id'], $_POST['amount'], $_POST['paid_on'], $_POST['notes']]);
}

// Fetch data
$clients = $pdo->query("SELECT * FROM clients")->fetchAll(PDO::FETCH_ASSOC);
$cases = $pdo->query("SELECT cases.*, clients.name as client_name FROM cases JOIN clients ON cases.client_id = clients.id")->fetchAll(PDO::FETCH_ASSOC);
$transactions = $pdo->query("SELECT transactions.*, clients.name as client_name FROM transactions JOIN clients ON transactions.client_id = clients.id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>mamla.php - মামলা ম্যানেজমেন্ট</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h2 { margin-top: 50px; }
        form { margin-bottom: 30px; padding: 10px; border: 1px solid #ccc; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 40px; }
        table, th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        input, textarea, select { width: 100%; padding: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>

<h1>📁 মামলা ম্যানেজমেন্ট সিস্টেম</h1>

<!-- Add Client -->
<h2>➕ ক্লায়েন্ট যুক্ত করুন</h2>
<form method="post">
    <input type="text" name="name" placeholder="নাম" required>
    <input type="text" name="phone" placeholder="ফোন নম্বর">
    <textarea name="address" placeholder="ঠিকানা"></textarea>
    <textarea name="notes" placeholder="নোটস"></textarea>
    <button type="submit" name="add_client">Save Client</button>
</form>

<!-- Add Case -->
<h2>⚖️ মামলা যুক্ত করুন</h2>
<form method="post">
    <select name="client_id" required>
        <option value="">ক্লায়েন্ট বাছাই করুন</option>
        <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="case_title" placeholder="মামলার শিরোনাম" required>
    <input type="text" name="court_name" placeholder="আদালতের নাম">
    <input type="text" name="case_status" placeholder="অবস্থা (যেমন ড্রাফট/শুনানি)">
    <input type="date" name="case_date">
    <button type="submit" name="add_case">Save Case</button>
</form>

<!-- Add Transaction -->
<h2>💰 লেনদেন যুক্ত করুন</h2>
<form method="post">
    <select name="client_id" required>
        <option value="">ক্লায়েন্ট বাছাই করুন</option>
        <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" step="0.01" name="amount" placeholder="টাকার পরিমাণ" required>
    <input type="date" name="paid_on">
    <textarea name="notes" placeholder="মন্তব্য (যদি থাকে)"></textarea>
    <button type="submit" name="add_transaction">Save Transaction</button>
</form>

<!-- Client List -->
<h2>📋 ক্লায়েন্ট তালিকা</h2>
<table>
    <tr><th>নাম</th><th>ফোন</th><th>ঠিকানা</th><th>নোটস</th></tr>
    <?php foreach ($clients as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['phone']) ?></td>
            <td><?= htmlspecialchars($c['address']) ?></td>
            <td><?= htmlspecialchars($c['notes']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Case List -->
<h2>⚖️ মামলা তালিকা</h2>
<table>
    <tr><th>ক্লায়েন্ট</th><th>শিরোনাম</th><th>আদালত</th><th>অবস্থা</th><th>তারিখ</th></tr>
    <?php foreach ($cases as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['client_name']) ?></td>
            <td><?= htmlspecialchars($c['case_title']) ?></td>
            <td><?= htmlspecialchars($c['court_name']) ?></td>
            <td><?= htmlspecialchars($c['case_status']) ?></td>
            <td><?= htmlspecialchars($c['case_date']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Transaction List -->
<h2>💰 লেনদেন তালিকা</h2>
<table>
    <tr><th>ক্লায়েন্ট</th><th>টাকা</th><th>তারিখ</th><th>মন্তব্য</th></tr>
    <?php foreach ($transactions as $t): ?>
        <tr>
            <td><?= htmlspecialchars($t['client_name']) ?></td>
            <td><?= htmlspecialchars($t['amount']) ?></td>
            <td><?= htmlspecialchars($t['paid_on']) ?></td>
            <td><?= htmlspecialchars($t['notes']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
