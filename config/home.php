<?php

/*
| Homepage content defaults (the original static homepage).
| The admin edits each section in Admin → Home page; saved versions live in the
| `settings` table under "home.{section}" and are merged over these defaults.
| Not managed here: the Services section (Admin → Services) and the booking form.
*/

return [

    'hero' => [
        'visible' => true,
        'interval' => 6.5, // seconds per slide
        'slides' => [
            [
                'image' => '/images/clinic-entrance.jpg',
                'alt' => 'Entrance of Mind Essence Wellness Centre',
                'eyebrow' => 'Mind Essence Wellness Centre',
                'title' => 'A healthier mind,',
                'title_em' => 'a brighter you.',
                'text' => "Compassionate, evidence-based psychological care with Clinical Psychologist Ms. Lovleena Sharma, in a calm, private space where you're heard without judgement.",
                'primary_label' => 'Book an Appointment',
                'primary_url' => '#appointment',
                'secondary_label' => 'Explore Services',
                'secondary_url' => '#services',
            ],
            [
                'image' => '/images/therapy-room-2.jpg',
                'alt' => 'Calm therapy room with sofas',
                'eyebrow' => 'Therapy & Counselling',
                'title' => 'A safe space to',
                'title_em' => 'slow down & heal.',
                'text' => 'From anxiety, low mood and OCD to relationship and family concerns, every plan is shaped around you.',
                'primary_label' => 'Start Your Journey',
                'primary_url' => '#appointment',
                'secondary_label' => 'Meet the Psychologist',
                'secondary_url' => '#about',
            ],
            [
                'image' => '/images/signage.jpg',
                'alt' => 'Mind Essence Wellness Centre signage',
                'eyebrow' => 'Psychological Assessment',
                'title' => 'Understand first,',
                'title_em' => 'then begin to heal.',
                'text' => 'Structured assessments for ADHD, autism, cognitive abilities and personality, followed by clear feedback and a written report.',
                'primary_label' => 'View Assessments',
                'primary_url' => '#assessment',
                'secondary_label' => 'Book an Assessment',
                'secondary_url' => '#appointment',
            ],
        ],
        'card' => [
            'visible' => true,
            'photo' => '/images/loveleena-sharma.jpg',
            'label' => 'Your psychologist',
            'name' => 'Ms. Lovleena Sharma',
            'details' => "RCI Registered Clinical Psychologist\nM.Phil. in Clinical Psychology",
            'badge' => '', // optional pill under the details; empty = hidden
            'link_label' => 'Meet Lovleena',
            'link_url' => '#about',
        ],
    ],

    'trust' => [
        'visible' => true,
        'items' => [
            ['value' => '6+', 'label' => 'Years of clinical practice'],
            ['value' => '1,000+', 'label' => 'Clients supported'],
            ['value' => 'M.Phil.', 'label' => 'Clinical Psychology'],
            ['value' => 'RCI', 'label' => 'Registered Clinical Psychologist'],
        ],
    ],

    'about' => [
        'visible' => true,
        'image' => '/images/loveleena-sharma.jpg',
        'image_alt' => 'Ms. Lovleena Sharma, RCI Registered Clinical Psychologist',
        'badge_title' => 'RCI Registered',
        'badge_text' => 'Rehabilitation Council of India',
        'eyebrow' => 'Meet your psychologist',
        'heading' => 'Ms. Lovleena Sharma',
        'roles' => ['RCI Registered Clinical Psychologist', 'M.Phil. Clinical Psychology'],
        'body' => "Lovleena is the clinical psychologist behind Mind Essence Wellness Centre, with over six years of experience across diverse clinical settings. She works with children, adolescents, adults, couples and families, blending warmth with structured, evidence-based therapy.\n\nHer practice focuses on anxiety, depression, OCD, ADHD, autism, personality concerns and relationships, alongside detailed psychological assessment. You'll work with her directly, from your first consultation through every session after.",
        'quote' => 'Every person needs a unique approach. My role is to understand yours.',
        'credentials' => [
            'M.Phil. in Clinical Psychology',
            'RCI Registered',
            '6+ years of clinical experience',
            'CBT, ERP, behavioural therapy & psychotherapy',
            'Sessions in Hindi & English',
            'In-clinic & online consultations',
        ],
        'primary_label' => 'Book a Consultation',
        'primary_url' => '#appointment',
        'secondary_label' => 'WhatsApp Lovleena',
        'secondary_url' => '', // blank = the clinic's WhatsApp
    ],

    'assessment' => [
        'visible' => true,
        'eyebrow' => 'Psychological assessment',
        'heading' => 'Clarity before care',
        'lead' => "A psychological assessment uses standardised tests, clinical interviews and observation to understand what's really going on, so that treatment, school support or next steps are based on evidence rather than guesswork.",
        'types' => [
            ['title' => 'ADHD Assessment', 'text' => 'Evaluates attention, hyperactivity and impulsivity using rating scales, a detailed clinical interview and input from parents or teachers where relevant.', 'for' => 'Children · Adolescents · Adults'],
            ['title' => 'Autism Spectrum Assessment', 'text' => "Looks at social communication, behaviour, sensory needs and developmental history to understand whether autism explains a child's difficulties.", 'for' => 'Children · Adolescents'],
            ['title' => 'Cognitive / IQ Assessment', 'text' => 'Measures intellectual functioning, strengths and learning needs. Helpful for school planning and understanding academic difficulties.', 'for' => 'Children · Adolescents · Adults'],
            ['title' => 'Personality Assessment', 'text' => 'Standardised questionnaires and clinical tools to understand personality patterns, coping styles and emotional functioning.', 'for' => 'Adolescents · Adults'],
            ['title' => 'Clinical Diagnostic Assessment', 'text' => 'Clarifies the nature and severity of concerns such as anxiety, depression or OCD, and gives a baseline to measure progress in therapy.', 'for' => 'Adolescents · Adults'],
            ['title' => 'Child Behavioural Assessment', 'text' => "For children showing changes in behaviour, mood, school performance or relationships, to identify what's driving them.", 'for' => 'Children · Adolescents'],
        ],
        'process_heading' => 'How the assessment works',
        'process' => [
            ['title' => 'Initial consultation', 'text' => 'A 50-minute meeting to understand the concern and history, and to decide which assessment is appropriate.'],
            ['title' => 'Testing session(s)', 'text' => 'Standardised tests and structured interviews, usually over one to three sessions depending on the assessment.'],
            ['title' => 'Scoring & interpretation', 'text' => 'Results are carefully scored and integrated with clinical observations and history.'],
            ['title' => 'Feedback & written report', 'text' => 'A feedback session explaining the findings in plain language, with a written report and clear recommendations.'],
        ],
        'who_heading' => 'Who is it for?',
        'who_items' => [
            "Parents concerned about a child's attention, learning, development or behaviour",
            'Adults seeking clarity on long-standing difficulties with focus, mood or relationships',
            'Anyone who needs a formal report for school, college or another professional',
            'Clients whose therapy would benefit from a clearer understanding of the concern',
        ],
        'note' => 'Not sure which assessment is right? Book an initial consultation and Lovleena will recommend the most suitable one.',
        'primary_label' => 'Book an Assessment',
        'whatsapp_label' => 'Ask on WhatsApp',
        'whatsapp_message' => "Hi, I'd like to know more about psychological assessments.",
    ],

    'steps' => [
        'visible' => true,
        'eyebrow' => 'Your first steps',
        'heading' => 'Beginning is simpler than you think',
        'lead' => "Reaching out takes courage. We'll make everything after that feel easy.",
        'items' => [
            ['title' => 'Reach out', 'text' => "Book online, call or WhatsApp us. Share as much or as little as you're comfortable with."],
            ['title' => 'First consultation', 'text' => 'A relaxed 50-minute conversation with Lovleena to understand your concerns, history and goals.'],
            ['title' => 'Your care plan', 'text' => 'Together you build a personalised plan and review progress as you grow.'],
        ],
    ],

    'space' => [
        'visible' => true,
        'eyebrow' => 'Our space',
        'heading' => 'Designed to help you feel at ease',
        'lead' => 'Soft light, warm wood and quiet rooms. Our centre was built so that the moment you walk in, you can breathe a little easier.',
        // Exactly 4 photos: 1 = tall (left), 2 = wide (top right), 3 + 4 = small (bottom right)
        'photos' => [
            ['image' => '/images/therapy-room-2.jpg', 'alt' => 'Therapy lounge', 'caption' => 'Therapy lounge'],
            ['image' => '/images/clinic-entrance.jpg', 'alt' => 'Clinic entrance', 'caption' => 'Welcome entrance'],
            ['image' => '/images/consult-room.jpg', 'alt' => 'Private consultation room', 'caption' => 'Private consultation'],
            ['image' => '/images/therapy-room-1.jpg', 'alt' => 'Counselling room', 'caption' => 'Counselling room'],
        ],
    ],

    'videos' => [
        'visible' => true,
        'eyebrow' => 'Watch & learn',
        'heading' => 'Small insights for everyday wellbeing',
        'lead' => 'Short, practical videos on mental health from Lovleena, shared on YouTube and Instagram.',
        'channel_name' => 'Mind Essence',
        'channel_subtitle' => 'with Lovleena Sharma',
        'channel_image' => '/images/loveleena-sharma.jpg',
        // platform "youtube": plays on the page (video = YouTube link or ID); "instagram": opens the reel (video = URL)
        'reels' => [
            ['platform' => 'youtube', 'video' => 'kSVMb10Rj6I', 'image' => '/images/therapy-room-2.jpg', 'cover' => 'Understanding', 'cover_em' => 'your mind', 'topic' => 'Mental wellbeing', 'title' => 'Understanding your mind'],
            ['platform' => 'instagram', 'video' => 'https://www.instagram.com/reel/DCReoxqtuT2/', 'image' => '/images/consult-room.jpg', 'cover' => 'Coping with', 'cover_em' => 'anxious thoughts', 'topic' => 'Anxiety', 'title' => 'Coping with anxious thoughts'],
            ['platform' => 'instagram', 'video' => 'https://www.instagram.com/reel/DDKMMyUzYGE/', 'image' => '/images/therapy-room-1.jpg', 'cover' => 'When to', 'cover_em' => 'seek therapy', 'topic' => 'Starting therapy', 'title' => 'When to seek therapy'],
        ],
    ],

    'testimonials' => [
        'visible' => true,
        'eyebrow' => 'Client stories',
        'heading' => "Words from those we've walked with",
        'interval' => 7, // seconds per testimonial
        'items' => [
            ['quote' => "I had been struggling with intrusive thoughts for years. Lovleena ma'am made me feel understood from the very first session. Today I finally feel in control of my mind again.", 'name' => 'R. M.', 'service' => 'OCD therapy', 'rating' => 5],
            ['quote' => 'The space is so calm and private. I never felt judged. The practical tools for my anxiety actually work in daily life, at work and at home.', 'name' => 'A. K.', 'service' => 'Anxiety counselling', 'rating' => 5],
            ['quote' => "Our son was struggling with focus and exams. The assessment gave us clarity and the sessions helped him grow in confidence. We're truly grateful.", 'name' => 'Parent of a teen', 'service' => 'ADHD assessment', 'rating' => 5],
            ['quote' => 'Couples counselling gave us a way to talk again without fighting. Warm, patient and incredibly insightful.', 'name' => 'S. & N.', 'service' => 'Marriage counselling', 'rating' => 5],
        ],
    ],

    'faq' => [
        'visible' => true,
        'eyebrow' => 'FAQ',
        'heading' => 'Questions you may have',
        'lead' => 'Starting therapy can bring up a lot of questions. Here are answers to the ones we hear most often.',
        // Answers support links: [link text](/#section or https://…)
        'items' => [
            ['question' => 'What happens in the first consultation?', 'answer' => "The first session is a relaxed conversation with Lovleena about what's bringing you in, your background and what you'd like to change. By the end, you'll have a clearer sense of the concern and a suggested plan, whether that's therapy, an assessment or both. There's no pressure to share more than you're comfortable with."],
            ['question' => 'How long is each session?', 'answer' => 'Consultation and therapy sessions are 50 minutes. Couples and family sessions may run slightly longer. Assessments usually take one to three sessions, depending on the type of assessment.'],
            ['question' => 'Do you offer online sessions?', 'answer' => "Yes. Online sessions take place over a secure video call and follow the same structure as in-clinic sessions. You'll just need a private, quiet space and a stable internet connection. Some assessments, particularly for young children, are best done in person."],
            ['question' => 'Is what I share kept confidential?', 'answer' => "Yes. Everything you share is kept strictly confidential, in line with professional and ethical guidelines. The only exceptions are rare situations where there is a serious risk to your safety or someone else's, or where disclosure is required by law. These limits are explained clearly in your first session."],
            ['question' => 'How many sessions will I need?', 'answer' => "It depends on the concern and your goals. Some people benefit from a few focused sessions, while others prefer longer-term support. Progress is reviewed together regularly, and you're always involved in decisions about your care."],
            ['question' => 'What does a psychological assessment involve?', 'answer' => 'An assessment begins with a consultation, followed by standardised tests and interviews, scoring and interpretation, and finally a feedback session with a written report. [See the assessments we offer.](#assessment)'],
            ['question' => 'Do you see children and adolescents?', 'answer' => 'Yes. For children, the first meeting usually includes parents so we can understand the concern fully. Parents are kept involved throughout, while older adolescents are also given private space to speak openly.'],
            ['question' => 'Do you prescribe medication?', 'answer' => 'No. Clinical psychologists provide therapy and assessment, not medication. If medication may help, Lovleena can guide you to a psychiatrist and coordinate care where appropriate.'],
            ['question' => 'How do I book an appointment?', 'answer' => "You can call, send a WhatsApp message, or fill in the [appointment form](#appointment). We'll confirm a convenient date and time, and whether you'd prefer an in-clinic or online session."],
            ['question' => 'Can I cancel or reschedule a session?', 'answer' => "Of course. We ask for at least 24 hours' notice to cancel or reschedule, so the slot can be offered to someone else. Just call or WhatsApp us."],
            ['question' => 'What are the session fees?', 'answer' => "Fees vary by session type (individual, couples, family or assessment). Please call or WhatsApp us for current fees. We're happy to explain everything before you book."],
        ],
    ],

    'seo' => [
        'title' => 'Mind Essence Wellness Centre · Ms. Lovleena Sharma, Clinical Psychologist',
        'description' => 'Mind Essence Wellness Centre with Ms. Lovleena Sharma, RCI Registered Clinical Psychologist (M.Phil. Clinical Psychology). Therapy, counselling and psychological assessment, in-clinic and online.',
        'keywords' => '',
        'og_title' => '',
        'og_description' => '',
        'og_image' => '', // blank = default site image
    ],

];
