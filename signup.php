<?php
require_once __DIR__ . '/inc/header.php';
require_once __DIR__ . '/inc/db.php';

$err = $ok = null;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $dob = trim($_POST['dob'] ?? '');
  $bio = trim($_POST['bio'] ?? '');
  $password = $_POST['password'] ?? '';
  if(!$name || !$email || !$password){
    $err = 'Name, Email and Password are required.';
  } else {
    try{
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users(name,email,password,dob,bio) VALUES(?,?,?,?,?)");
      $stmt->execute([$name,$email,$hash,$dob,$bio]);
      $ok = 'Account created. You can now log in.';
    }catch(Throwable $e){
      $err = 'Sign up failed: '.$e->getMessage();
    }
  }
}
if($ok){
  header('Location: /login.php?msg=signup_success');
  exit;
}
?>
<div class="card" style="max-width:720px;margin:32px auto">
  <h2 style="margin-top:0">Create your account</h2>
  <?php if($err): ?><div class="badge danger" style="display:inline-block;margin-bottom:12px"><?php echo htmlspecialchars($err) ?></div><?php endif; ?>
  <?php if($ok): ?><div class="badge ok" style="display:inline-block;margin-bottom:12px"><?php echo htmlspecialchars($ok) ?></div><?php endif; ?>
  <form method="post">
    <div class="form-row">
      <div>
        <div class="label">Name</div>
        <input class="input" name="name" placeholder="Jane Doe" required/>
      </div>
      <div>
        <div class="label">Email</div>
        <input class="input" type="email" name="email" placeholder="you@example.com" required/>
      </div>
    </div>
    <div class="form-row">
      <div>
        <div class="label">Date of Birth</div>
        <input class="input" type="date" name="dob"/>
      </div>
      <div>
        <div class="label">Password</div>
        <input class="input" type="password" name="password" placeholder="Create a password" required/>
      </div>
    </div>
    <div>
      <div class="label">Description</div>
      <textarea class="input" name="bio" rows="4" placeholder="Tell us something about you"></textarea>
    </div>
    <div class="form-actions">
      <a class="btn ghost" href="/index.php">Cancel</a>
      <button class="btn primary" type="submit">Sign up</button>
    </div>
    <div style="margin-top: 16px; text-align: center;">
      <p>Already have an account? <a href="/login.php" style="color: #5b8def; text-decoration: none;">Login here</a></p>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
