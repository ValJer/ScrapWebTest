<?php
header('Content-Type: application/json');

// Initialize the cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.decora.ee/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request
$html = curl_exec($ch);
curl_close($ch);

// Load the HTML into a DOMDocument
$dom = new DOMDocument();
@$dom->loadHTML($html); // Suppress warnings for invalid HTML

$xpath = new DOMXPath($dom);

// Get top-level category names, ensuring we ignore nested categories
$categories = [];

// Fetch all top-level categories
$categoryNodes = $xpath->query("//div[contains(@class, 'categories-widget')]//li[contains(@class, 'cat') and not(ancestor::ul[contains(@class, 'sub-menu')])]");

// Loop through each top-level category node found
foreach ($categoryNodes as $node) {
    // Extract category name
    $categoryNameNode = $xpath->query(".//span[contains(@class, 'cat-name')]", $node);
    if ($categoryNameNode->length > 0) {
        $categoryName = trim($categoryNameNode->item(0)->textContent);

        // Count immediate subcategories
        $subcategoryCount = $xpath->query(".//ul[contains(@class, 'sub-menu lvl-2')][1]/li", $node)->length; // Only count direct children of the first sub-menu

        // Placeholder for items count
        $itemsCount = 0; // This can be updated later based on additional scraping

        // Append to the categories array
        $categories[] = [
            'category_name' => $categoryName,
            'subcategory_count' => $subcategoryCount,
            'items_count' => $itemsCount
        ];
    }
}

// Output the categories as JSON
echo json_encode($categories);

