<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Community Reviews</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Axios -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <style>
    :root{
      --bg:#f6f8fb;
      --card:#ffffff;
      --border:#e2e8f0;
      --text:#0f172a;
      --muted:#64748b;
      --accent:#0ea5e9;
    }
    *{box-sizing:border-box}
    html, body {height:100%; margin:0}
    body{font-family:system-ui, -apple-system, Segoe UI, Roboto, Inter, sans-serif; background:var(--bg); color:var(--text)}
    .shell{height:100vh; padding:18px; display:flex; align-items:stretch; justify-content:center;}
    .container{
      width:min(1200px, 100%);
      background:var(--card);
      border:1px solid var(--border);
      border-radius:14px;
      display:grid;
      grid-template-columns: 1fr 1.5fr;
      overflow:hidden;
      box-shadow: 0 10px 25px rgba(0,0,0,.05);
    }
    .col{display:flex; flex-direction:column; min-height:0;}
    .left, .right{padding:18px; overflow-y:auto;}
    .left{ border-right:1px solid var(--border); }
    .header{padding:14px 18px; border-bottom:1px solid var(--border); background:#fbfdff;
      display:flex; align-items:center; justify-content:space-between; gap:10px;}
    h2{margin:0; font-size:18px}
    .muted{color:var(--muted); font-size:14px}
    .field{margin:10px 0}
    label{display:block; font-size:14px; color:#334155; margin-bottom:6px}
    input[type="text"], textarea{
      width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:10px;
      background:#fff; outline:none; transition:border .2s ease;
    }
    textarea{min-height:110px; resize:vertical}
    input:focus, textarea:focus{border-color:var(--accent)}
    .btn{display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 14px;
      border-radius:10px; cursor:pointer; border:1px solid transparent; background:var(--accent); color:#fff; font-weight:600;}
    .btn.secondary{background:#fff; color:#0f172a; border-color:var(--border)}
    .btn:disabled{opacity:.65; cursor:not-allowed}
    .stars{display:flex; gap:6px; font-size:22px; user-select:none}
    .star{cursor:pointer; transition:transform .05s ease}
    .star:hover{transform:scale(1.05)}
    .star.active{color:#f59e0b}
    .star.inactive{color:#cbd5e1}
    .toolbar{display:flex; gap:8px; align-items:center;}
    .seg{display:inline-flex; border:1px solid var(--border); border-radius:10px; overflow:hidden;}
    .seg button{background:#fff; border:0; padding:8px 12px; cursor:pointer; font-weight:600; color:#334155;}
    .seg button.active{background:#eef6ff; color:#0369a1}
    .list{margin-top:12px; display:flex; flex-direction:column; gap:10px}
    .card{background:#fff; border:1px solid var(--border); border-radius:12px; padding:12px;}
    .card .top{display:flex; align-items:center; justify-content:space-between; gap:10px;}
    .name{font-weight:700}
    .rating{font-weight:700; color:#f59e0b}
    .body{margin:8px 0; line-height:1.5}
    .meta{font-size:12px; color:var(--muted)}
    .actions{display:flex; gap:8px; margin-top:8px}
    .empty{padding:18px; text-align:center; color:var(--muted)}
    .hl{background:#fffbea; border:1px dashed #f59e0b; padding:8px; border-radius:8px}
    @media (max-width: 900px){
      .container{grid-template-columns: 1fr}
      .left{border-right:0; border-bottom:1px solid var(--border)}
    }
  </style>
</head>
<body>
  <div class="shell">
    <div class="container">
      <div class="col">
        <div class="header">
          <div>
            <h2>Share your thoughts</h2>
            <div class="muted" id="meLine">Loading your profile…</div>
          </div>
        </div>
        <div class="left">
          <form id="reviewForm" onsubmit="return false;">
            <div class="field">
              <label>Rating</label>
              <div class="stars" id="starBox">
                <span class="star inactive" data-v="1">★</span>
                <span class="star inactive" data-v="2">★</span>
                <span class="star inactive" data-v="3">★</span>
                <span class="star inactive" data-v="4">★</span>
                <span class="star inactive" data-v="5">★</span>
              </div>
              <input type="hidden" id="rating" value="5">
            </div>
            <div class="field">
              <label>Title (optional)</label>
              <input type="text" id="title" placeholder="Short title">
            </div>
            <div class="field">
              <label>Comment</label>
              <textarea id="body" placeholder="Write something helpful for others…"></textarea>
            </div>
            <div class="field">
              <button class="btn" id="submitBtn" onclick="submitReview()">Post review</button>
            </div>
          </form>
        </div>
      </div>
      <div class="col">
        <div class="header">
          <div>
            <h2>Community reviews</h2>
            <div class="muted">Newest first</div>
          </div>
          <div class="toolbar">
            <div class="seg">
              <button id="tabAll" class="active" onclick="setFilter('all')">All</button>
              <button id="tabMy" onclick="setFilter('my')">My</button>
            </div>
          </div>
        </div>
        <div class="right">
          <div id="list" class="list"></div>
        </div>
      </div>
    </div>
  </div>
<script>
  axios.defaults.baseURL = '/api/v1';
  const apiToken = localStorage.getItem('api_token');
  if (apiToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${apiToken}`;
  }

  let me = { user_id: null, customer_id: null, name: 'You' };
  let filter = 'all';

  function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
  }
  function fmtDate(s) {
    try { return new Date(s).toLocaleString(); } catch(e){ return s; }
  }

  async function loadMe() {
    try {
      const r = await axios.get('/me/customer');
      me = { user_id: r.data.user_id, customer_id: r.data.customer_id, name: r.data.name };
      document.getElementById('meLine').textContent = `Signed in as ${me.name}`;
    } catch(e) {
      document.getElementById('meLine').textContent = 'Not signed in';
    }
  }

  const starBox = document.getElementById('starBox');
  const ratingInput = document.getElementById('rating');
  function paintStars(v) {
    [...starBox.querySelectorAll('.star')].forEach(st => {
      const n = +st.dataset.v;
      st.classList.toggle('active', n <= v);
      st.classList.toggle('inactive', n > v);
    });
  }
  starBox.addEventListener('click', e => {
    if (!e.target.classList.contains('star')) return;
    ratingInput.value = +e.target.dataset.v;
    paintStars(+ratingInput.value);
  });
  paintStars(5);

  async function loadReviews(url = 'reviews') {
    try {
      const res = await axios.get(url); // axios 
      const items = res.data.data ?? res.data;

      const box = document.getElementById('list');
      box.innerHTML = '';

      if (!items.length) {
        box.innerHTML = '<div class="empty">No reviews yet.</div>';
        return;
      }

      items.forEach(r => {
        const mine = me.customer_id && r.customer_id === me.customer_id;
        const el = document.createElement('div');
        el.className = 'card';
        el.innerHTML = `
          <div class="top">
            <div class="name">${escapeHtml(r.customer_name || 'User')}</div>
            <div class="rating">${r.rating} ★</div>
          </div>
          ${r.title ? `<div class="hl"><strong>${escapeHtml(r.title)}</strong></div>` : ''}
          <div class="body">${escapeHtml(r.body || '')}</div>
          <div class="meta">Posted ${fmtDate(r.created_at)}</div>
          <div class="actions" ${mine ? '' : 'style="display:none"'} >
            <button class="btn secondary" onclick="editReview(${r.id}, ${r.rating}, ${JSON.stringify(r.title || '')}, ${JSON.stringify(r.body || '')})">Edit</button>
            <button class="btn secondary" onclick="deleteReview(${r.id})">Delete</button>
          </div>`;
        box.appendChild(el);
      });
    } catch (e) {
      alert('Error loading: ' + (e.response?.data?.message || e.message));
    }
  }

  function setFilter(v) {
    filter = v;
    document.getElementById('tabAll').classList.toggle('active', v === 'all');
    document.getElementById('tabMy').classList.toggle('active', v === 'my');

    if (v === 'all') {
      loadReviews('reviews');  // public
    } else {
      if (!me.customer_id) {
        document.getElementById('list').innerHTML = '<div class="empty">⚠️ Please sign in to see your reviews.</div>';
        return;
      }
      loadReviews('reviews/my'); // protected
    }
  }

  async function submitReview() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    const payload = {
      rating: +document.getElementById('rating').value,
      title: document.getElementById('title').value || null,
      body: document.getElementById('body').value || null
    };
    try {
      await axios.post('/reviews', payload);
      document.getElementById('title').value = '';
      document.getElementById('body').value = '';
      paintStars(5);
      loadReviews();
    } catch(e) {
      alert('Submit error: ' + JSON.stringify(e.response?.data || e.message));
    } finally {
      btn.disabled = false;
    }
  }

  function editReview(id, rating, title, body) {
    const nr = prompt('Rating (1-5)', rating); if (nr === null) return;
    const nt = prompt('Title', title); if (nt === null) return;
    const nb = prompt('Body', body); if (nb === null) return;
    axios.put('/reviews/' + id, { rating:+nr, title:nt||null, body:nb||null }).then(()=>loadReviews());
  }

  function deleteReview(id) {
    if (!confirm('Delete?')) return;
    axios.delete('/reviews/' + id).then(()=>loadReviews());
  }

  (async()=>{ await loadMe(); await loadReviews(); })();
</script>

</body>
</html>
