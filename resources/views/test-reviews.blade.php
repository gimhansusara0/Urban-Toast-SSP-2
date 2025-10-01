<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reviews Tester (Sanctum token)</title>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <style>
    body{font-family:system-ui;padding:18px;background:#f6f8fb}
    input, textarea, select, button { padding:8px; margin:6px 0; width:100%; box-sizing:border-box; }
    .card{background:#fff;border:1px solid #e2e8f0;padding:12px;margin:8px 0;border-radius:8px}
    .stars{font-weight:bold}
  </style>
</head>
<body>
  <h2>Reviews Tester (Protected API)</h2>

  <div class="card">
    <label>Product ID</label>
    <input id="product_id" value="1">
    <button onclick="fetchReviews()">Load Reviews</button>
    <div id="ratingSummary"></div>
  </div>

  <div class="card">
    <h3>Add / Update Review</h3>
    <label>Your Customer ID (optional, demo only)</label>
    <input id="customer_id" placeholder="customer id (demo)">

    <label>Rating (1-5)</label>
    <select id="rating">
      <option>5</option><option>4</option><option>3</option><option>2</option><option>1</option>
    </select>

    <label>Title</label>
    <input id="title" placeholder="Short title">

    <label>Body</label>
    <textarea id="body" rows="4" placeholder="Tell others about the coffee..."></textarea>

    <button onclick="submitReview()">Submit Review</button>
  </div>

  <h3>Reviews</h3>
  <div id="reviews"></div>

<script>
  // ==============================
  // Axios setup for Sanctum API
  // ==============================
  axios.defaults.baseURL = '/api/v1';

  // Try to load stored token (you should save token after login)
  const apiToken = localStorage.getItem('api_token'); 
  if (apiToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${apiToken}`;
  } else {
    alert('⚠️ No API token found in localStorage. Please login first and store token as "api_token".');
  }

  // helper functions
  function quote(s){ return JSON.stringify(String(s)); }
  function escapeHtml(s){ return String(s).replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }

  // ==============================
  // API functions
  // ==============================
  async function fetchReviews(){
    const pid = document.getElementById('product_id').value;
    if (!pid) { alert('Enter product id'); return; }
    try {
      const res = await axios.get('/reviews', { params: { product_id: pid, per_page: 100 }});
      const items = res.data.data || res.data;
      const container = document.getElementById('reviews');
      container.innerHTML = '';
      items.forEach(r => {
        const el = document.createElement('div');
        el.className = 'card';
        el.innerHTML = `
          <div><strong>${escapeHtml(r.title || '—')}</strong> <span class="stars">[${r.rating}★]</span></div>
          <div style="color:#555;margin:6px 0">${escapeHtml(r.body || '')}</div>
          <div style="font-size:12px;color:#666">by ${escapeHtml(r.user_name || 'User')} — ${new Date(r.created_at).toLocaleString()}</div>
          <div style="margin-top:8px">
            <button onclick="prefillUpdate(${r.id}, ${r.rating}, ${quote(r.title||'')}, ${quote(r.body||'')})">Edit</button>
            <button onclick="deleteReview(${r.id})">Delete</button>
          </div>
        `;
        container.appendChild(el);
      });

      // rating summary
      const rsum = await axios.get(`/products/${pid}/rating`);
      const s = document.getElementById('ratingSummary');
      s.innerHTML = `<strong>Avg:</strong> ${rsum.data.avg_rating} ★ — ${rsum.data.count} review(s)`;
    } catch (e) {
      alert('Error loading reviews: ' + (e.response?.data?.message || e.message));
    }
  }

  async function submitReview(){
    const payload = {
      ...(document.getElementById('customer_id').value.trim() 
          ? { customer_id: parseInt(document.getElementById('customer_id').value, 10) } : {}),
      product_id: parseInt(document.getElementById('product_id').value, 10),
      rating: parseInt(document.getElementById('rating').value, 10),
      title: document.getElementById('title').value,
      body: document.getElementById('body').value,
    };

    if (!payload.product_id) { alert('Enter product id'); return; }
    if (!payload.rating || payload.rating < 1 || payload.rating > 5) { alert('Rating 1-5'); return; }

    try {
      await axios.post('/reviews', payload);
      alert('Submitted');
      document.getElementById('title').value = '';
      document.getElementById('body').value = '';
      fetchReviews();
    } catch (e) {
      alert('Submit error: ' + JSON.stringify(e.response?.data || e.message));
    }
  }

  function prefillUpdate(id, rating, title, body) {
    const newRating = prompt('Rating 1-5', rating);
    if (newRating === null) return;
    const newTitle = prompt('Title', title);
    if (newTitle === null) return;
    const newBody = prompt('Body', body);
    if (newBody === null) return;
    updateReview(id, {
      rating: parseInt(newRating),
      title: newTitle,
      body: newBody
    });
  }

  async function updateReview(id, payload) {
    try {
      await axios.put('/reviews/' + id, payload);
      fetchReviews();
    } catch (e) {
      alert('Update error: ' + JSON.stringify(e.response?.data || e.message));
    }
  }

  async function deleteReview(id) {
    if (!confirm('Delete review #' + id + '?')) return;
    try {
      await axios.delete('/reviews/' + id);
      fetchReviews();
    } catch (e) {
      alert('Delete error: ' + (e.response?.data?.message || e.message));
    }
  }
</script>

</body>
</html>
