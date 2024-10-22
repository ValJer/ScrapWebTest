<?php
include 'get_item_count.php';
// Suppress warnings from invalid HTML
libxml_use_internal_errors(true);

// URL of the page to scrape
$url = 'https://www.nailpassion.ee/pood/';

// Get the HTML content
$htmlContent = file_get_contents($url);

// Check if content was fetched successfully
if ($htmlContent === false) {
    // Return error in JSON format
    echo json_encode(['error' => 'Failed to fetch HTML content from the URL.']);
    exit;
}

// Create a new DOMDocument instance
$dom = new DOMDocument();
@$dom->loadHTML($htmlContent); // Suppress warnings for invalid HTML

// Create a new DOMXPath instance
$xpath = new DOMXPath($dom);

// Find the main categories
$mainCategories = $xpath->query('//div[contains(@class, "jet-custom-nav")]//div[contains(@class, "menu-item-has-children")]');

$categories = [];

foreach ($mainCategories as $mainCategory) {
    // Get the category name
    $categoryNode = $xpath->query('.//span[contains(@class, "top-level-label")]', $mainCategory);

    if ($categoryNode->length > 0) {
        $categoryName = trim($categoryNode->item(0)->nodeValue);

        // Count the subcategories
        $subItemsCount = $xpath->query('.//div[contains(@class, "jet-custom-nav__sub")]/div[contains(@class, "menu-item")]', $mainCategory)->length;

        // Count items in this category
        $itemCount = getItemsCount($categoryName); // Call the item counting function

        // Store the results in the categories array
        $categories[] = [
            'category_name' => $categoryName,
            'subcategory_count' => $subItemsCount,
            'items_count' => $itemCount
        ];
    }
}

// Set the content type to JSON
header('Content-Type: application/json');

// Output the result in JSON format
echo json_encode($categories);

