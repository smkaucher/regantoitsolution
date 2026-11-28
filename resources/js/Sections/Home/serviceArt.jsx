/* Icon and small picture per service (matched on the slug; anything else gets the chart), as in home2.blade.php */
import Icon from '../../Components/Icon';

export const ICONS = {
    code: '<path d="M8 7 3 12l5 5M16 7l5 5-5 5M14 4l-4 16"/>',
    design: '<path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.6-.8 1.6-1.6 0-.5-.2-.8-.5-1.1-.3-.3-.5-.7-.5-1.1 0-.9.7-1.6 1.6-1.6H16a5 5 0 0 0 5-5c0-4-4-7.6-9-7.6Z"/><circle cx="7.5" cy="11" r="1.2"/><circle cx="10.5" cy="7.2" r="1.2"/><circle cx="15" cy="7.6" r="1.2"/>',
    chart: '<path d="M3 11v3a1 1 0 0 0 1 1h2l5 4V6L6 10H4a1 1 0 0 0-1 1Z"/><path d="M15 9a4 4 0 0 1 0 6M18 6a8 8 0 0 1 0 12"/>',
    shield: '<path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"/><rect x="9" y="11" width="6" height="5" rx="1"/><path d="M10 11V9.5a2 2 0 0 1 4 0V11"/>',
    social: '<circle cx="12" cy="8" r="3"/><circle cx="5" cy="10" r="2.2"/><circle cx="19" cy="10" r="2.2"/><path d="M6.5 19a5.5 5.5 0 0 1 11 0M1.8 18a3.4 3.4 0 0 1 5-2.9M22.2 18a3.4 3.4 0 0 0-5-2.9"/>',
};

export function kindOf(slug) {
    const pairs = [['develop', 'code'], ['design', 'design'], ['brand', 'design'], ['market', 'chart'], ['secur', 'shield'], ['social', 'social']];
    for (const [needle, k] of pairs) if (slug.includes(needle)) return k;
    return 'chart';
}

export const pad = (i) => String(i).padStart(2, '0');

export function Art({ kind }) {
    switch (kind) {
        case 'code': return <span className="a-win"><i></i><i></i><i></i><i></i><i></i></span>;
        case 'design': return <><span className="a-scr a"></span><span className="a-scr b"></span><span className="a-dots"><i></i><i></i><i></i></span></>;
        case 'shield': return <><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.2" dangerouslySetInnerHTML={{ __html: ICONS.shield }} /><span className="a-net"></span></>;
        case 'social': return <span className="a-soc"><Icon name="facebook" size="12" /><Icon name="instagram" size="12" /><Icon name="youtube" size="12" /><Icon name="linkedin" size="12" /><Icon name="tiktok" size="12" /></span>;
        default: return <span className="a-bars">{['30%', '45%', '40%', '62%', '78%', '96%'].map((h) => <i key={h} style={{ '--h': h }}></i>)}</span>;
    }
}
