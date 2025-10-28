<?php
// Soubor: api/database.example.php
// Toto je vzorový soubor. Zkopírujte ho do api/database.php a doplňte skutečné údaje.

// Nastavení údajů pro připojení k databázi
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'VASE_JMENO');
define('DB_PASSWORD', 'VASE_HESLO');
define('DB_NAME', 'studentsky_portal');

// Vytvoření a ověření připojení k databázi pomocí MySQLi
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Kontrola, zda se připojení podařilo
if ($conn->connect_error) {
    die("Chyba připojení k databázi: " . $conn->connect_error);
}

// Nastavení kódování znaků na UTF-8
$conn->set_charset("utf8mb4");

?>