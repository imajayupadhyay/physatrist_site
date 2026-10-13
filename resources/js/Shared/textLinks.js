// "Fill in the [appointment form](#appointment)." → [{ text: 'Fill in the ' }, { text: 'appointment form', href: '#appointment' }, { text: '.' }]
// Lets admins put links in plain text fields without HTML. Only safe link types become links.
const SAFE = /^(\/(?!\/)|#|https?:\/\/|mailto:|tel:)/i;

export const textLinks = (text = '') => {
    const parts = [];
    const re = /\[([^\]]+)\]\(([^)\s]+)\)/g;
    let last = 0;
    let match;

    while ((match = re.exec(text))) {
        if (match.index > last) parts.push({ text: text.slice(last, match.index) });
        parts.push(SAFE.test(match[2]) ? { text: match[1], href: match[2] } : { text: match[1] });
        last = re.lastIndex;
    }
    if (last < text.length) parts.push({ text: text.slice(last) });

    return parts;
};
