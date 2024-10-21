<?php
header('Content-Type: application/json');

$html = file_get_contents('https://www.decora.ee/');
$dom = new DOMDocument();
@$dom->loadHTML($html);

$xpath = new DOMXPath($dom);

// Example XPath to get subcategory data (adjust as necessary)
$subcategories = [];
$subcategoryNodes = $xpath->query("//div[@class='subcategory-list']//a");

foreach ($subcategoryNodes as $node) {
    $subcategoryName = trim($node->textContent);
    $itemsCount = 0; // Placeholder; scrape or calculate items in this subcategory

    $subcategories[] = [
        'subcategory_name' => $subcategoryName,
        'items_count' => $itemsCount
    ];
}

echo json_encode($subcategories);

