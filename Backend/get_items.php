<?php
header('Content-Type: application/json');

$html = file_get_contents('https://www.decora.ee/');
$dom = new DOMDocument();
@$dom->loadHTML($html);

$xpath = new DOMXPath($dom);

// Example XPath to get item data (adjust as necessary)
$items = [];
$itemNodes = $xpath->query("//div[@class='item-list']//div[@class='item']");

foreach ($itemNodes as $node) {
    $itemName = trim($node->getElementsByTagName('h2')->item(0)->textContent);
    $itemPrice = trim($node->getElementsByClassName('price')->item(0)->textContent);
    $discountPrice = null;

    // Check if a discount price exists
    if ($node->getElementsByClassName('discount-price')->length > 0) {
        $discountPrice = trim($node->getElementsByClassName('discount-price')->item(0)->textContent);
    }

    $items[] = [
        'item_name' => $itemName,
        'item_price' => $itemPrice,
        'discount_price' => $discountPrice
    ];
}

echo json_encode($items);

