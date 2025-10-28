<?php
// Soubor: admin.php

// Zahájení session a kontrola přístupu
session_start();

// !!! ZRANITELNOST: Role se načítá POUZE z cookie, nikoli z bezpečné session !!!
$role = isset($_COOKIE['role']) ? $_COOKIE['role'] : 'guest';

// Kontrola, zda je uživatel administrátor. Pokud ne, přesměrujeme ho pryč.
if ($role !== 'admin') {
    // Můžeme ho poslat pryč nebo zobrazit chybovou hlášku
    header("location: /welcome.php"); 
    exit;
}

// Zpracování formuláře pro nahrání příspěvku
$message = "";
$message_type = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'api/database.php';

    $title = $_POST['title'];
    $description = $_POST['description'];
    
    // Zpracování nahraného souboru
    if (isset($_FILES['image'])) {
        $target_dir = "uploads/"; // Adresář pro nahrání
        $original_filename = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $original_filename;

        // !!! ZRANITELNOST: Chybí validace typu souboru !!!
        // Kód nekontroluje, zda je nahrávaný soubor obrázek (.jpg, .png) 
        // nebo škodlivý skript (.php).

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Soubor byl úspěšně nahrán, uložíme cestu do databáze
            $sql = "INSERT INTO posts (title, description, image_path) VALUES (?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sss", $title, $description, $target_file);
                if ($stmt->execute()) {
                    $message = "Příspěvek byl úspěšně přidán.";
                    $message_type = "success";
                } else {
                    $message = "Chyba při ukládání do databáze.";
                    $message_type = "error";
                }
                $stmt->close();
            }
        } else {
            $message = "Došlo k chybě při nahrávání souboru.";
            $message_type = "error";
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Administrátorský panel</h1>
            <p>Vytvořte nový edukativní příspěvek.</p>
            <a href="/welcome.php">Zpět na hlavní stránku</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="/admin.php" method="post" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="title">Název příspěvku</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Popisek</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Obrázek (nebo PHP shell?)</label>
                <input type="file" id="image" name="image" required>
            </div>
            <button type="submit">Přidat příspěvek</button>
        </form>
    </div>
</body>
</html>
