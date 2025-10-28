<?php
// Soubor: api/login.php

// Zahájení session pro práci s proměnnými session
session_start();

// Načtení konfiguračního souboru pro připojení k databázi
require_once 'database.php';

// Zpracování pouze POST požadavků
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Příprava SQL dotazu pro výběr uživatele, nyní včetně role
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Navázání parametrů
        $stmt->bind_param("s", $username);

        // Provedení dotazu
        if ($stmt->execute()) {
            // Uložení výsledku
            $stmt->store_result();

            // Kontrola, zda byl uživatel nalezen
            if ($stmt->num_rows == 1) {
                // Navázání výsledků (id, username, hashed_password, role)
                $stmt->bind_result($id, $username, $hashed_password, $role);
                if ($stmt->fetch()) {
                    // Ověření hesla
                    if (password_verify($password, $hashed_password)) {
                        // Heslo je správné, vytvoření session
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["role"] = $role; // Uložení role do session (bezpečné)

                        // !!! CTF ZRANITELNOST: Uložení role do NEZABEZPEČENÉ cookie !!!
                        // Tato cookie může být v prohlížeči snadno změněna.
                        setcookie("role", $role, time() + (86400 * 30), "/"); // Cookie platná 30 dní

                        // Přesměrování na zabezpečenou stránku
                        header("location: /welcome.php");
                    } else {
                        // Zobrazení chybové hlášky, pokud heslo není správné
                        $error_message = urlencode("Zadané heslo není správné.");
                        header("Location: /login.html?msg=" . $error_message);
                    }
                }
            } else {
                // Zobrazení chybové hlášky, pokud uživatelské jméno neexistuje
                $error_message = urlencode("Uživatel s tímto jménem neexistuje.");
                header("Location: /login.html?msg=" . $error_message);
            }
        } else {
            echo "Chyba! Zkuste to prosím později.";
        }

        // Uzavření statement
        $stmt->close();
    }

    // Uzavření připojení
    $conn->close();
} else {
    // Pokud se někdo pokusí přistoupit na tento soubor přímo, přesměrujeme ho
    header("location: /login.html");
}
?>
