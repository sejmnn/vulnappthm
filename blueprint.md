# Blueprint: Studentský Portál (CTF verze)

## Přehled

Cílem tohoto projektu je vytvořit jednoduchý a bezpečný studentský portál. Aplikace umožní uživatelům registraci, přihlášení a přístup k personalizovanému obsahu. Důraz je kladen na bezpečnost (hashování hesel) a moderní webové standardy.

**Upozornění:** Tato verze obsahuje záměrné zranitelnosti pro účely soutěže Capture The Flag (CTF).

## Implementované Funkce

### Verze 1.2

*   **Uživatelská Autentizace:**
    *   Standardní registrace, přihlášení a odhlášení.
    *   Při registraci je automaticky přiřazena role `student`.

*   **Systém Příspěvků:**
    *   Administrátoři mohou vytvářet edukativní příspěvky (název, popisek, obrázek).
    *   Příspěvky se ukládají do databáze (tabulka `posts`).
    *   Všechny příspěvky se zobrazují na hlavní stránce `welcome.php`.

*   **CTF Challenge #1: Eskalace Oprávnění (Privilege Escalation)**
    *   **Zranitelnost:** Role uživatele je po přihlášení uložena do **nezabezpečené cookie** s názvem `role`.
    *   **Vektor Útoku:** Uživatel může v prohlížeči manuálně změnit hodnotu cookie z `student` na `admin`.
    *   **Dopad:** Po změně cookie získá uživatel přístup k odkazu na administrátorský panel `/admin.php`.

*   **CTF Challenge #2: Nahrání Škodlivého Souboru a RCE (Remote Code Execution)**
    *   **Předpoklad:** Útočník úspěšně dokončil Challenge #1 a má `role=admin` v cookie.
    *   **Zranitelnost:** Administrátorský panel `/admin.php` obsahuje formulář pro nahrávání příspěvků. Skript na serveru **nekontroluje typ nahrávaného souboru**.
    *   **Vektor Útoku:** Útočník může místo obrázku (`.jpg`, `.png`) nahrát PHP soubor se škodlivým kódem (např. jednoduchý webshell jako `<?php system($_GET['cmd']); ?>`).
    *   **Dopad:** Soubor se nahraje do adresáře `/uploads/`. Útočník může následně přistoupit na URL `.../uploads/shell.php?cmd=ls` a spouštět libovolné příkazy na serveru, čímž získá plnou kontrolu (RCE).

*   **Struktura Projektu:**
    *   Adresář `uploads/` pro ukládání nahraných souborů.
    *   Administrátorský panel `admin.php`.
    *   Oddělení logiky (PHP) od prezentační vrstvy (HTML).

## Plán pro Aktuální Změnu

*   **Cíl:** Vytvořit řetězec zranitelností (Privilege Escalation -> RCE) pro CTF.
*   **Kroky:**
    *   **Databáze:** Vytvořit tabulku `posts` pro ukládání příspěvků.
    *   **Adresář:** Vytvořit adresář `uploads/`.
    *   **Admin Panel:** Vytvořit `admin.php` s formulářem pro nahrávání souborů a se záměrnou zranitelností (chybějící validace typu souboru).
    *   **Hlavní Stránka:** Upravit `welcome.php` tak, aby zobrazovala přidané příspěvky a aby obsahovala odkaz na admin panel (viditelný pouze pro `role=admin`).
    *   **Dokumentace:** Aktualizovat `blueprint.md` a popsat obě na sebe navazující zranitelnosti.
