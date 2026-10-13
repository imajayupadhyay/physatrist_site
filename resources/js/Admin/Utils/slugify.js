// "Anxiety, Stress & Depression" → "anxiety-stress-depression" (matches Laravel's Str::slug for plain text)
export const slugify = (text = '') =>
    text
        .toString()
        .normalize('NFKD')
        .replace(/[̀-ͯ]/g, '')
        .toLowerCase()
        .replace(/&/g, ' ')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .slice(0, 100);
