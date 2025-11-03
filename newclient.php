<?php
include 'db.php';

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO clients (name, phone, username, email, password) VALUES ('$name', '$phone', '$username', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "New client added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<form action="yourfile.php" method="POST">
    <input type="text" name="name" placeholder="Name" required><br>
    <input type="text" name="phone" placeholder="Phone" required><br>
    <input type="text" name="username" placeholder="Username"><br>
    <input type="email" name="email" placeholder="Email"><br>
    <input type="text" name="password" placeholder="Password"><br>
    <button type="submit" name="add">Add Client</button>
</form>
