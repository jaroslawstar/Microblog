<?php
// add_post.php - Dodawanie wpisów przez zalogowanego użytkownika

// Rozpoczęcie sesji
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Połączenie z bazą danych
$host = 'localhost';
$dbname = 'microblog';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to the database: " . $e->getMessage());
}

// Obsługa formularza dodawania wpisu
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';

    // Walidacja wpisu
    if (empty($content)) {
        $error = 'Required field!';
    } elseif (strlen($content) > 280) { // Limit znaków (np. 280 znaków)
        $error = 'No more than 280 symbols!';
    } else {
        // Dodanie wpisu do bazy danych
        $stmt = $pdo->prepare("INSERT INTO posts (author, content) VALUES (?, ?)");
        $stmt->execute([
            $_SESSION['user']['nick'], // Autor wpisu
            $content
        ]);

        // Przekierowanie na stronę główną
        header('Location: Home.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en" style=" height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add post - µblog</title>
</head>
<body style="margin: 0 0 0 0; background-color: rgba(0,0,0,5%); font-family: Arial, Helvetica, serif; height: 100%;">

    <header style="align-self: center; background-color: deepskyblue; margin: 0 0 0 0; position: sticky; top: 0;">
        <a href="Home.php" style="text-decoration: none;" ><h1 style="align-content: center; color: white; margin: 0 0 0 0; text-align: center;">µBLOG</h1></a>
    </header>


    <div style="text-align: center; padding-top: 5%; margin-left: 10%; margin-right: 10%; padding-right: 5%; height: 100%; padding-left: 5%; background-color: rgba(255,255,255,100%);">

        <h1 >What's on your mind today <?= htmlspecialchars($_SESSION['user']['nick']) ?>?</h1>

        <?php if (!empty($error)): ?>
            <p style="color: red;"> <?= htmlspecialchars($error) ?> </p>
        <?php endif; ?>

        <a href="Home.php" style="color: deepskyblue; text-decoration: none;"> <p>< Back</p></a>

        <form method="POST" action="add_post.php">
            <label for="content"></label><br>
            <textarea id="content" name="content" rows="5" cols="40" maxlength="280" required autofocus style="resize: none; width: 35%;"></textarea>

            <button type="submit" style="height: 100%; width: 5%; background-color: deepskyblue; border-width: 0; font-size: 15px; color: white; text-align: center; align-content: center; border-radius: 10px;">send</button>
        </form>

    </div>
    
</body>
</html>
