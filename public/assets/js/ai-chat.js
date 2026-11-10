/* Reganto AI chat: messenger-style modal. Opens from any [data-ai-open] element. */
(function () {
  var root = document.getElementById('aic');
  if (!root) return;
  var log = document.getElementById('aicLog');
  var form = document.getElementById('aicForm');
  var input = document.getElementById('aicInput');
  var chips = document.getElementById('aicChips');
  var KEY = 'rg_ai_chat_v1';
  var WELCOME = "Hi! I'm Reganto AI. Ask me about our services, projects, team, pricing, location or how to book.";
  var msgs = [];
  var busy = false;

  try { msgs = JSON.parse(sessionStorage.getItem(KEY) || '[]'); } catch (e) { msgs = []; }

  function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

  /* Escape first, then add bold, links and line breaks. */
  function fmt(text) {
    var h = esc(text);
    h = h.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    h = h.replace(/(https?:\/\/[^\s<]+[^\s<.,;:!?)])/g, '<a href="$1" target="_blank" rel="noreferrer">$1</a>');
    h = h.replace(/(^|[\s(])(\/(?:projects|services|about|team|blog|contact)(?:\/[a-z0-9-]+)?)(?![\w\/-])/g, '$1<a href="$2">$2</a>');
    return h.replace(/\n/g, '<br>');
  }

  function time() { return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); }

  function bubble(role, text, t) {
    var row = document.createElement('div');
    row.className = 'aic-row ' + (role === 'user' ? 'me' : 'bot');
    row.innerHTML = '<div class="aic-msg">' + fmt(text) + '<time>' + esc(t || time()) + '</time></div>';
    log.appendChild(row);
    log.scrollTop = log.scrollHeight;
    return row;
  }

  function save() { try { sessionStorage.setItem(KEY, JSON.stringify(msgs.slice(-30))); } catch (e) {} }

  function paint() {
    log.innerHTML = '';
    bubble('assistant', WELCOME, '');
    log.firstChild.querySelector('time').remove();
    msgs.forEach(function (m) { bubble(m.role, m.content, m.t); });
    chips.hidden = msgs.length > 0;
  }

  function typing() {
    var row = document.createElement('div');
    row.className = 'aic-row bot';
    row.innerHTML = '<div class="aic-msg aic-typing"><i></i><i></i><i></i></div>';
    log.appendChild(row);
    log.scrollTop = log.scrollHeight;
    return row;
  }

  function send(text) {
    text = (text || '').trim();
    if (!text || busy) return;
    busy = true;
    chips.hidden = true;
    var history = msgs.slice(-8).map(function (m) { return { role: m.role, content: m.content }; });
    msgs.push({ role: 'user', content: text, t: time() });
    bubble('user', text, msgs[msgs.length - 1].t);
    input.value = '';
    var dots = typing();
    var started = Date.now();

    fetch(root.dataset.url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': root.dataset.token },
      body: JSON.stringify({ message: text, history: history })
    }).then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
      .then(function (j) { return j.reply; })
      .catch(function () { return 'Sorry, I could not answer right now. Please message us on WhatsApp or use the Contact page.'; })
      .then(function (reply) {
        var wait = Math.max(0, 600 - (Date.now() - started));
        setTimeout(function () {
          dots.remove();
          msgs.push({ role: 'assistant', content: reply, t: time() });
          bubble('assistant', reply, msgs[msgs.length - 1].t);
          save();
          busy = false;
          input.focus();
        }, wait);
      });
  }

  function open() {
    paint();
    root.hidden = false;
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(function () { root.classList.add('on'); input.focus(); });
  }
  function close() {
    root.classList.remove('on');
    document.body.style.overflow = '';
    setTimeout(function () { root.hidden = true; }, 220);
  }

  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-ai-open]');
    if (t) { e.preventDefault(); open(); }
  });
  root.querySelectorAll('[data-aic-close]').forEach(function (x) { x.addEventListener('click', close); });
  root.querySelector('[data-aic-reset]').addEventListener('click', function () { msgs = []; save(); paint(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !root.hidden) close(); });
  form.addEventListener('submit', function (e) { e.preventDefault(); send(input.value); });
  chips.querySelectorAll('button').forEach(function (b) { b.addEventListener('click', function () { send(b.textContent); }); });
})();
