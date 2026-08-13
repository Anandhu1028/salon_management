<?php

return [

    'default' => 'default',

    'icons' => [

        'haircut' => [
            'label' => 'Haircut',
            'category' => 'Hair',
            'keywords' => [
                'haircut', 'hair cut', 'hair cutting', 'hair trim', 'cut and blow',
                'blowdry', 'blow dry', 'styling', 'style cut', 'kids cut', 'men cut', 'women cut',
            ],
            'related' => ['spa', 'hair-color', 'keratin'],
        ],

        'hair-color' => [
            'label' => 'Hair Color',
            'category' => 'Hair',
            'keywords' => [
                'hair color', 'hair colour', 'coloring', 'colouring', 'highlights', 'balayage',
                'ombre', 'dye', 'tint', 'global color', 'global colour', 'root touch', 'bleach',
            ],
            'related' => ['haircut', 'keratin', 'spa'],
        ],

        'keratin' => [
            'label' => 'Hair Treatment',
            'category' => 'Hair',
            'keywords' => [
                'keratin', 'smoothening', 'smoothing', 'straightening', 'rebonding', 'botox',
                'hair treatment', 'protein', 'deep conditioning', 'hair repair',
            ],
            'related' => ['spa', 'hair-color', 'haircut'],
        ],

        'spa' => [
            'label' => 'Hair Spa',
            'category' => 'Hair',
            'keywords' => [
                'hair spa', 'spa treatment', 'head spa', 'scalp spa', 'scalp treatment',
                'hot oil', 'oil treatment',
            ],
            'related' => ['keratin', 'haircut', 'massage'],
        ],

        'facial' => [
            'label' => 'Facial',
            'category' => 'Skin',
            'keywords' => [
                'facial', 'cleanup', 'clean up', 'cleansing', 'deep cleansing', 'skincare',
                'skin care', 'face treatment', 'anti aging', 'anti-ageing', 'glow', 'peel',
            ],
            'related' => ['makeup', 'waxing', 'threading'],
        ],

        'makeup' => [
            'label' => 'Makeup',
            'category' => 'Makeup',
            'keywords' => [
                'makeup', 'make up', 'bridal', 'bride', 'party makeup', 'engagement',
                'cosmetic', 'beauty', 'glam', 'hd makeup',
            ],
            'related' => ['facial', 'threading', 'nails'],
        ],

        'nails' => [
            'label' => 'Nails',
            'category' => 'Nails',
            'keywords' => [
                'nail', 'nails', 'manicure', 'pedicure', 'mani pedi', 'gel nails', 'acrylic',
                'nail art', 'polish', 'cuticle',
            ],
            'related' => ['makeup', 'threading', 'default'],
        ],

        'beard' => [
            'label' => 'Beard & Grooming',
            'category' => 'Grooming',
            'keywords' => [
                'beard', 'beard trim', 'beard shape', 'shave', 'shaving', 'grooming',
                'trim beard', 'mustache', 'moustache', 'fade', 'men grooming',
            ],
            'related' => ['haircut', 'default', 'spa'],
        ],

        'massage' => [
            'label' => 'Massage & Spa',
            'category' => 'Spa',
            'keywords' => [
                'massage', 'body massage', 'full body massage', 'therapy', 'aromatherapy',
                'swedish', 'deep tissue', 'relaxation', 'body spa', 'reflexology',
            ],
            'related' => ['spa', 'facial', 'waxing'],
        ],

        'waxing' => [
            'label' => 'Waxing',
            'category' => 'Skin',
            'keywords' => [
                'wax', 'waxing', 'body wax', 'full body wax', 'bikini wax', 'leg wax',
                'arm wax', 'underarm', 'de-fuzz', 'defuzz',
            ],
            'related' => ['threading', 'facial', 'massage'],
        ],

        'threading' => [
            'label' => 'Threading',
            'category' => 'Beauty',
            'keywords' => [
                'threading', 'eyebrow', 'brow', 'brows', 'upper lip', 'face threading',
                'eyebrow shape', 'brow shape',
            ],
            'related' => ['makeup', 'facial', 'waxing'],
        ],

        'default' => [
            'label' => 'Salon Service',
            'category' => 'General',
            'keywords' => [],
            'related' => ['haircut', 'facial', 'spa'],
        ],

    ],

];
