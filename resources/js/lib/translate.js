/* Bangla for the React pages, the browser twin of app/Support/Translator.php.
   The Blade pages are translated on the server (Localize middleware). React pages are built in the browser from
   JSON, so a Bangla visitor gets the same word list (window.BN, keys already whitespace-normalised by the server)
   and every text, placeholder, title, alt and aria-label the page shows is swapped here, as the page renders.
   Anything not in lang/bn.json stays English, exactly like the server version. */

const BN_DIGITS = { 0: '০', 1: '১', 2: '২', 3: '৩', 4: '৪', 5: '৫', 6: '৬', 7: '৭', 8: '৮', 9: '৯' };
const digits = (n) => String(n).replace(/[0-9]/g, (d) => BN_DIGITS[d]);
const key = (s) => s.replace(/\s+/gu, ' ').trim();
const MONTHS = { Jan: 'জানুয়ারি', Feb: 'ফেব্রুয়ারি', Mar: 'মার্চ', Apr: 'এপ্রিল', May: 'মে', Jun: 'জুন',
    Jul: 'জুলাই', Aug: 'আগস্ট', Sep: 'সেপ্টেম্বর', Oct: 'অক্টোবর', Nov: 'নভেম্বর', Dec: 'ডিসেম্বর' };

export const isBangla = () => !!window.BN;

/** Bangla for one plain text, or null when there is none. */
export function lookup(text) {
    const map = window.BN;
    if (!map || typeof text !== 'string') return null;
    const k = key(text);
    if (!k) return null;
    if (Object.prototype.hasOwnProperty.call(map, k)) return map[k];
    if (k.includes('&amp;')) return lookup(k.replace(/&amp;/g, '&'));
    return pattern(k);
}

/** Texts that carry a number, a date or another translated text (same rules as Translator::pattern). */
function pattern(k) {
    let m;
    if (k.startsWith('/ ')) return '/ ' + k.slice(2).split(' / ').map(tr).join(' / ');
    if ((m = k.match(/^(\d+) services$/))) return digits(m[1]) + 'টি সার্ভিস';
    if ((m = k.match(/^(\d+) things included$/))) return digits(m[1]) + 'টি বিষয় অন্তর্ভুক্ত';
    if ((m = k.match(/^(\d+) included$/))) return digits(m[1]) + 'টি অন্তর্ভুক্ত';
    if ((m = k.match(/^Show review (\d+)$/))) return 'রিভিউ ' + digits(m[1]) + ' দেখুন';
    if ((m = k.match(/^(.+) from the Reganto IT Solution team\. Everything that is included is listed below\.$/)))
        return tr(m[1]) + ' — Reganto IT Solution টিমের পক্ষ থেকে। যা যা অন্তর্ভুক্ত, নিচে দেওয়া আছে।';
    if ((m = k.match(/^Talk to us about (.+)$/))) return tr(m[1]) + ' নিয়ে আমাদের সাথে কথা বলুন';
    if ((m = k.match(/^Recent Work in (.+)$/))) return tr(m[1]) + '-এর সাম্প্রতিক কাজ';
    if ((m = k.match(/^More in (.+)$/))) return tr(m[1]) + '-এর আরও সার্ভিস';
    if ((m = k.match(/^(.+) – Reganto IT Solution$/))) {
        const title = lookup(m[1]) ?? m[1].split(' · ').map(tr).join(' · ');
        return title + ' – Reganto IT Solution';
    }
    if ((m = k.match(/^([A-Z][a-z]{2}) (\d{1,2}), (\d{4})(?: · (\d+) min read)?$/)) && MONTHS[m[1]]) {
        const out = digits(m[2]) + ' ' + MONTHS[m[1]] + ', ' + digits(m[3]);
        return m[4] ? out + ' · ' + digits(m[4]) + ' মিনিটে পড়া' : out;
    }
    return null;
}

/** The Bangla text when the visitor reads Bangla and there is one, else the text itself. */
export function tr(text) {
    return lookup(text) ?? text;
}

const ATTRS = ['placeholder', 'aria-label', 'title', 'alt'];
const SKIP = new Set(['SCRIPT', 'STYLE', 'TEXTAREA', 'NOSCRIPT']);

function translateText(node) {
    const v = node.nodeValue;
    if (!v || !/[A-Za-z]/.test(v)) return;
    const m = v.match(/^(\s*)([\s\S]*?)(\s*)$/);
    const bn = lookup(m[2]);
    if (bn !== null && bn !== m[2]) node.nodeValue = m[1] + bn + m[3];
}

function translateElement(el) {
    for (const a of ATTRS) {
        const v = el.getAttribute(a);
        if (v && /[A-Za-z]/.test(v)) {
            const bn = lookup(v);
            if (bn !== null && bn !== v) el.setAttribute(a, bn);
        }
    }
}

/** Translate everything under a node (text and the attributes above). */
export function translateTree(root) {
    if (!isBangla() || !root) return;
    if (root.nodeType === Node.TEXT_NODE) { if (!SKIP.has(root.parentNode?.nodeName)) translateText(root); return; }
    if (root.nodeType !== Node.ELEMENT_NODE) return;
    translateElement(root); // (a textarea's placeholder too, but never its text)
    if (SKIP.has(root.nodeName)) return;
    const walker = document.createTreeWalker(root, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT, {
        acceptNode: (n) => (n.nodeType === Node.TEXT_NODE && SKIP.has(n.parentNode.nodeName) ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT),
    });
    let n;
    while ((n = walker.nextNode())) {
        if (n.nodeType === Node.TEXT_NODE) translateText(n); else translateElement(n);
    }
}

/** Keep the page translated while React (and the page scripts) change it. Runs before the browser paints. */
export function watchTranslations(root) {
    if (!isBangla()) return () => {};
    translateTree(root);
    const mo = new MutationObserver((records) => {
        for (const r of records) {
            if (r.type === 'childList') r.addedNodes.forEach(translateTree);
            else if (r.type === 'characterData') translateText(r.target);
            else if (r.type === 'attributes') translateElement(r.target);
        }
    });
    // Translating only ever swaps English for Bangla, and Bangla is never looked up again, so this cannot loop.
    mo.observe(root, { subtree: true, childList: true, characterData: true, attributes: true, attributeFilter: ATTRS });
    return () => mo.disconnect();
}

// The page scripts' window.t() falls back to the full word list for texts outside window.T.
window.BN_T = tr;
