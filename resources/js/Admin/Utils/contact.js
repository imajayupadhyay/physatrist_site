// Contact links for a lead's phone number (Indian numbers without a country code get +91).
const digits = (phone = '') => phone.replace(/\D/g, '');

const withCountryCode = (phone) => {
    const d = digits(phone);
    if (d.length === 10) return `91${d}`;
    if (d.length === 11 && d.startsWith('0')) return `91${d.slice(1)}`;
    return d;
};

export const telUrl = (phone) => `tel:+${withCountryCode(phone)}`;
export const whatsappUrl = (phone, text = '') =>
    `https://wa.me/${withCountryCode(phone)}` + (text ? `?text=${encodeURIComponent(text)}` : '');
