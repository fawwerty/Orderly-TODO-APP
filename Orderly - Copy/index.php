<?php require_once __DIR__ . '/inc/header.php'; ?>
  <section class="hero">
    <div class="card">
      <h1>Organize your day with <span style="background:linear-gradient(90deg,#5b8def,#6ee7b7);-webkit-background-clip:text;background-clip:text;color:transparent">Orderly</span></h1>
      <p>Simple, fast and modern todo app. Create tasks, set due dates and priorities, and track progress — all in your browser.</p>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <?php if(current_user()): ?>
          <a class="btn primary" href="/dashboard.php">Go to Dashboard</a>
        <?php else: ?>
          <a class="btn primary" href="/signup.php">Get started — it's free</a>
          <a class="btn" href="/login.php">I already have an account</a>
        <?php endif; ?>
      </div>
      <div style="margin-top:16px" class="kbd">Tip: Press <b>Ctrl</b> + <b>K</b> to quick add in the dashboard.</div>
    </div>
    <div class="card">
      <h3 style="margin-top:0">What you can do</h3>
      <div class="grid cards">
        <div class="card">
          <div class="badge ok">Todos</div>
          <div>Quickly add, edit, complete, or delete tasks.</div>
        </div>
        <div class="card">
          <div class="badge warn">Members</div>
          <div>See other users and collaborate in future.</div>
        </div>
        <div class="card">
          <div class="badge danger">Payments</div>
          <div>Track mock payments (for practice & UI).</div>
        </div>
        <div class="card">
          <div class="badge">Profile</div>
          <div>Edit your info and preferences.</div>
        </div>
      </div>
    </div>
  </section>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
