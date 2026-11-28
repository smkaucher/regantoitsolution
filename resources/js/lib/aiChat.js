/* Reganto AI chat (public/assets/js/ai-chat.js), for the React layout. The modal markup is rendered by
   Components/AiChat.jsx; the message log is filled here (React never renders inside #aicLog). Opens from any
   [data-ai-open] element on the page. Returns a cleanup. */
import { translateTree } from './translate';

export function initAiChat(root, url) {
    const log = root.querySelector('#aicLog');
    const form = root.querySelector('#aicForm');
    const input = root.querySelector('#aicInput');
    const chips = root.querySelector('#aicChips');
    const t = (k) => (window.t ? window.t(k) : k);
    const KEY = 'rg_ai_chat_v1';
    const WELCOME = t("Hi! I'm Reganto AI. Ask me about our services, projects, team, pricing, location or how to book.");
    let msgs = [];
    let busy = false;
    const timers = [];
    try { msgs = JSON.parse(sessionStorage.getItem(KEY) || '[]'); } catch (e) { msgs = []; }

    const esc = (s) => { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; };
    const fmt = (text) => {
        let h = esc(text);
        h = h.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        h = h.replace(/(https?:\/\/[^\s<]+[^\s<.,;:!?)])/g, '<a href="$1" target="_blank" rel="noreferrer">$1</a>');
        h = h.replace(/(^|[\s(])(\/(?:projects|services|about|team|contact)(?:\/[a-z0-9-]+)?)(?![\w/-])/g, '$1<a href="$2">$2</a>');
        return h.replace(/\n/g, '<br>');
    };
    const time = () => new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const bubble = (role, text, tm, wa) => {
        const row = document.createElement('div');
        row.className = 'aic-row ' + (role === 'user' ? 'me' : 'bot');
        const cta = wa && /^https:\/\/wa\.me\//.test(wa) ? '<a class="aic-wa" href="' + esc(wa) + '" target="_blank" rel="noreferrer">' + esc(t('Send on WhatsApp to confirm')) + '</a>' : '';
        row.innerHTML = '<div class="aic-msg">' + fmt(text) + cta + '<time>' + esc(tm || time()) + '</time></div>';
        log.appendChild(row);
        log.scrollTop = log.scrollHeight;
        return row;
    };
    const save = () => { try { sessionStorage.setItem(KEY, JSON.stringify(msgs.slice(-30))); } catch (e) { /* private mode */ } };
    const paint = () => {
        log.innerHTML = '';
        bubble('assistant', WELCOME, '');
        log.firstChild.querySelector('time').remove();
        msgs.forEach((m) => bubble(m.role, m.content, m.t, m.wa));
        chips.hidden = msgs.length > 0;
    };
    const typing = () => {
        const row = document.createElement('div');
        row.className = 'aic-row bot';
        row.innerHTML = '<div class="aic-msg aic-typing"><i></i><i></i><i></i></div>';
        log.appendChild(row);
        log.scrollTop = log.scrollHeight;
        return row;
    };
    const token = () => decodeURIComponent((document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/) || [])[1] || '');
    const send = (text) => {
        text = (text || '').trim();
        if (!text || busy) return;
        busy = true;
        chips.hidden = true;
        const history = msgs.slice(-8).map((m) => ({ role: m.role, content: m.content }));
        msgs.push({ role: 'user', content: text, t: time() });
        bubble('user', text, msgs[msgs.length - 1].t);
        input.value = '';
        const dots = typing();
        const started = Date.now();
        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-XSRF-TOKEN': token() },
            body: JSON.stringify({ message: text, history }),
        }).then((r) => { if (!r.ok) throw new Error(r.status); return r.json(); })
            .then((j) => ({ reply: j.reply, wa: j.whatsapp || '' }))
            .catch(() => ({ reply: t('Sorry, I could not answer right now. Please message us on WhatsApp or use the Contact page.'), wa: '' }))
            .then((reply) => {
                const wait = Math.max(0, 600 - (Date.now() - started));
                timers.push(setTimeout(() => {
                    dots.remove();
                    msgs.push({ role: 'assistant', content: reply.reply, t: time(), wa: reply.wa });
                    bubble('assistant', reply.reply, msgs[msgs.length - 1].t, reply.wa);
                    save();
                    busy = false;
                    input.focus();
                }, wait));
            });
    };
    const open = () => {
        paint();
        translateTree(root);
        root.hidden = false;
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => { root.classList.add('on'); input.focus(); });
    };
    const close = () => {
        root.classList.remove('on');
        document.body.style.overflow = '';
        timers.push(setTimeout(() => { root.hidden = true; }, 220));
    };

    const onDocClick = (e) => { const el = e.target.closest && e.target.closest('[data-ai-open]'); if (el) { e.preventDefault(); open(); } };
    const onKey = (e) => { if (e.key === 'Escape' && !root.hidden) close(); };
    const onReset = () => { msgs = []; save(); paint(); };
    const onSubmit = (e) => { e.preventDefault(); send(input.value); };
    const onChip = (e) => { const btn = e.target.closest('button'); if (btn) send(btn.textContent); };
    const closers = root.querySelectorAll('[data-aic-close]');
    const reset = root.querySelector('[data-aic-reset]');

    document.addEventListener('click', onDocClick);
    document.addEventListener('keydown', onKey);
    closers.forEach((x) => x.addEventListener('click', close));
    reset.addEventListener('click', onReset);
    form.addEventListener('submit', onSubmit);
    chips.addEventListener('click', onChip);
    return () => {
        document.removeEventListener('click', onDocClick);
        document.removeEventListener('keydown', onKey);
        closers.forEach((x) => x.removeEventListener('click', close));
        reset.removeEventListener('click', onReset);
        form.removeEventListener('submit', onSubmit);
        chips.removeEventListener('click', onChip);
        timers.forEach(clearTimeout);
    };
}
