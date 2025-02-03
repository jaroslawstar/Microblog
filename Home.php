<?php
//Prosta platforma mikroblogowa

// Rozpoczęcie sesji
session_start();

// Połączenie z bazą danych (przykład z MySQL - zmodyfikuj według swoich ustawień)
$host = 'localhost';
$dbname = 'microblog';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tworzenie bazy danych, jeśli nie istnieje
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");

    // Tworzenie tabeli 'posts', jeśli nie istnieje
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            author VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Dodanie przykładowych wpisów, jeśli tabela jest pusta
    $stmt = $pdo->query("SELECT COUNT(*) FROM posts");
    $postCount = $stmt->fetchColumn();

    if ($postCount == 0) {
        $pdo->exec("
            INSERT INTO posts (author, content) VALUES
            ('Sign Up', 'Sign up to create your account on this microblog'),
            ('Log In', 'Use Log in function to have access to you account'),
            ('Post', 'Click post to add your thought on the dashboard')
        ");
    }
} catch (PDOException $e) {
    die("Error connectiong to the DataBase: " . $e->getMessage());
}

// Funkcja pobierająca wpisy z bazy danych
function getPosts($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Pobierz wszystkie wpisy (dla niezalogowanego użytkownika)
$posts = getPosts($pdo);

?>

<!DOCTYPE html>
<html lang="en" style=" height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>µblog</title>
</head>
<body style="margin: 0 0 0 0; background-color: rgba(0,0,0,5%); font-family: Arial, Helvetica, serif; height: 100%;">

    <header style="align-self: center; background-color: deepskyblue; margin: 0 0 0 0; position: sticky; top: 0;">
        <a href="Home.php" style="text-decoration: none;" ><h1 style="align-content: center; color: white; margin: 0 0 0 0; text-align: center;">µBLOG</h1></a>
    </header>
    <div style=" margin-left: 10%; margin-right: 10%; margin-bottom: 0; padding-right: 5%; padding-left: 5%; background-color: rgba(255,255,255,100%);">
    	
   		<div id="userBlock" style="text-align: right; padding-top: 1%" >
   			
   		
    	<?php if (isset($_SESSION['user'])): ?>
        	<p style="margin-top: 0; padding-top: 0%;"><strong><?= htmlspecialchars($_SESSION['user']['nick']) ?></strong></p>
        	<a href="logout.php" style="color: red; text-decoration: none"><p>Log out</p></a>
        	<a href="add_post.php" style="color: deepskyblue; text-decoration: none;"><p>Create Post</p></a>
    	<?php else: ?>
        	<a href="login.php">Log In</a> or
        	<a href="register.php">Sign Up</a>
    	<?php endif; ?>

    	</div>



    	<h2>Recent activity</h2>
    	<ul style="list-style-type: none;">
        	<?php foreach ($posts as $post): ?>
        <?php
        $timeDiff = time() - strtotime($post['created_at']);
        if ($timeDiff < 60) {
            $timeString = "Now";
        } elseif ($timeDiff < 3600) {
            $timeString = floor($timeDiff / 60) . " minutes ago";
        } elseif ($timeDiff < 86400) {
            $timeString = floor($timeDiff / 3600) . " hours ago";
        } else {
            $timeString = floor($timeDiff / 86400) . " days ago";
        }
        ?>
        <li style="background-color: rgba(0,191,255,30%);margin-top: 1%; padding-left: 1%; padding-right: 1%; padding-top: 10px; padding-bottom: 1%; border-radius: 40px;">
            <p><strong><?= htmlspecialchars($post['author']) ?></br></strong><?= htmlspecialchars($post['content']) ?><br><small style="color: gray;">  <?= $timeString ?></small></p>
        </li>
    <?php endforeach; ?>
    </ul>

    </div>
</body>
</html>
