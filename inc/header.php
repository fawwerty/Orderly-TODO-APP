<?php
// inc/header.php - shared header + navbar
require_once __DIR__ . '/auth.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>ORDERLY — Minimal PHP Todo</title>
  <link rel="stylesheet" href="/assets/css/styles.css"/>
</head>
<body>
  <nav class="nav">
    <div class="nav-inner container">
      <a class="brand" href="/index.php">
        <img src="/assets/img/logo.svg" alt="ORDERLY"/>
        <span class="name">ORDERLY</span>
      </a>
      <div class="actions">
        <button id="theme-toggle" class="btn ghost" title="Toggle light/dark mode">🌓</button>
        <?php if(current_user()): ?>
          <a class="btn ghost" href="/dashboard.php">Dashboard</a>
          <a class="btn danger" href="/logout.php">Logout</a>
        <?php else: ?>
          <a class="btn ghost" href="/login.php">Login</a>
          <a class="btn primary" href="/signup.php">Sign up</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  <div class="container">
