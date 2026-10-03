<?php

declare(strict_types=1);

/**
 * Sembrador de datos de demostración vía HTTP API
 * Cumple la regla arquitectónica: NUNCA se conecta directamente a la BD.
 */

$apiUrl = getenv('API_URL') ?: 'http://localhost:8000';
echo "=================================================\n";
echo "Simple Stock Flow — CLI Seeder (HTTP Client)\n";
echo "Target API: {$apiUrl}\n";
echo "=================================================\n\n";

function request(string $method, string $url, ?array $body = null, ?string $token = null): array
{
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    if ($token !== null) {
        $headers[] = "Authorization: Bearer {$token}";
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $json = is_string($response) ? json_decode($response, true) : null;
    return [
        'code' => $httpCode,
        'body' => $json,
        'raw' => $response,
    ];
}

// 1. Login como Admin
echo "1. Autenticando como Admin...\n";
$adminUsername = getenv('ADMIN_USERNAME') ?: 'admin';
$adminPassword = getenv('ADMIN_PASSWORD') ?: 'Admin123456*';

$loginRes = request('POST', "{$apiUrl}/api/auth/login", [
    'username' => $adminUsername,
    'password' => $adminPassword,
]);

if ($loginRes['code'] !== 200 || !isset($loginRes['body']['accessToken'])) {
    echo "❌ Error al autenticar como admin (HTTP {$loginRes['code']}). Asegúrate de que la API esté corriendo.\n";
    exit(1);
}

$adminToken = $loginRes['body']['accessToken'];
echo "✅ Admin autenticado exitosamente.\n\n";

// 2. Registrar Vendedor Demo
echo "2. Registrando vendedor demo...\n";
$sellerRes = request('POST', "{$apiUrl}/api/auth/register", [
    'username' => 'vendedor_demo',
    'password' => 'Vendedor1234*',
], $adminToken);

if ($sellerRes['code'] === 201) {
    echo "✅ Vendedor 'vendedor_demo' registrado exitosamente.\n";
} else {
    echo "ℹ️ Vendedor 'vendedor_demo' ya existe o respondió HTTP {$sellerRes['code']}.\n";
}
echo "\n";

// 3. Obtener Categorías
echo "3. Consultando categorías...\n";
$catRes = request('GET', "{$apiUrl}/api/categories", null, $adminToken);
if ($catRes['code'] !== 200 || !is_array($catRes['body'])) {
    echo "❌ Error al obtener categorías.\n";
    exit(1);
}

$categoryMap = [];
foreach ($catRes['body'] as $cat) {
    $categoryMap[$cat['name']] = $cat['id'];
}
echo "✅ " . count($categoryMap) . " categorías encontradas.\n\n";

// 4. Sembrar Productos Demo
echo "4. Sembrando productos demo en el catálogo...\n";
$demoProducts = [
    [
        'name' => 'Martillo de Uña Curva 16oz',
        'price' => 28500.0,
        'stock' => 25,
        'category' => 'Herramientas',
    ],
    [
        'name' => 'Juego Destornilladores x6',
        'price' => 19900.0,
        'stock' => 30,
        'category' => 'Herramientas',
    ],
    [
        'name' => 'Cable Eléctrico THHN #12 (100m)',
        'price' => 145000.0,
        'stock' => 10,
        'category' => 'Electricidad',
    ],
    [
        'name' => 'Llave de Tubo Stilson 14"',
        'price' => 42000.0,
        'stock' => 15,
        'category' => 'Fontanería',
    ],
    [
        'name' => 'Pintura Vinilo Tipo 1 Blanco Galón',
        'price' => 78000.0,
        'stock' => 18,
        'category' => 'Pinturas',
    ],
];

$createdProductIds = [];
foreach ($demoProducts as $prod) {
    $catId = $categoryMap[$prod['category']] ?? array_values($categoryMap)[0];
    $prodRes = request('POST', "{$apiUrl}/api/products", [
        'name' => $prod['name'],
        'price' => $prod['price'],
        'stock' => $prod['stock'],
        'categoryId' => $catId,
    ], $adminToken);

    if ($prodRes['code'] === 201 && isset($prodRes['body']['id'])) {
        $createdProductIds[] = $prodRes['body']['id'];
        echo "  - Creado: {$prod['name']} (Stock: {$prod['stock']}, Precio: \${$prod['price']} COP)\n";
    }
}
echo "✅ Catálogo sembrado.\n\n";

// 5. Registrar una Venta Demo como Vendedor
echo "5. Autenticando vendedor y registrando venta de prueba...\n";
$sellerLogin = request('POST', "{$apiUrl}/api/auth/login", [
    'username' => 'vendedor_demo',
    'password' => 'Vendedor1234*',
]);

if ($sellerLogin['code'] === 200 && isset($sellerLogin['body']['accessToken']) && count($createdProductIds) >= 2) {
    $sellerToken = $sellerLogin['body']['accessToken'];
    $saleRes = request('POST', "{$apiUrl}/api/sales", [
        'lines' => [
            [
                'productId' => $createdProductIds[0],
                'quantity' => 2,
            ],
            [
                'productId' => $createdProductIds[1],
                'quantity' => 1,
            ],
        ],
    ], $sellerToken);

    if ($saleRes['code'] === 201) {
        echo "✅ Venta registrada exitosamente. Total: \${$saleRes['body']['total']} COP (Líneas: " . count($saleRes['body']['items']) . ")\n";
    } else {
        echo "❌ Error al registrar venta demo: {$saleRes['raw']}\n";
    }
} else {
    echo "ℹ️ Vendedor no autenticado para venta demo.\n";
}

echo "\n=================================================\n";
echo "🎉 Siembra de datos completada exitosamente.\n";
echo "=================================================\n";
