<?php
require_once "../config/db.php";

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $kontotyp = $_POST['kontotyp'] ?? '';

    if ($username === '' || $password === '' || $email === '' || $kontotyp === '') {
        $errors[] = "Fyll i alla fält.";
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Ogiltig e-postadress.";
    }

    if ($kontotyp !== 'arbetstagare' && $kontotyp !== 'arbetsgivare') {
        $errors[] = "Kontotyp är ogiltig.";
    }

    if (!$errors) {
        // Kolla om username eller email redan finns
        $stmt = $conn->prepare("SELECT id FROM users WHERE anvandarnamn = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $errors[] = "Användarnamn eller e-post är redan upptaget.";
        }
        $stmt->close();
    }

    if (!$errors) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $namn = $username; // kan bytas till eget fält senare

        $stmt = $conn->prepare("
            INSERT INTO users (anvandarnamn, losenord, namn, email, kontotyp)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssss", $username, $hashedPassword, $namn, $email, $kontotyp);

        if ($stmt->execute()) {
            // valfritt: logga in direkt
            $_SESSION['user_id'] = $stmt->insert_id;
            header("Location: create_profile.php"); // eller login-sida
            exit;
        } else {
            $errors[] = "Ett fel uppstod, försök igen. (" . $conn->error . ")";
        }
        $stmt->close();
    }

    if ($errors) {
        $message = implode("<br>", array_map("htmlspecialchars", $errors));
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Registrera konto</title>
    <link rel="stylesheet" href="../assets/css/index.css">
</head>
<body>
<div class="container">
    <h1>Skapa konto</h1>

    <?php if ($message !== ''): ?>
        <p style="color:red;"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" action="register.php">
        <p>
            Användarnamn<br>
            <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </p>

        <p>
            E-post<br>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </p>

        <p>
            Kontotyp<br>
            <select name="kontotyp" required>
                <option value="">Välj...</option>
                <option value="arbetstagare" <?= (($_POST['kontotyp'] ?? '') === 'arbetstagare') ? 'selected' : '' ?>>Arbetstagare</option>
                <option value="arbetsgivare"  <?= (($_POST['kontotyp'] ?? '') === 'arbetsgivare') ? 'selected' : '' ?>>Arbetsgivare</option>
            </select>
        </p>

        <p>
            Lösenord<br>
            <input type="password" name="password" required>
        </p>

        <p>
            <input type="submit" value="Skapa konto">
        </p>
    </form>

    <p><a href="../frontend/login.html">Tillbaka till inloggning</a></p>
</div>
</body>
</html>
<?php
session_start();
require_once "../config/db.php";

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $kontotyp = $_POST['kontotyp'] ?? '';

    if ($username === '' || $password === '' || $email === '' || $kontotyp === '') {
        $errors[] = "Fyll i alla fält.";
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Ogiltig e-postadress.";
    }

    if ($kontotyp !== 'arbetstagare' && $kontotyp !== 'arbetsgivare') {
        $errors[] = "Kontotyp är ogiltig.";
    }

    if (!$errors) {
        // Kolla om username eller email redan finns
        $stmt = $conn->prepare("SELECT id FROM users WHERE anvandarnamn = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $errors[] = "Användarnamn eller e-post är redan upptaget.";
        }
        $stmt->close();
    }

    if (!$errors) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $namn = $username; // kan bytas till eget fält senare

        $stmt = $conn->prepare("
            INSERT INTO users (anvandarnamn, losenord, namn, email, kontotyp)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssss", $username, $hashedPassword, $namn, $email, $kontotyp);

        if ($stmt->execute()) {
            // valfritt: logga in direkt
            $_SESSION['user_id'] = $stmt->insert_id;
            header("Location: create_profile.php"); // eller login-sida
            exit;
        } else {
            $errors[] = "Ett fel uppstod, försök igen. (" . $conn->error . ")";
        }
        $stmt->close();
    }

    if ($errors) {
        $message = implode("<br>", array_map("htmlspecialchars", $errors));
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Registrera konto</title>
    <link rel="stylesheet" href="../assets/css/index.css">
</head>
<body>
<div class="container">
    <h1>Skapa konto</h1>

    <?php if ($message !== ''): ?>
        <p style="color:red;"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" action="register.php">
        <p>
            Användarnamn<br>
            <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </p>

        <p>
            E-post<br>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </p>

        <p>
            Kontotyp<br>
            <select name="kontotyp" required>
                <option value="">Välj...</option>
                <option value="arbetstagare" <?= (($_POST['kontotyp'] ?? '') === 'arbetstagare') ? 'selected' : '' ?>>Arbetstagare</option>
                <option value="arbetsgivare"  <?= (($_POST['kontotyp'] ?? '') === 'arbetsgivare') ? 'selected' : '' ?>>Arbetsgivare</option>
            </select>
        </p>

        <p>
            Lösenord<br>
            <input type="password" name="password" required>
        </p>

        <p>
            <input type="submit" value="Skapa konto">
        </p>
    </form>

    <p><a href="../frontend/login.html">Tillbaka till inloggning</a></p>
</div>
</body>
</html>
