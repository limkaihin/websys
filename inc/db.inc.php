<?php
// ─────────────────────────────────────────────────────────────
//  MeowMart Data Layer — pure PHP arrays, no database needed.
//  Only login & register use MySQL (see account/ files).
// ─────────────────────────────────────────────────────────────

function getProducts(): array {
    return [
        ['id'=>1,  'name'=>'Grain-Free Salmon Pâté for Adult Cats',  'category'=>'Food',        'price'=>14.90, 'description'=>'Premium grain-free wet food packed with real salmon. High moisture content, no artificial additives.',           'is_featured'=>1],
        ['id'=>2,  'name'=>'Ultra Clumping Lavender Cat Litter 8kg', 'category'=>'Litter',      'price'=>22.50, 'description'=>'Superior clumping formula with natural lavender scent. Low dust and easy to scoop.',                          'is_featured'=>1],
        ['id'=>3,  'name'=>'Interactive Feather Wand & Refill Set',  'category'=>'Toys',        'price'=>9.90,  'description'=>'Entices natural hunting instincts with rustling feathers. Comes with 3 interchangeable refill heads.',         'is_featured'=>1],
        ['id'=>4,  'name'=>'Reversible Floral Bow Tie & Collar Set', 'category'=>'Apparel',     'price'=>12.00, 'description'=>'Adjustable collar with removable reversible bow tie. Breakaway safety buckle included.',                      'is_featured'=>1],
        ['id'=>5,  'name'=>'Freeze-Dried Chicken Treats 100g',       'category'=>'Food',        'price'=>11.50, 'description'=>'Single-ingredient freeze-dried chicken treats. No preservatives, no fillers — pure protein your cat loves.',   'is_featured'=>0],
        ['id'=>6,  'name'=>'Enclosed Self-Cleaning Litter Box',      'category'=>'Litter',      'price'=>89.00, 'description'=>'Spacious enclosed litter box with activated carbon filter. Odour-lock design for a fresher home.',            'is_featured'=>0],
        ['id'=>7,  'name'=>'Electronic Laser Chase Auto Toy',        'category'=>'Toys',        'price'=>34.90, 'description'=>'Automatic rotating laser toy with 5 speed modes. Auto shut-off after 15 minutes to avoid overstimulation.',  'is_featured'=>0],
        ['id'=>8,  'name'=>'Velvet Holiday Hoodie – Multiple Sizes', 'category'=>'Apparel',     'price'=>19.90, 'description'=>'Cosy velvet hoodie perfect for festive photos. Available in XS, S, M and L. Machine washable.',              'is_featured'=>0],
        ['id'=>9,  'name'=>'Multi-Level Cat Tree & Scratching Post', 'category'=>'Accessories', 'price'=>68.00, 'description'=>'Sturdy sisal-wrapped posts with plush platforms at multiple heights. Base measures 50x50cm for stability.',  'is_featured'=>1],
        ['id'=>10, 'name'=>'Stainless Steel Double-Bowl Feeder',     'category'=>'Accessories', 'price'=>22.00, 'description'=>'Elevated anti-spill feeder with two removable stainless steel bowls. Raised design aids digestion.',         'is_featured'=>0],
        ['id'=>11, 'name'=>'Gourmet Tuna & Prawn Mousse 12-pack',   'category'=>'Food',        'price'=>28.80, 'description'=>'Silky smooth mousse with real tuna and prawn pieces. No grain, no soy — just the good stuff.',               'is_featured'=>0],
        ['id'=>12, 'name'=>'Natural Tofu Cat Litter 6L',             'category'=>'Litter',      'price'=>16.50, 'description'=>'Plant-based tofu litter, flushable and 100% biodegradable. Clumps fast, controls odour naturally.',         'is_featured'=>0],
        ['id'=>13, 'name'=>'Faux-Fur Donut Cat Bed (Large)',         'category'=>'Accessories', 'price'=>45.00, 'description'=>'Ultra-plush self-warming donut bed, 55cm diameter. The raised rim gives cats a sense of security.',          'is_featured'=>0],
        ['id'=>14, 'name'=>'Catnip & Silver-vine Crinkle Balls',     'category'=>'Toys',        'price'=>8.90,  'description'=>'Crinkle balls infused with catnip and silver vine. Lightweight and irresistible for solo play.',             'is_featured'=>0],
        ['id'=>15, 'name'=>'Waterproof Sailor Raincoat – S/M/L',    'category'=>'Apparel',     'price'=>24.90, 'description'=>'Lightweight waterproof raincoat with reflective safety strip. Velcro closure for easy on/off.',              'is_featured'=>0],
    ];
}

function getProduct(int $id): ?array {
    foreach (getProducts() as $p) {
        if ($p['id'] === $id) return $p;
    }
    return null;
}

function filterProducts(string $category = '', string $search = '', string $sort = 'name'): array {
    $products = getProducts();

    if ($category !== '') {
        $products = array_filter($products,
            fn($p) => strtolower($p['category']) === strtolower($category));
    }
    if ($search !== '') {
        $q = strtolower($search);
        $products = array_filter($products,
            fn($p) => str_contains(strtolower($p['name']), $q)
                   || str_contains(strtolower($p['description']), $q));
    }

    usort($products, match($sort) {
        'price_asc'  => fn($a,$b) => $a['price'] <=> $b['price'],
        'price_desc' => fn($a,$b) => $b['price'] <=> $a['price'],
        'featured'   => fn($a,$b) => $b['is_featured'] <=> $a['is_featured'] ?: strcmp($a['name'],$b['name']),
        default      => fn($a,$b) => strcmp($a['name'], $b['name']),
    });

    return array_values($products);
}

function getBlogPosts(string $tag = ''): array {
    $posts = [
        ['id'=>1, 'title'=>'The Ultimate Guide to Feeding Your Cat a Balanced Diet',
         'tag'=>'Nutrition', 'author'=>'Dr. Lee Jun Wei', 'icon'=>'🥗',
         'excerpt'=>'From raw diets to premium kibble, everything you need to know to keep your cat healthy and well-fed.',
         'content'=>"Cats are obligate carnivores — their bodies are designed to thrive on animal protein.\n\nWhen choosing a cat food, look for:\n• Named protein source as the first ingredient (e.g. salmon, chicken)\n• High moisture content — wet food closely mimics a cat's natural prey diet\n• No artificial colours, flavours, or preservatives\n\nFeeding schedule: Most adult cats do well with 2 measured meals per day. Free-feeding dry food can lead to obesity, especially in indoor cats.\n\nAlways transition to new food gradually over 7–10 days to avoid digestive upset.",
         'created_at'=>'2025-03-01'],
        ['id'=>2, 'title'=>'10 Toys That Actually Keep Cats Entertained (Tested!)',
         'tag'=>'Play', 'author'=>'Priya N.', 'icon'=>'🧶',
         'excerpt'=>'Our team tested 30+ toys with real cats. Here are the clear winners that kept them engaged longest.',
         'content'=>"Not all cat toys are created equal. After months of testing, here are the toys that genuinely hold a cat's attention:\n\n1. Feather Wand — mimics bird movement, triggers hunting instinct\n2. Electronic Laser Toy — random patterns prevent cats from predicting movement\n3. Silver Vine Crinkle Balls — more potent than catnip for many cats\n4. Puzzle Feeders — slow feeding while stimulating the brain\n5. Robotic Mouse — realistic scurrying motion\n\nKey takeaway: rotate toys weekly to prevent boredom. Even the best toy loses novelty quickly.",
         'created_at'=>'2025-02-20'],
        ['id'=>3, 'title'=>'How to Groom Your Cat at Home Without the Drama',
         'tag'=>'Grooming', 'author'=>'Mei Lin', 'icon'=>'✂️',
         'excerpt'=>'Step-by-step guide for nail trimming, brushing, and bathing a reluctant cat — without battle scars.',
         'content'=>"Many cats resist grooming because they associate it with restraint. The secret is to start slow.\n\nBrushing\nStart with a soft-bristle brush and short sessions of 2–3 minutes. Brush in the direction of fur growth. Reward with treats immediately after.\n\nNail Trimming\nUse proper cat nail clippers. Cut only the clear tip, avoiding the pink quick. If your cat resists, trim one nail per day.\n\nEar Cleaning\nUse a vet-approved ear cleaner and cotton balls. Wipe the outer ear flap only.\n\nAlways end every grooming session with play or treats to build a positive association.",
         'created_at'=>'2025-02-10'],
        ['id'=>4, 'title'=>"Understanding Your Cat's Body Language",
         'tag'=>'Lifestyle', 'author'=>'Dr. Lee Jun Wei', 'icon'=>'🐱',
         'excerpt'=>'Learn what your cat is really trying to tell you through their tail, ears, and eyes.',
         'content'=>"Cats communicate constantly — you just need to know what to look for.\n\nTail Signals\n• Tail high = happy and confident\n• Tail puffed = scared or threatened\n• Tail low and tucked = anxious\n\nEar Positions\n• Forward = curious and engaged\n• Flat back = frightened or aggressive\n\nEyes\n• Slow blink = trust and affection (try blinking back!)\n• Dilated pupils = excited or scared\n• Half-closed = relaxed and content",
         'created_at'=>'2025-01-28'],
        ['id'=>5, 'title'=>'The Best Cat Trees for Singapore HDB Flats',
         'tag'=>'Lifestyle', 'author'=>'Priya N.', 'icon'=>'🏠',
         'excerpt'=>'Space-saving cat trees that keep your cats entertained without taking over your entire flat.',
         'content'=>"Living in an HDB doesn't mean your cat can't have vertical space.\n\n1. Slim Tower Trees — tall but narrow footprint, fits in corners\n2. Wall-mounted shelves — no floor space needed at all\n3. Modular systems — add or remove sections as needed\n4. Window perches — attaches to window frame, cats love watching birds\n\nWhat to look for:\n• Stable base — at least as wide as the tree is tall\n• Sisal-wrapped posts — cats prefer real sisal over rope\n• Replaceable parts — cheaper to replace a post than the whole tree",
         'created_at'=>'2025-01-15'],
    ];

    if ($tag !== '') {
        $posts = array_filter($posts, fn($p) => strtolower($p['tag']) === strtolower($tag));
    }
    return array_values($posts);
}

function getBlogPost(int $id): ?array {
    foreach (getBlogPosts() as $p) {
        if ($p['id'] === $id) return $p;
    }
    return null;
}
