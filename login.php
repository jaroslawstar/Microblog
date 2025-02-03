<?php
// login.php - Logowanie użytkownika

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
} catch (PDOException $e) {
    die("Error connecting to the database: " . $e->getMessage());
}

// Obsługa formularza logowania
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error = 'Fill in all fields';
    } else {
        // Sprawdź, czy login istnieje w bazie danych
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Logowanie powiodło się
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nick' => $user['nick'],
                'login' => $user['login']
            ];

            // Przekierowanie na stronę główną
            header('Location: Home.php');
            exit;
        } else {
            $error = 'Wrong email or password. Try again';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en" style=" height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogIn - µblog</title>
</head>
<body style="margin: 0 0 0 0; background-color: rgba(0,0,0,5%); font-family: Arial, Helvetica, serif; height: 100%;">

    <header style="align-self: center; background-color: deepskyblue; margin: 0 0 0 0; position: sticky; top: 0;">
        <a href="Home.php" style="text-decoration: none;" ><h1 style="align-content: center; color: white; margin: 0 0 0 0; text-align: center;">µBLOG</h1></a>
    </header>

    <div style="text-align: center; padding-top: 5%; margin-left: 10%; margin-right: 10%; padding-right: 5%; height: 100%; padding-left: 5%; background-color: rgba(255,255,255,100%);">
        
    <h1>Log In to your account</h1>

    

<div style="width: auto; height: auto; align-content: center; text-align: center;">

    <form method="POST" action="login.php">
        <label for="login">Login:</label><br>
        <input type="text" id="login" name="login" required><br><br>

        <label for="password">Pass:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <?php if (!empty($error)): ?>
            <p style="color: red;"> <?= htmlspecialchars($error) ?> </p>
        <?php endif; ?>

        <button type="submit" style="padding: 5px 10px 5px 10px; height: 100%; width: auto; background-color: deepskyblue; border-width: 0; font-size: 15px; color: white; text-align: center; align-content: center; border-radius: 10px;">Log in</button>
    </form>

</div>
    <p>No account? <a href="register.php">Sign up</a></p>
    </div>
</body>
</html>
