<?php
// Soubor: welcome.php (Finální verze)

// Zahájení session a kontrola přihlášení
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.html");
    exit;
}

// Načtení uživatelského jména ze session
$username = htmlspecialchars($_SESSION["username"]);

// !!! CTF ZRANITELNOST: Role se načítá z nezabezpečené cookie !!!
$role = isset($_COOKIE['role']) ? $_COOKIE['role'] : 'guest';

// Načtení příspěvků z databáze
require_once 'api/database.php';
$posts = [];
$sql = "SELECT title, description, image_path FROM posts ORDER BY id DESC";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    $result->free();
}
$conn->close();

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vítejte!</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php
    // Zobrazení odkazu na admin panel POUZE pro administrátory
    if ($role === 'admin') {
        echo '<div class="admin-link-container">\n';
        // Používáme jednoduchou Unicode ikonu (ozubené kolo)
        echo '<a href="/admin.php" class="admin-link"><span class="icon">&#9881;</span> Admin Panel</a>\n';
        echo '</div>\n';
    }
    ?>

    <div class="welcome-container">
        <div class="welcome-header">
            <h1>Vítejte, <?php echo $username; ?>!</h1>
            <p>Jste přihlášen jako: <strong><?php echo htmlspecialchars($role); ?></strong> | <a href="/api/logout.php">Odhlásit se</a></p>
        </div>

        <div class="posts-container">
            <h2>Nejnovější příspěvky</h2>
            <?php if (empty($posts)): ?>
                <p style="text-align: center; color: #888;">Zatím zde nejsou žádné příspěvky. Administrátor brzy nějaké přidá!</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <div class="post-content">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p><?php echo htmlspecialchars($post['description']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
