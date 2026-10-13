<?php

/*
| Default content for admin-editable sections. Used until the admin saves
| their own version (stored in the `settings` table) and by the seeders.
*/

return [

    // Homepage → "Areas of practice" section (cards come from the services table)
    'home_services' => [
        'eyebrow' => 'Areas of practice',
        'heading' => 'Focused care, grounded in expertise',
        'lead' => "From individual, psychological and mental health counselling to specialised therapy, Loveleena's practice centres on a defined set of concerns, so every client receives care backed by genuine clinical experience.",
        'card_link_label' => 'Learn more',
        'also_label' => 'Also within our care',
        'also_items' => [
            'Emotional Wellness',
            'Anger Management',
            'Grief & Loss',
            'Self-Esteem & Confidence',
            'Work & Academic Stress',
            'Personal Growth & Development',
            'Mental Wellness Sessions',
        ],
        'notes' => [
            'All services available in-clinic & as online counselling',
            'Sessions in Hindi & English',
            "Where another specialist is better suited, we'll guide you to the right care",
        ],
    ],

    // Website header (menu items live in the menu_items table)
    'header' => [
        'logo' => null, // uploaded image path; null = default leaf mark + brand text
        'logo_alt' => 'Mind Essence Wellness Centre',
        'logo_height' => 46, // px, for an uploaded logo
        'brand_name' => 'Mind Essence',
        'brand_tagline' => 'WELLNESS CENTRE',
        'cta_visible' => true,
        'cta_label' => 'Book a Session',
        'cta_url' => '/#appointment',
        'cta_new_tab' => false,
        // Side panel inside mega menus
        'mega_promo_visible' => true,
        'mega_promo_title' => 'Not sure where to start?',
        'mega_promo_text' => 'Book a 50-minute consultation and Loveleena will help you find the right support.',
        'mega_promo_cta_label' => 'Book a consultation',
        'mega_promo_cta_url' => '/#appointment',
    ],

    // Site-wide contact details: footer, top bar, booking section, WhatsApp buttons, structured data.
    'contact' => [
        'email' => 'lovleenasharma49@gmail.com',
        'phones' => ['+91 82853 54608', '+91 95608 06517'],
        'whatsapp_number' => '918285354608', // country code + number, digits only
        'address' => 'C-21 B, Shyam Nagar, Top Floor, Opposite Gurudwara, New Delhi – 110018',
        'maps_url' => 'https://maps.app.goo.gl/Urx2uJBwK7MQPVeR8',
        'website_url' => 'https://www.mindessencewellness.com',
        'hours' => 'Mon – Sat · 10:00 AM – 7:00 PM',
        'hours_short' => 'Mon – Sat · 10 AM – 7 PM',
        'instagram' => 'https://www.instagram.com/',
        'youtube' => 'https://www.youtube.com/',
        'facebook' => null,
        'linkedin' => null,
    ],

    // Website footer
    'footer' => [
        'logo' => null, // uploaded image path (for the dark background); null = leaf mark + brand text
        'logo_alt' => 'Mind Essence Wellness Centre',
        'logo_height' => 46,
        'brand_name' => 'Mind Essence',
        'brand_tagline' => 'WELLNESS CENTRE',
        'about_text' => 'Counselling · Therapy · Psychological Assessment · Mental Wellbeing',
        'credentials_name' => 'Ms. Loveleena Sharma',
        'credentials_text' => "RCI Registered Clinical Psychologist\nM.Phil. Clinical Psychology · Reg. No. A66045",
        'show_socials' => true,
        // Link columns between the brand column and the contact column (max 3).
        // type "links" = custom links, type "services" = published services (automatic).
        'columns' => [
            [
                'heading' => 'Explore',
                'type' => 'links',
                'limit' => null,
                'links' => [
                    ['label' => 'About Loveleena', 'url' => '/#about', 'new_tab' => false],
                    ['label' => 'Services', 'url' => '/#services', 'new_tab' => false],
                    ['label' => 'Assessments', 'url' => '/#assessment', 'new_tab' => false],
                    ['label' => 'Our Space', 'url' => '/#space', 'new_tab' => false],
                    ['label' => 'Videos', 'url' => '/#videos', 'new_tab' => false],
                    ['label' => 'FAQ', 'url' => '/#faq', 'new_tab' => false],
                ],
            ],
            ['heading' => 'Services', 'type' => 'services', 'limit' => 6, 'links' => []],
        ],
        'contact_heading' => 'Contact',
        'show_website' => true,
        'show_hours' => true,
        'helpline_visible' => true,
        'helpline_text' => 'In crisis? Call **Tele-MANAS 14416** (24×7, free) or visit your nearest emergency department.',
        'copyright_text' => '© {year} Mind Essence Wellness Centre. All rights reserved.',
        'bottom_text' => 'A healthier mind, a brighter you.',
    ],

];
