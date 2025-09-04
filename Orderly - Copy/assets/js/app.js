
// Minimal helper
const $ = (sel, ctx=document) => ctx.querySelector(sel);
const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

function toast(msg){
  const t = $('.toast');
  if(!t) return alert(msg);
  t.textContent = msg;
  t.style.display='block';
  setTimeout(()=> t.style.display='none', 2400);
}

// Toggle modals
$$('[data-modal-open]').forEach(btn=>{
  btn.addEventListener('click', ()=> {
    const id = btn.getAttribute('data-modal-open');
    $('#'+id)?.classList.add('open');
  });
});
$$('[data-modal-close]').forEach(btn=>{
  btn.addEventListener('click', ()=> btn.closest('.modal')?.classList.remove('open'));
});

// Todo interactions (AJAX)
async function api(path, data=null, method='POST'){
  const opt = { method, headers: { 'Content-Type':'application/json' }, credentials:'same-origin' };
  if(data) opt.body = JSON.stringify(data);
  const res = await fetch(path, opt);
  if(!res.ok) {
    const t = await res.text();
    throw new Error(t || ('HTTP '+res.status));
  }
  return res.json();
}

async function addTodo(e){
  e.preventDefault();
  const form = e.currentTarget;
  const payload = Object.fromEntries(new FormData(form).entries());
  const out = await api('todos_api.php?action=create', payload);
  toast('Todo added');
  form.reset();
  renderTodos(out.todos);
}

async function updateTodo(id, patch){
  const out = await api('todos_api.php?action=update', {id, ...patch});
  toast('Todo updated');
  renderTodos(out.todos);
}

async function deleteTodo(id){
  if(!confirm('Delete this todo?')) return;
  const out = await api('todos_api.php?action=delete', {id});
  toast('Todo deleted');
  renderTodos(out.todos);
}

function todoRow(t){
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input type="checkbox" ${t.completed ? 'checked':''} data-toggle-complete="${t.id}" title="Toggle complete"/></td>
    <td>${t.title}</td>
    <td>${t.due_date ?? ''}</td>
    <td><span class="badge ${t.priority==='High'?'danger':(t.priority==='Medium'?'warn':'ok')}">${t.priority}</span></td>
    <td>${t.description ?? ''}</td>
    <td>
      <button class="btn" data-edit="${t.id}">Edit</button>
      <button class="btn danger" data-del="${t.id}">Delete</button>
    </td>`;
  return tr;
}

function renderTodos(list){
  const tbody = $('#todos-body');
  if(!tbody) return;
  tbody.innerHTML = '';
  if(!list || !list.length){
    const tr = document.createElement('tr');
    const td = document.createElement('td');
    td.colSpan = 6;
    td.innerHTML = '<div class="empty">No todos yet. Add one above!</div>';
    tr.appendChild(td);
    tbody.appendChild(tr);
    return;
  }
  list.forEach(t=> tbody.appendChild(todoRow(t)));

  // Wire actions
  $$('[data-del]').forEach(btn=> btn.addEventListener('click', ()=> deleteTodo(btn.dataset.del)));
  $$('[data-toggle-complete]').forEach(cb=> cb.addEventListener('change', ()=> updateTodo(cb.dataset.toggleComplete, {completed: cb.checked?1:0})));
  $$('[data-edit]').forEach(btn=> btn.addEventListener('click', ()=> openEdit(btn.dataset.edit)));
}

function openEdit(id){
  const row = [...$('#todos-body').children].find(tr=> tr.querySelector(`[data-edit="${id}"]`));
  if(!row) return;
  const title = row.children[1].textContent;
  const due_date = row.children[2].textContent;
  const priority = row.children[3].innerText.trim();
  const description = row.children[4].textContent;
  $('#edit-id').value = id;
  $('#edit-title').value = title;
  $('#edit-due').value = due_date;
  $('#edit-priority').value = priority;
  $('#edit-description').value = description;
  $('#modal-edit').classList.add('open');
}

async function submitEdit(e){
  e.preventDefault();
  const payload = Object.fromEntries(new FormData(e.currentTarget).entries());
  const id = payload.id; delete payload.id;
  const out = await api('todos_api.php?action=update', {id, ...payload});
  toast('Todo updated');
  $('#modal-edit').classList.remove('open');
  renderTodos(out.todos);
}

// Initialize dashboard if present
async function initDashboard(){
  const table = $('#todos-body');
  if(!table) return;
  try{
    const out = await api('todos_api.php?action=list', null, 'GET');
    renderTodos(out.todos);
  }catch(e){ console.error(e); }
}

// Theme toggle
function setTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('theme', theme);
}

function toggleTheme() {
  const currentTheme = localStorage.getItem('theme') || 'dark';
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  setTheme(newTheme);
}

document.addEventListener('DOMContentLoaded', ()=>{
  $('#add-todo-form')?.addEventListener('submit', addTodo);
  $('#edit-form')?.addEventListener('submit', submitEdit);
  initDashboard();

  // Initialize theme
  const savedTheme = localStorage.getItem('theme') || 'dark';
  setTheme(savedTheme);

  // Add event listener to theme toggle button
  const themeToggleBtn = document.getElementById('theme-toggle');
  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', toggleTheme);
  }
});
