<?php
require_once __DIR__ . '/inc/header.php';
require_once __DIR__ . '/inc/db.php';

$err = null;
$msg = $_GET['msg'] ?? '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if($user && password_verify($password, $user['password'])){
    $_SESSION['user'] = ['id'=>$user['id'], 'name'=>$user['name'], 'email'=>$user['email']];
    header('Location: /dashboard.php');
    exit;
  } else {
    $err = 'Invalid credentials.';
  }
}
?>
<div class="card" style="max-width:520px;margin:32px auto">
  <h2 style="margin-top:0">Welcome back</h2>
  <?php if($err): ?><div class="badge danger" style="display:inline-block;margin-bottom:12px"><?php echo htmlspecialchars($err) ?></div><?php endif; ?>
  <?php if($msg === 'signup_success'): ?><div class="badge ok" style="display:inline-block;margin-bottom:12px">Account created successfully! Please log in.</div><?php endif; ?>
  <form method="post">
    <div>
      <div class="label">Email</div>
      <input class="input" type="email" name="email" placeholder="you@example.com" required/>
    </div>
    <div style="margin-top:10px">
      <div class="label">Password</div>
      <input class="input" type="password" name="password" placeholder="Your password" required/>
    </div>
    <div class="form-actions">
      <a class="btn ghost" href="/index.php">Back</a>
      <button class="btn primary" type="submit">Login</button>
    </div>
    <div style="margin-top: 16px; text-align: center;">
      <p>Don't have an account? <a href="/signup.php" style="color: #5b8def; text-decoration: none;">Sign up here</a></p>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
