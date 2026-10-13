import { reactive } from 'vue';

// Clinic-wide contact details used by many components (header top bar, footer, booking, WhatsApp buttons…).
// The values below are only fallbacks: on every page load app.js fills this object from the shared
// `contact` prop, which the admin edits in Admin → Footer → Contact & social. Don't hard-code them elsewhere.
export const site = reactive({
    name: 'Mind Essence Wellness Centre',
    url: 'https://www.mindessencewellness.com',
    email: 'lovleenasharma49@gmail.com',
    phones: [
        { display: '+91 82853 54608', tel: '+918285354608' },
        { display: '+91 95608 06517', tel: '+919560806517' },
    ],
    whatsappNumber: '918285354608', // country code, no +
    hours: 'Mon – Sat · 10:00 AM – 7:00 PM',
    hoursShort: 'Mon – Sat · 10 AM – 7 PM',
    address: 'C-21 B, Shyam Nagar, Top Floor, Opposite Gurudwara, New Delhi – 110018',
    mapsUrl: 'https://maps.app.goo.gl/Urx2uJBwK7MQPVeR8',
    instagram: 'https://www.instagram.com/',
    youtube: 'https://www.youtube.com/',
    facebook: null,
    linkedin: null,
});

// Called by app.js with page.props.contact (App\Support\Contact::forWebsite()).
export const syncSite = (contact) => {
    if (contact) Object.assign(site, contact);
};

export const whatsappUrl = (text) =>
    `https://wa.me/${site.whatsappNumber}` + (text ? `?text=${encodeURIComponent(text)}` : '');
