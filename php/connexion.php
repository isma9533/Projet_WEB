<?php
session_start();

require_once __DIR__ . '/bdd.php';

function redirectWithMessage($type, $text) {
    $query = http_build_query(['status' => $type, 'message' => $text]);
    header("Location: ../html/connexion.html?$query");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithMessage('error', 'Requête invalide.');
}

$action = $_POST['action'] ?? '';

if ($action === 'register') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '' || $password === '' || $confirm_password === '') {
        redirectWithMessage('error', 'Tous les champs sont requis.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectWithMessage('error', 'Email invalide.');
    }

    if ($password !== $confirm_password) {
        redirectWithMessage('error', 'Les mots de passe ne correspondent pas.');
    }

    if (strlen($password) < 8) {
        redirectWithMessage('error', 'Le mot de passe doit faire au moins 8 caractères.');
    }

    try {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email');
        $stmt->execute([':username' => $username, ':email' => $email]);

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            redirectWithMessage('error', 'Nom ou email déjà utilisé.');
        }

        $pwHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)');
        $stmt->execute([':username' => $username, ':email' => $email, ':password_hash' => $pwHash]);

        $_SESSION['user'] = ['username' => $username, 'email' => $email];
        header('Location: ../html/menu.html');
        exit;

    } catch (PDOException $e) {
        redirectWithMessage('error', 'Erreur base de données : ' . $e->getMessage());
    }

} elseif ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        redirectWithMessage('error', 'Tous les champs sont requis.');
    }

    try {
        $stmt = $pdo->prepare('SELECT id, username, email, password_hash FROM users WHERE username = :username OR email = :username');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'email' => $user['email']];
            header('Location: ../html/menu.html');
            exit;
        }

        redirectWithMessage('error', 'Identifiants invalides.');

    } catch (PDOException $e) {
        redirectWithMessage('error', 'Erreur base de données : ' . $e->getMessage());
    }

} else {
    redirectWithMessage('error', 'Action non reconnue.');
}
