<?php
require_once __DIR__ . '/inc/header.php';
require_once __DIR__ . '/inc/db.php';
require_login();
$user = current_user();

// Quick mock payments seed if none exist for this user
$check = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE user_id=?");
$check->execute([$user['id']]);
if((int)$check->fetchColumn()===0){
  $seed = $pdo->prepare("INSERT INTO payments(user_id, amount, status, reference) VALUES(?,?,?,?)");
  $seed->execute([$user['id'], 12.99, 'Paid', 'SUB-'.bin2hex(random_bytes(3))]);
  $seed->execute([$user['id'], 5.00, 'Pending', 'ADDON-'.bin2hex(random_bytes(3))]);
}
?>
<div class="card" style="margin-top:24px">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
    <div>
      <div class="badge ok">Dashboard</div>
      <h2 style="margin:6px 0 0">Welcome, <?php echo htmlspecialchars($user['name']); ?> 👋</h2>
      <div class="kbd">Today is <?php echo date('l, F j'); ?>.</div>
    </div>
    <div style="display:flex;gap:8px">
      <a class="btn" href="/index.php">Home</a>
      <a class="btn ghost" href="/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="tabs">
  <a href="#todos" class="tab active">My Todos</a>
  <a href="#members" class="tab">Members</a>
  <a href="#payments" class="tab">Payments</a>
  <a href="#profile" class="tab">Profile</a>
</div>

<div id="view-todos" class="card">
  <form id="add-todo-form">
    <div class="form-row">
      <div>
        <div class="label">Title</div>
        <input class="input" name="title" placeholder="e.g. Finish project proposal" required/>
      </div>
      <div>
        <div class="label">Due date</div>
        <input class="input" type="date" name="due_date"/>
      </div>
    </div>
    <div class="form-row" style="margin-top:10px">
      <div>
        <div class="label">Priority</div>
        <select class="input" name="priority">
          <option>Low</option>
          <option selected>Medium</option>
          <option>High</option>
        </select>
      </div>
      <div>
        <div class="label">Description</div>
        <input class="input" name="description" placeholder="Optional notes"/>
      </div>
    </div>
    <div class="form-actions">
      <button class="btn primary" type="submit">Add Todo</button>
    </div>
  </form>

  <div style="margin-top:16px;overflow:auto">
    <table class="table">
      <thead>
        <tr>
          <th>Done</th><th>Title</th><th>Due</th><th>Priority</th><th>Description</th><th>Actions</th>
        </tr>
      </thead>
      <tbody id="todos-body"></tbody>
    </table>
  </div>
</div>

<div id="view-members" class="card" style="margin-top:16px">
  <h3 style="margin-top:0">Members</h3>
  <div class="grid cards">
    <?php
      $users = $pdo->query("SELECT id,name,email,created_at FROM users ORDER BY created_at DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
      foreach($users as $u){
        echo '<div class="card"><div style=\"font-weight:700\">'.htmlspecialchars($u['name']).'</div><div class=\"kbd\">'.htmlspecialchars($u['email']).'</div><div class=\"badge\">Joined '.htmlspecialchars(substr($u['created_at'],0,10)).'</div></div>';
      }
      if(!$users){ echo '<div class="empty">No members yet.</div>'; }
    ?>
  </div>
</div>

<div id="view-payments" class="card" style="margin-top:16px">
  <h3 style="margin-top:0">Payments</h3>
  <table class="table">
    <thead><tr><th>Reference</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
    <tbody>
      <?php
        $ps = $pdo->prepare("SELECT * FROM payments WHERE user_id=? ORDER BY created_at DESC");
        $ps->execute([$user['id']]);
        $rows = $ps->fetchAll(PDO::FETCH_ASSOC);
        if($rows){
          foreach($rows as $p){
            $badge = $p['status']==='Paid' ? 'ok' : ($p['status']==='Pending'?'warn':'danger');
            echo '<tr><td>'.htmlspecialchars($p['reference']).'</td><td>$'.number_format((float)$p['amount'],2).'</td><td><span class="badge '.$badge.'">'.htmlspecialchars($p['status']).'</span></td><td>'.htmlspecialchars(substr($p['created_at'],0,10)).'</td></tr>';
          }
        } else {
          echo '<tr><td colspan="4" class="empty">No payments.</td></tr>';
        }
      ?>
    </tbody>
  </table>
</div>

<div id="view-profile" class="card" style="margin-top:16px">
  <h3 style="margin-top:0">Profile</h3>
  <?php
    $stmt = $pdo->prepare("SELECT name,email,dob,bio FROM users WHERE id=?");
    $stmt->execute([$user['id']]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
  ?>
  <div class="grid" style="grid-template-columns:1fr 2fr">
    <div>
      <div class="label">Name</div>
      <div class="badge"><?php echo htmlspecialchars($profile['name']); ?></div>
      <div class="label" style="margin-top:12px">Email</div>
      <div class="badge"><?php echo htmlspecialchars($profile['email']); ?></div>
      <div class="label" style="margin-top:12px">DOB</div>
      <div class="badge"><?php echo htmlspecialchars($profile['dob']??'-'); ?></div>
    </div>
    <div>
      <div class="label">About</div>
      <div class="card"><?php echo nl2br(htmlspecialchars($profile['bio']??'No description yet.')); ?></div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div id="modal-edit" class="modal">
  <div class="dialog card">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <h3 style="margin:0">Edit Todo</h3>
      <button class="btn" data-modal-close>Close</button>
    </div>
    <form id="edit-form" style="margin-top:10px">
      <input type="hidden" id="edit-id" name="id"/>
      <div class="form-row">
        <div>
          <div class="label">Title</div>
          <input class="input" id="edit-title" name="title" required/>
        </div>
        <div>
          <div class="label">Due date</div>
          <input class="input" type="date" id="edit-due" name="due_date"/>
        </div>
      </div>
      <div class="form-row" style="margin-top:10px">
        <div>
          <div class="label">Priority</div>
          <select class="input" id="edit-priority" name="priority">
            <option>Low</option><option>Medium</option><option>High</option>
          </select>
        </div>
        <div>
          <div class="label">Description</div>
          <input class="input" id="edit-description" name="description"/>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn primary" type="submit">Save changes</button>
      </div>
    </form>
  </div>
</div>

<script>
// Simple tab switcher
document.addEventListener('click', (e)=>{
  if(e.target.matches('.tab')){
    e.preventDefault();
    document.querySelectorAll('.tab').forEach(t=> t.classList.remove('active'));
    e.target.classList.add('active');
    const target = e.target.getAttribute('href').replace('#','');
    ['todos','members','payments','profile'].forEach(id=>{
      document.querySelector('#view-'+id).style.display = (id===target)?'block':'none';
    });
  }
});
// Default visible sections
['members','payments','profile'].forEach(id=> document.querySelector('#view-'+id).style.display='none');
</script>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
