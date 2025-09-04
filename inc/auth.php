<?php
// inc/auth.php - sessions + helpers
declare(strict_types=1);
session_start();

function current_user() {
  return $_SESSION['user'] ?? null;
}
function require_login() {
  if (!current_user()) {
    header('Location: /index.php?msg=login_required');
    exit;
  }
}
