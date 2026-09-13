<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=UTF-8');

$page  = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$offset = ($page - 1) * $limit;

function formatProduct($p){
    return [
        "name" => [
            "en" => $p['name_en'],
            "fr" => $p['name_fr']
        ],
        "description" => [
            "en" => $p['description_en'],
            "fr" => $p['description_fr']
        ],
        "gtin" => $p['gtin'],
        "brand" => $p['brand'],
        "countryOfOrigin" => $p['country_of_origin'],
        "weight" => [
            "gross" => (float)$p['gross_weight'],
            "net" => (float)$p['net_weight'],
            "unit" => $p['weight_unit']
        ],
        "company" => [
            "companyName" => $p['company_name'],
            "companyAddress" => $p['company_address'],
            "companyTelephone" => $p['company_telephone'],
            "companyEmail" => $p['company_email'],
            "owner" => [
                "name" => $p['owner_name'],
                "mobileNumber" => $p['owner_mobile'],
                "email" => $p['owner_email']
            ],
            "contact" => [
                "name" => $p['contact_name'],
                "mobileNumber" => $p['contact_mobile'],
                "email" => $p['contact_email']
            ]
        ]
    ];
}

$gtin  = $_GET['gtin'] ?? '';
$query = $_GET['query'] ?? '';


if ($gtin) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.*
        FROM products p
        LEFT JOIN companies c ON c.id = p.company_id
        WHERE p.gtin = :gtin
        AND p.is_hidden = 0
        AND (c.is_deactivated = 0 OR c.is_deactivated IS NULL)
        LIMIT 1
    ");

    $stmt->execute(['gtin' => $gtin]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo json_encode(404);
        exit;
    }

    echo json_encode(formatProduct($product), JSON_UNESCAPED_SLASHES);
    exit;
}


$sqlCount = "
    SELECT COUNT(*) 
    FROM products p
    LEFT JOIN companies c ON c.id = p.company_id
    WHERE p.is_hidden = 0
    AND (c.is_deactivated = 0 OR c.is_deactivated IS NULL)
";

if ($query) {
    $sqlCount .= " AND (
        p.name_en LIKE :q OR
        p.name_fr LIKE :q OR
        p.description_en LIKE :q OR
        p.description_fr LIKE :q OR
        p.brand LIKE :q OR
        c.company_name LIKE :q OR
        p.gtin LIKE :q
    )";
}

$countStmt = $pdo->prepare($sqlCount);

if ($query) {
    $countStmt->bindValue(':q', "%$query%");
}

$countStmt->execute();
$total = (int)$countStmt->fetchColumn();


$sql = "
    SELECT p.*, c.*
    FROM products p
    LEFT JOIN companies c ON c.id = p.company_id
    WHERE p.is_hidden = 0
    AND (c.is_deactivated = 0 OR c.is_deactivated IS NULL)
";

if ($query) {
    $sql .= " AND (
        p.name_en LIKE :q OR
        p.name_fr LIKE :q OR
        p.description_en LIKE :q OR
        p.description_fr LIKE :q OR
        p.brand LIKE :q OR
        c.company_name LIKE :q OR
        p.gtin LIKE :q
    )";
}

$sql .= " ORDER BY p.id DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

if ($query) {
    $stmt->bindValue(':q', "%$query%");
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


$items = [];

foreach ($rows as $r) {
    $items[] = formatProduct($r);
}


echo json_encode([
    "page" => $page,
    "limit" => $limit,
    "total" => $total,
    "total_pages" => ceil($total / $limit),
    "items" => $items
], JSON_UNESCAPED_SLASHES);