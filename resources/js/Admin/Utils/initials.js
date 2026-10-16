// "Lovleena Sharma" → "LS"
export const initials = (name = '') =>
    name
        .trim()
        .split(/\s+/)
        .map((part) => part[0] ?? '')
        .join('')
        .slice(0, 2)
        .toUpperCase() || 'A';
