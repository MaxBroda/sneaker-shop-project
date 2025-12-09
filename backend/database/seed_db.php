<?php
require_once __DIR__ . '/../utils/db_connection.php';

try {
    echo "Starting database seeding...\n\n";

    // ========================================
    // CREATE USERS
    // ========================================
    echo "Creating users...\n";

    // Seller 1: Peter
    $stmt = $pdo->prepare("
        INSERT INTO users (email, first_name, last_name, password_hash, role)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        'emailPeter@example.com',
        'Peter',
        'Petersen',
        password_hash('test', PASSWORD_DEFAULT),
        'seller'
    ]);
    $peterUserId = $pdo->lastInsertId();
    echo "✓ Created seller: Peter Petersen (ID: $peterUserId)\n";

    // Seller 2: Max
    $stmt->execute([
        'emailMax@example.com',
        'Max',
        'Mustermann',
        password_hash('test', PASSWORD_DEFAULT),
        'seller'
    ]);
    $maxUserId = $pdo->lastInsertId();
    echo "✓ Created seller: Max Mustermann (ID: $maxUserId)\n";

    // Customer: Lisa
    $stmt->execute([
        'emailLisa@example.com',
        'Lisa',
        'Lustig',
        password_hash('test', PASSWORD_DEFAULT),
        'customer'
    ]);
    $lisaUserId = $pdo->lastInsertId();
    echo "✓ Created customer: Lisa Lustig (ID: $lisaUserId)\n\n";

    // ========================================
    // CREATE ADDRESSES
    // ========================================
    echo "Creating addresses...\n";

    $addrStmt = $pdo->prepare("
        INSERT INTO addresses (user_id, street, house_number, city, postal_code, country, is_default)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    // Peter's addresses
    $addrStmt->execute([$peterUserId, 'Hauptstraße', '42', 'München', '80331', 'Deutschland', 1]);
    $addrStmt->execute([$peterUserId, 'Nebenstraße', '7', 'München', '80333', 'Deutschland', 0]);
    $addrStmt->execute([$peterUserId, 'Gartenweg', '15', 'München', '80335', 'Deutschland', 0]);
    echo "✓ Created 3 addresses for Peter\n";

    // Max's addresses
    $addrStmt->execute([$maxUserId, 'Bahnhofstraße', '123', 'Berlin', '10115', 'Deutschland', 1]);
    $addrStmt->execute([$maxUserId, 'Alexanderplatz', '5', 'Berlin', '10178', 'Deutschland', 0]);
    echo "✓ Created 2 addresses for Max\n";

    // Lisa's addresses
    $addrStmt->execute([$lisaUserId, 'Rosenweg', '88', 'Hamburg', '20095', 'Deutschland', 1]);
    $addrStmt->execute([$lisaUserId, 'Hafenstraße', '22', 'Hamburg', '20097', 'Deutschland', 0]);
    $addrStmt->execute([$lisaUserId, 'Alsterweg', '34', 'Hamburg', '20099', 'Deutschland', 0]);
    $addrStmt->execute([$lisaUserId, 'Elbchaussee', '99', 'Hamburg', '22765', 'Deutschland', 0]);
    echo "✓ Created 4 addresses for Lisa\n\n";

    // ========================================
    // CREATE PRODUCTS
    // ========================================
    echo "Creating products...\n";

    $productStmt = $pdo->prepare("
        INSERT INTO products (seller_id, name, description, price, category, image, technical_specs, tag_icon, tag_text)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // ========================================
    // PETER'S PRODUCTS (14 products)
    // ========================================
    
    // Product 1 - Running
    $productStmt->execute([
        $peterUserId,
        'Nike Air Zoom Pegasus 40',
        'Der Nike Air Zoom Pegasus 40 ist der ultimative Laufschuh für Läufer jeden Levels. Mit seiner reaktionsfreudigen Dämpfung und dem atmungsaktiven Mesh-Obermaterial bietet er maximalen Komfort bei jedem Schritt. Die Zoom Air-Einheit im Vorfuß sorgt für explosive Energie-Rückführung, während die gepolsterte Ferse zusätzlichen Halt bietet. Das erneuerte Design mit verbesserter Passform macht ihn zum perfekten Begleiter für deine täglichen Läufe, egal ob kurze Sprints oder lange Distanzen.',
        129.99,
        'Running',
        'placeholder_3.jpg',
        "Gewicht: 280g (Größe 42)\nSprengung: 10mm\nDämpfung: Nike Air Zoom\nObermaterial: Engineered Mesh\nZwischensohle: React Foam\nAußensohle: Gummi mit Waffelmuster\nEinsatzbereich: Straßenlauf, Training",
        'mdi:fire',
        'Bestseller'
    ]);
    echo "✓ Created: Nike Air Zoom Pegasus 40\n";

    // Product 2 - Sneaker
    $productStmt->execute([
        $peterUserId,
        'Jordan Luka 2',
        'Der Jordan Luka 2 ist der Signature-Schuh von NBA-Star Luka Dončić. Entwickelt für explosive Bewegungen und schnelle Richtungswechsel auf dem Court. Die IsoPlate-Technologie bietet außergewöhnliche Stabilität, während die Formula 23-Schaumstoffdämpfung für weiche Landungen sorgt. Das niedrige Profil ermöglicht maximale Bewegungsfreiheit am Knöchel. Mit seinem auffälligen Design und der Performance-orientierten Konstruktion ist dieser Schuh perfekt für Guards, die Geschwindigkeit und Kontrolle benötigen.',
        139.99,
        'Sneaker',
        'placeholder_4.jpg',
        "Gewicht: 365g (Größe 42)\nSchafthöhe: Low-Top\nDämpfung: Formula 23 Foam\nStabilität: IsoPlate Technology\nObermaterial: Mesh mit TPU-Verstärkungen\nGrip: Multidirektionales Tractionsmuster\nVerschluss: Schnürung mit Fersenkappe",
        'mdi:star',
        'Signature'
    ]);
    echo "✓ Created: Jordan Luka 2\n";

    // Product 3 - Sneaker
    $productStmt->execute([
        $peterUserId,
        'Adidas Samba OG',
        'Ein zeitloser Klassiker: Der Adidas Samba OG vereint Retro-Charme mit modernem Komfort. Ursprünglich für Hallenfußball entwickelt, ist er heute ein unverzichtbarer Lifestyle-Sneaker. Das Premium-Lederobermaterial in klassischem Schwarz-Weiß sorgt für einen eleganten Look, während die charakteristischen drei Streifen das ikonische Adidas-Design unterstreichen. Die Gummi-Außensohle bietet hervorragende Traktion auf verschiedenen Untergründen. Perfekt kombinierbar mit Jeans, Chinos oder sportlichen Outfits.',
        99.99,
        'Sneaker',
        'placeholder_5.jpg',
        "Gewicht: 320g (Größe 42)\nObermaterial: Vollnarbenleder\nInnenfutter: Textil\nZwischensohle: EVA\nAußensohle: Gummi mit Hallenprofil\nBesonderheit: Goldfarbene Samba-Prägung\nPassform: Regular Fit",
        'mdi:fire',
        'Klassiker'
    ]);
    echo "✓ Created: Adidas Samba OG\n";

    // Product 4 - Running, Training
    $productStmt->execute([
        $peterUserId,
        'ASICS Gel-Kayano 30',
        'Der ASICS Gel-Kayano 30 ist die neueste Evolution der legendären Stabilitätsschuh-Serie. Entwickelt für Überpronierer und Läufer, die maximale Unterstützung benötigen. Die FF BLAST PLUS ECO-Dämpfung bietet ein weiches, reaktionsfreudiges Laufgefühl, während die 4D GUIDANCE SYSTEM-Technologie adaptive Stabilität liefert. Das atmungsaktive Jacquard-Mesh-Obermaterial sorgt für optimale Belüftung auch bei langen Läufen. Die PureGEL-Technologie absorbiert Stöße effektiv und schützt deine Gelenke.',
        179.99,
        'Running, Training',
        'placeholder_6.jpg',
        "Gewicht: 310g (Größe 42)\nSprengung: 10mm\nDämpfung: FF BLAST PLUS ECO + PureGEL\nStabilität: 4D GUIDANCE SYSTEM\nObermaterial: Engineered Jacquard Mesh\nZwischensohle: FlyteFoam\nAußensohle: AHARPLUS Gummi\nNachhaltigkeit: Recycelte Materialien",
        'mdi:leaf',
        'ECO'
    ]);
    echo "✓ Created: ASICS Gel-Kayano 30\n";

    // Product 5 - Outdoor
    $productStmt->execute([
        $peterUserId,
        'Salomon Speedcross 6',
        'Der Salomon Speedcross 6 ist der ultimative Trailrunning-Schuh für anspruchsvolles Gelände. Die aggressive Chevron-Stollen-Konfiguration bietet unübertroffenen Grip auf Schlamm, Schnee und felsigem Untergrund. Das Quicklace-System ermöglicht sekundenschnelles An- und Ausziehen. Die EnergyCell+ Zwischensohle kombiniert Dämpfung mit Energie-Rückführung für kraftvolle Bergaufläufe. Das SensiFit-System umschließt den Fuß für präzisen, sicheren Halt. Mit diesem Schuh meisterst du jeden Trail, egal wie technisch oder schlammig.',
        149.99,
        'Outdoor',
        'placeholder_7.jpg',
        "Gewicht: 298g (Größe 42)\nSprengung: 10mm\nDämpfung: EnergyCell+\nStollenhöhe: 5mm Chevron-Profil\nObermaterial: Anti-Debris Mesh\nVerschluss: Quicklace System\nSchutz: Mud Contagrip Außensohle\nEinsatz: Technisches Trailrunning",
        'mdi:trending-up',
        'Trail Pro'
    ]);
    echo "✓ Created: Salomon Speedcross 6\n";

    // Product 6 - Sneaker, Training
    $productStmt->execute([
        $peterUserId,
        'Nike LeBron 21',
        'Der Nike LeBron 21 ist LeBron James\' neuester Signature-Schuh, entwickelt für Power-Spieler, die Dominanz auf dem Court suchen. Die Zoom Air-Einheiten in Vorfuß und Ferse bieten explosive Dämpfung für kraftvolle Sprünge und harte Landungen. Das Battleknit 2.0-Obermaterial kombiniert Atmungsaktivität mit strategischer Unterstützung. Die Carbon-Fiber-Platte in der Zwischensohle maximiert die Energie-Rückführung. Mit seinem futuristischen Design und LeBrons Königssymbol ist dieser Schuh ein Statement auf und neben dem Platz.',
        199.99,
        'Sneaker, Training',
        'placeholder_8.jpg',
        "Gewicht: 425g (Größe 42)\nSchafthöhe: Mid-Top\nDämpfung: Doppel-Zoom Air\nStabilität: Carbon Fiber Shank Plate\nObermaterial: Battleknit 2.0\nAußensohle: Durables Gummi\nVerschluss: Asymmetrische Schnürung\nBesonderheit: LeBron Crown Logo",
        'mdi:star',
        'King James'
    ]);
    echo "✓ Created: Nike LeBron 21\n";

    // Product 7 - Sneaker
    $productStmt->execute([
        $peterUserId,
        'Vans Old Skool',
        'Die Vans Old Skool sind eine Ikone der Skate-Kultur seit 1977. Mit ihrem charakteristischen Seitenstreifen und der robusten Canvas-Konstruktion haben sie Generationen von Skatern begleitet. Die verstärkte Zehenkappe bietet zusätzliche Haltbarkeit beim Skateboarden, während die Waffelprofilsohle für optimalen Grip auf dem Board sorgt. Das zeitlose Schwarz-Weiß-Design passt zu jedem Style - vom Skatepark bis zur Street. Ein Must-Have für jeden Sneaker-Sammler und Skate-Enthusiasten.',
        74.99,
        'Sneaker',
        'placeholder_9.jpg',
        "Gewicht: 340g (Größe 42)\nObermaterial: Canvas + Wildleder\nInnenfutter: Textil\nZwischensohle: EVA\nAußensohle: Waffle Gummi\nVerschluss: Schnürung\nBesonderheit: Verstärkte Toe Cap\nHeritage: Seit 1977",
        'mdi:fire',
        'Skate Classic'
    ]);
    echo "✓ Created: Vans Old Skool\n";

    // Product 8 - Running, Outdoor
    $productStmt->execute([
        $peterUserId,
        'Hoka Speedgoat 5',
        'Der Hoka Speedgoat 5 ist nach Ultra-Läufer Karl "Speedgoat" Meltzer benannt und für extreme Distanzen im Gelände konzipiert. Die übergroße Zwischensohle bietet maximale Dämpfung ohne Gewichtsstrafe. Die Vibram Megagrip-Außensohle mit 5mm-Stollen meistert jedes Terrain. Das atmungsaktive Mesh mit 3D-gedruckten Overlays sorgt für Struktur ohne Gewicht. Die Metarocker-Geometrie fördert einen effizienten, rollenden Laufstil. Perfekt für Ultra-Trails, lange Bergläufe und technische Strecken.',
        159.99,
        'Running, Outdoor',
        'placeholder_10.jpg',
        "Gewicht: 295g (Größe 42)\nSprengung: 4mm\nDämpfung: CMEVA Midsole\nStollenhöhe: 5mm Multidirektional\nAußensohle: Vibram Megagrip\nObermaterial: Engineered Mesh\nBesonderheit: Metarocker Geometrie\nEinsatz: Ultra-Trails, Bergläufe",
        'mdi:trending-up',
        'Ultra Distance'
    ]);
    echo "✓ Created: Hoka Speedgoat 5\n";

    // Product 9 - Sneaker
    $productStmt->execute([
        $peterUserId,
        'New Balance 550',
        'Der New Balance 550 ist ein Basketball-Retro-Sneaker aus den 80ern, der sein Comeback als Lifestyle-Statement feiert. Das Premium-Lederobermaterial kombiniert klassischen Style mit modernem Komfort. Die minimalistische Silhouette und das ikonische "N"-Logo machen ihn zum perfekten Everyday-Sneaker. Die EVA-Zwischensohle bietet ganztägigen Tragekomfort, während die Gummi-Cupsole für Haltbarkeit sorgt. In klassischem Weiß mit grünen Akzenten ein echter Hingucker.',
        119.99,
        'Sneaker',
        'placeholder_12.jpg',
        "Gewicht: 350g (Größe 42)\nObermaterial: Premium-Leder + Synthetik\nInnenfutter: Mesh\nZwischensohle: EVA\nAußensohle: Gummi Cupsole\nVerschluss: Schnürung\nHerkunft: 80s Basketball Heritage\nStyle: Retro Low-Top",
        'mdi:star',
        'Retro Vibe'
    ]);
    echo "✓ Created: New Balance 550\n";

    // Product 10 - Training
    $productStmt->execute([
        $peterUserId,
        'Nike Metcon 9',
        'Der Nike Metcon 9 ist der ultimative Cross-Training-Schuh für intensive Workouts. Entwickelt für Gewichtheben, HIIT und funktionelles Training. Die flache, stabile Ferse bietet soliden Stand beim Heben schwerer Gewichte. Die React-Schaumstoff-Dämpfung im Vorfuß sorgt für Komfort bei dynamischen Bewegungen. Das HyperLift-System ermöglicht eine erhöhte Ferse für tiefere Squats. Die robuste Gummi-Außensohle mit Seilkletter-Zone meistert jede Trainingsherausforderung. Für Athletes, die keine Kompromisse eingehen.',
        144.99,
        'Training',
        'placeholder_13.jpg',
        "Gewicht: 345g (Größe 42)\nFersenerhöhung: Variable mit HyperLift\nDämpfung: React Foam (Vorfuß)\nStabilität: Flache, breite Ferse\nObermaterial: Flexibles Mesh + TPU\nAußensohle: Gummi mit Rope Climb Zone\nEinsatz: CrossFit, HIIT, Weightlifting\nBesonderheit: Abriebfeste Zehenkappe",
        'mdi:lightning-bolt',
        'Workout Beast'
    ]);
    echo "✓ Created: Nike Metcon 9\n";

    // Product 11 - Outdoor
    $productStmt->execute([
        $peterUserId,
        'Merrell Moab 3 Mid GTX',
        'Der Merrell Moab 3 Mid GTX (Mother Of All Boots) ist ein legendärer Wanderstiefel, der Millionen von Kilometern auf Trails weltweit zurückgelegt hat. Die Gore-Tex-Membran garantiert wasserdichte, atmungsaktive Performance. Die Vibram TC5+ Außensohle bietet überragenden Grip auf nassem und trockenem Terrain. Das Kinetic Fit Base-Fußbett unterstützt die natürliche Fußform. Der Mid-Cut-Schaft schützt die Knöchel, während das atmungsaktive Mesh-Futter Blasen vorbeugt. Perfekt für Tageswanderungen und mehrtägige Trekkingtouren.',
        169.99,
        'Outdoor',
        'placeholder_14.jpg',
        "Gewicht: 480g (Größe 42)\nSchafthöhe: Mid-Cut (Knöchelschutz)\nWasserschutz: Gore-Tex Membran\nAußensohle: Vibram TC5+\nFußbett: Kinetic Fit Base\nObermaterial: Wildleder + Mesh\nDämpfung: EVA + Air Cushion Heel\nEinsatz: Hiking, Trekking, Backpacking",
        'mdi:trending-up',
        'Trail Legend'
    ]);
    echo "✓ Created: Merrell Moab 3 Mid GTX\n";

    // Product 12 - Running
    $productStmt->execute([
        $peterUserId,
        'Adidas Adizero Adios Pro 3',
        'Der Adidas Adizero Adios Pro 3 ist ein Wettkampfschuh der Spitzenklasse für ambitionierte Marathonläufer. Entwickelt mit Weltklasse-Athleten für Rekordzeiten. Die Lightstrike Pro-Dämpfung kombiniert mit fünf Energy Rods aus Carbon sorgt für explosive Vortriebskraft. Das ultraleichte Celermesh-Obermaterial spart Gewicht ohne Stabilität zu opfern. Die Continental-Gummi-Außensohle bietet zuverlässigen Grip auch bei Nässe. Mit nur 205g (Größe 42) einer der leichtesten Racing-Schuhe auf dem Markt.',
        229.99,
        'Running',
        'placeholder_15.jpg',
        "Gewicht: 205g (Größe 42)\nSprengung: 8mm\nDämpfung: Lightstrike Pro\nVortrieb: 5x Energy Rods (Carbon)\nObermaterial: Celermesh\nAußensohle: Continental Gummi\nEinsatz: Marathon, Wettkampf\nBesonderheit: Sub-2-Marathon-Technologie",
        'mdi:lightning-bolt',
        'Race Day'
    ]);
    echo "✓ Created: Adidas Adizero Adios Pro 3\n";

    // Product 13 - Sneaker
    $productStmt->execute([
        $peterUserId,
        'Reebok Club C 85',
        'Der Reebok Club C 85 ist ein Tennis-inspirierter Retro-Sneaker, der Clean-Style auf ein neues Level hebt. Das Premium-Lederobermaterial in klassischem Weiß strahlt zeitlose Eleganz aus. Die minimalistische Silhouette mit dem dezenten Reebok-Branding macht ihn zum perfekten Lifestyle-Begleiter. Die EVA-Zwischensohle bietet überraschend guten Komfort für den ganzen Tag. Die gepolsterte Ferskappe sorgt für angenehmen Sitz. Ob zur Jeans, Shorts oder sogar zum Business-Casual - dieser Sneaker passt immer.',
        89.99,
        'Sneaker',
        'placeholder_16.jpg',
        "Gewicht: 310g (Größe 42)\nObermaterial: Vollnarbenleder\nInnenfutter: Textil\nZwischensohle: EVA\nAußensohle: Gummi\nVerschluss: Schnürung\nHeritage: 1985 Tennis Classic\nStyle: Minimalist Low-Top",
        'mdi:star',
        'Tennis Heritage'
    ]);
    echo "✓ Created: Reebok Club C 85\n";

    // Product 14 - Sneaker
    $productStmt->execute([
        $peterUserId,
        'Converse Chuck Taylor All Star',
        'Die Converse Chuck Taylor All Star sind DIE Sneaker-Ikone schlechthin. Seit 1917 unverändert im Design und zeitlos im Style. Der Canvas-Schaft mit dem charakteristischen Knöchelflicken ist weltweit erkennbar. Die vulkanisierte Gummisohle bietet erstaunlichen Grip. Ursprünglich als Basketball-Schuh entwickelt, heute ein Lifestyle-Statement für alle Generationen. Das hohe Modell in klassischem Schwarz ist der Inbegriff von Coolness. Von Rockstars bis zu Künstlern - Chuck Taylors sind Kult.',
        69.99,
        'Sneaker',
        'placeholder_17.jpg',
        "Gewicht: 390g (Größe 42)\nSchafthöhe: High-Top\nObermaterial: Canvas\nInnenfutter: Canvas\nZwischensohle: Minimal\nAußensohle: Vulkanisierte Gummi\nVerschluss: Schnürung + Knöchelflicken\nHeritage: Seit 1917 - Original Design",
        'mdi:star',
        'Timeless Icon'
    ]);
    echo "✓ Created: Converse Chuck Taylor All Star\n\n";

    // ========================================
    // MAX'S PRODUCTS (8 products)
    // ========================================

    // Product 1 - Running
    $productStmt->execute([
        $maxUserId,
        'On Cloudmonster',
        'Der On Cloudmonster revolutioniert das Lauferlebnis mit seiner massiven CloudTec-Dämpfung. Die übergroßen Cloud-Elemente bieten unvergleichlich weichen Komfort, während der Speedboard aus Nylon für kraftvolle Energie-Rückführung sorgt. Das atmungsaktive Mesh-Obermaterial mit nahtlosen Overlays umschließt den Fuß wie eine zweite Haut. Trotz der maximalen Dämpfung bleibt der Schuh mit 270g überraschend leicht. Perfect für lange Läufe, Recovery-Runs und Läufer, die maximalen Komfort schätzen.',
        169.99,
        'Running',
        'placeholder_18.jpg',
        "Gewicht: 270g (Größe 42)\nSprengung: 6mm\nDämpfung: CloudTec (Max Stack)\nVortrieb: Speedboard Nylon\nObermaterial: Engineered Mesh\nAußensohle: Missiongrip Gummi\nStackhöhe: 26mm (Ferse)\nEinsatz: Easy Runs, Long Distance",
        'mdi:new-box',
        'Max Cushion'
    ]);
    echo "✓ Created: On Cloudmonster\n";

    // Product 2 - Sneaker
    $productStmt->execute([
        $maxUserId,
        'Air Jordan 1 Retro High',
        'Die Air Jordan 1 Retro High ist die Mutter aller Sneaker. 1985 von Nike für Michael Jordan kreiert, veränderte dieser Schuh die Sneaker-Kultur für immer. Das Premium-Lederobermaterial im ikonischen Chicago-Colorway (Rot, Weiß, Schwarz) ist ein zeitloses Meisterwerk. Die Air-Sole-Unit in der Ferse bietet klassische Dämpfung. Der High-Top-Schaft mit dem Swoosh und dem Wings-Logo sind sofort erkennbar. Mehr als nur ein Schuh - ein Stück Basketball- und Kulturgeschichte.',
        189.99,
        'Sneaker',
        'placeholder_3.jpg',
        "Gewicht: 385g (Größe 42)\nSchafthöhe: High-Top\nObermaterial: Premium-Leder\nInnenfutter: Textil\nDämpfung: Nike Air (Ferse)\nAußensohle: Gummi mit Pivot Point\nColorway: Chicago (Bred)\nHeritage: 1985 Original Design",
        'mdi:fire',
        'Legend'
    ]);
    echo "✓ Created: Air Jordan 1 Retro High\n";

    // Product 3 - Training
    $productStmt->execute([
        $maxUserId,
        'Reebok Nano X3',
        'Der Reebok Nano X3 ist der offizielle Schuh der CrossFit Games und für härteste Workouts konzipiert. Die Flexweave-Technologie bietet strategische Unterstützung ohne Bewegungseinschränkung. Die Floatride Energy Foam-Dämpfung absorbiert Stöße bei Box Jumps und Burpees. Die breite, flache Plattform garantiert Stabilität beim Gewichtheben. Die extrem haltbare Gummi-Außensohle mit Rope Pro-Schutz meistert Seilklettern mühelos. Für Athletes, die ihre Grenzen verschieben wollen.',
        154.99,
        'Training',
        'placeholder_4.jpg',
        "Gewicht: 330g (Größe 42)\nFersenerhöhung: 4mm\nDämpfung: Floatride Energy Foam\nObermaterial: Flexweave\nAußensohle: Gummi mit Rope Pro\nStabilität: Breite Lift-Plattform\nEinsatz: CrossFit, HIIT, Functional Training\nBesonderheit: Official CrossFit Games Shoe",
        'mdi:lightning-bolt',
        'CrossFit Official'
    ]);
    echo "✓ Created: Reebok Nano X3\n";

    // Product 4 - Outdoor
    $productStmt->execute([
        $maxUserId,
        'La Sportiva Bushido II',
        'Der La Sportiva Bushido II ist ein technischer Bergschuh für anspruchsvolles alpines Terrain. Entwickelt für steile Anstiege, technische Abstiege und felsige Trails. Die STB Control-Konstruktion bietet präzise Kontrolle auf unebenem Untergrund. Die FriXion XT V-Groove 2-Außensohle mit Impact Brake System garantiert bombenfesten Grip bergauf und kontrolliertes Bremsen bergab. Das TPU-Skelett umschließt den Fuß für maximale Stabilität. Für ambitionierte Bergläufer und Skyrunner.',
        179.99,
        'Outdoor',
        'placeholder_5.jpg',
        "Gewicht: 285g (Größe 42)\nSprengung: 6mm\nDämpfung: Compressed EVA\nAußensohle: FriXion XT V-Groove 2\nObermaterial: AirMesh + TPU-Skelett\nSchutz: Rock Guard\nEinsatz: Technisches Trailrunning, Skyrunning\nBesonderheit: Impact Brake System",
        'mdi:trending-up',
        'Alpine Tech'
    ]);
    echo "✓ Created: La Sportiva Bushido II\n";

    // Product 5 - Sneaker
    $productStmt->execute([
        $maxUserId,
        'Puma MB.02',
        'Der Puma MB.02 ist LaMelo Balls zweiter Signature-Schuh und verbindet Performance mit extravagantem Design. Die NITRO Foam-Technologie bietet reaktionsfreudige Dämpfung für explosive Sprünge. Das MELO Mesh-Obermaterial mit strukturierten Details sorgt für Atmungsaktivität und Support. Die Non-Slip Grip-Sockliner verhindert Verrutschen im Schuh bei schnellen Cuts. Das futuristische Design mit auffälligen Colorways macht den Schuh zum Statement-Piece auf dem Court.',
        134.99,
        'Sneaker',
        'placeholder_6.jpg',
        "Gewicht: 370g (Größe 42)\nSchafthöhe: Low-Top\nDämpfung: NITRO Foam\nObermaterial: MELO Mesh\nSockliner: Non-Slip Grip\nAußensohle: Gummi mit Pivot Zone\nDesign: LaMelo Ball Signature\nBesonderheit: 1 of 1 Symbol",
        'mdi:new-box',
        'Rare'
    ]);
    echo "✓ Created: Puma MB.02\n";

    // Product 6 - Running
    $productStmt->execute([
        $maxUserId,
        'Saucony Endorphin Pro 3',
        'Der Saucony Endorphin Pro 3 ist ein Carbon-Plated Racing-Schuh für Tempo-Training und Wettkämpfe. Die PWRRUN PB-Dämpfung ist extrem leicht und reaktionsfreudig. Die S-Curve Carbon-Platte bietet sanften, aber kraftvollen Vortrieb. Das SPEEDROLL-Geometrie fördert einen effizienten, nach vorne gerichteten Laufstil. Mit nur 215g und dem atmungsaktiven Mesh-Obermaterial perfekt für Personal Records vom 5K bis zum Marathon.',
        224.99,
        'Running',
        'placeholder_7.jpg',
        "Gewicht: 215g (Größe 42)\nSprengung: 8mm\nDämpfung: PWRRUN PB\nVortrieb: S-Curve Carbon Plate\nObermaterial: Engineered Mesh\nAußensohle: XT-900 Gummi\nGeometrie: SPEEDROLL\nEinsatz: Racing, Tempo-Training",
        'mdi:lightning-bolt',
        'PR Breaker'
    ]);
    echo "✓ Created: Saucony Endorphin Pro 3\n";

    // Product 7 - Sneaker
    $productStmt->execute([
        $maxUserId,
        'Puma Suede Classic',
        'Der Puma Suede Classic ist seit 1968 ein Streetwear-Staple und Kultschuh. Das Premium-Wildleder-Obermaterial strahlt Qualität und Zeitlosigkeit aus. Das ikonische Puma-Formstripe und das goldene Branding verleihen dem Schuh Eleganz. Die EVA-Zwischensohle bietet Komfort für den ganzen Tag, während die Gummi-Außensohle Haltbarkeit garantiert. In klassischem Schwarz ein vielseitiger Begleiter für jede Gelegenheit. Von der Basketball-Legende zum Style-Icon.',
        84.99,
        'Sneaker',
        'placeholder_8.jpg',
        "Gewicht: 325g (Größe 42)\nObermaterial: Premium-Wildleder\nInnenfutter: Textil\nZwischensohle: EVA\nAußensohle: Gummi\nVerschluss: Schnürung\nHeritage: Seit 1968\nBesonderheit: Goldenes Puma-Branding",
        'mdi:heart',
        'Heritage'
    ]);
    echo "✓ Created: Puma Suede Classic\n";

    // Product 8 - Outdoor, Sneaker
    $productStmt->execute([
        $maxUserId,
        'Timberland 6-Inch Premium Boot',
        'Der Timberland 6-Inch Premium Boot ist DIE Ikone unter den Outdoor-Boots. Seit 1973 für Robustheit und Style bekannt. Das wasserdichte Premium-Lederobermaterial hält deine Füße bei jedem Wetter trocken. Die direkt angeformte PU-Außensohle garantiert außergewöhnliche Haltbarkeit und Traktion. Die gepolsterte Kragen und Anti-Fatigue-Technologie sorgen für ganztägigen Komfort. Ob auf der Baustelle, im Wald oder in der Stadt - diese Boots sind ein zeitloses Statement.',
        199.99,
        'Outdoor, Sneaker',
        'placeholder_9.jpg',
        "Gewicht: 650g (Größe 42)\nSchafthöhe: 6 Inch / 15cm\nObermaterial: Premium Nubuck Leder\nWasserschutz: Waterproof\nAußensohle: Direkt angeformtes PU\nKomfort: Anti-Fatigue Technology\nVerschluss: Schnürung + Speed Hooks\nHeritage: Seit 1973 - Original Design",
        'mdi:star',
        'Iconic Boot'
    ]);
    echo "✓ Created: Timberland 6-Inch Premium Boot\n\n";

    echo "========================================\n";
    echo "Database seeding completed successfully!\n";
    echo "========================================\n";
    echo "Summary:\n";
    echo "- 3 Users (2 Sellers, 1 Customer)\n";
    echo "- 9 Addresses\n";
    echo "- 22 Products (14 from Peter, 8 from Max)\n";
    echo "- Categories: Sneaker, Running, Training, Outdoor\n";
    echo "========================================\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
