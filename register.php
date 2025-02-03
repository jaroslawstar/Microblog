<?php
// register.php - Rejestracja nowego użytkownika

// Rozpoczęcie sesji
session_start();

// Połączenie z bazą danych
$host = 'localhost';
$dbname = 'microblog';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tworzenie tabeli 'users', jeśli nie istnieje
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nick VARCHAR(255) NOT NULL,
            login VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )
    ");
} catch (PDOException $e) {
    die("Error connecting to the database: " . $e->getMessage());
}

// Obsługa formularza rejestracji
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick = $_POST['nick'] ?? '';
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($nick) || empty($login) || empty($password)) {
        $error = 'Fill in all fields';
    } else {
        // Sprawdź, czy login jest unikalny
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login = ?");
        $stmt->execute([$login]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'Login isalready in use!';
        } else {
            // Haszowanie hasła
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Dodanie użytkownika do bazy danych
            $stmt = $pdo->prepare("INSERT INTO users (nick, login, password) VALUES (?, ?, ?)");
            $stmt->execute([$nick, $login, $hashedPassword]);

            // Przekierowanie na stronę logowania
            header('Location: login.php');
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en" style=" height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp - µblog</title>
</head>
<body style="margin: 0 0 0 0; background-color: rgba(0,0,0,5%); font-family: Arial, Helvetica, serif; height: 100%;">

    <header style="align-self: center; background-color: deepskyblue; margin: 0 0 0 0; position: sticky; top: 0;">
        <a href="Home.php" style="text-decoration: none;" ><h1 style="align-content: center; color: white; margin: 0 0 0 0; text-align: center;">µBLOG</h1></a>
    </header>

    <div style="text-align: center; padding-top: 5%; margin-left: 10%; margin-right: 10%; padding-right: 5%; height: 100%; padding-left: 5%; background-color: rgba(255,255,255,100%);">

    <h1>Sign up to the µblog</h1>

    

    <form method="POST" action="register.php">
        <label for="nick">Name:</label><br>
        <input type="text" id="nick" name="nick" required><br><br>

        <label for="login">E-mail:</label><br>
        <input type="text" id="login" name="login" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>


        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <button type="submit" style="padding: 5px 10px 5px 10px; height: 100%; width: auto; background-color: deepskyblue; border-width: 0; font-size: 15px; color: white; text-align: center; align-content: center; border-radius: 10px;">Sign up</button>
    </form>

    <p>Already have an account? <a href="login.php">Log In</a>.</p>
</body>
</html>
