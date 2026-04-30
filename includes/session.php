<?php
// includes/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function currentRole(): string {
    return $_SESSION['user_role'] ?? '';
}

function requireRole(string ...$roles): void {
    if (!isLoggedIn()) {
        header('Location: /ecall/auth/login.php');
        exit;
    }
    if (!in_array(currentRole(), $roles, true)) {
        header('Location: /ecall/auth/login.php?error=access');
        exit;
    }
}

function redirectByRole(): void {
    $role = currentRole();
    if ($role === 'admin')  { header('Location: /ecall/admin/dashboard.php');  exit; }
    if ($role === 'agent')  { header('Location: /ecall/agent/dashboard.php');  exit; }
    if ($role === 'client') { header('Location: /ecall/client/dashboard.php'); exit; }
    header('Location: /ecall/auth/login.php');
    exit;
}
