<?php

error_log('script started');
include 'get_item_count.php';
$baseUrl = 'https://www.mullitaja.ee/';  // Main categories page

// Initialize cURL
$ch = curl_init();

// Set the cURL options
curl_setopt($ch, CURLOPT_URL, $baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Execute the cURL request and fetch the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    error_log('cURL error: ' . curl_error($ch));
    exit;
}

// Close the cURL session
curl_close($ch);

// Create a new DOMDocument object
$dom = new DOMDocument();

// Suppress errors due to malformed HTML
libxml_use_internal_errors(true);

// Load the HTML content into the DOMDocument object
$dom->loadHTML($response);

// Clear the errors after loading
libxml_clear_errors();

// Create a new DOMXPath object
$xpath = new DOMXPath($dom);

// Query for the main categories in the side menu
$category_nodes = $xpath->query('//div[@id="category_menu"]//li[contains(@class, "level0")]');

// Check if any category nodes were found
if ($category_nodes->length === 0) {
    echo json_encode(['error' => 'No category nodes found. Check the XPath expression or page structure.']);
    exit;
}

// Prepare an array to store the categories and their data
$categories = [];

foreach ($category_nodes as $category_node) {
    // Extract the category name and URL
    $category_link = $xpath->query('.//a', $category_node);
    if ($category_link->length > 0) {
        $category_name = trim($category_link->item(0)->nodeValue);
        $category_url = $category_link->item(0)->getAttribute('href');
    } else {
        continue; // If no category name found, skip this entry
    }

    // Count the number of subcategories within the current category
    $subcategory_count = $xpath->query('.//ul[contains(@class, "nav-list")]//li[contains(@class, "level1")]', $category_node)->length;

    // Call get_items_count.php to get the item count for the category
    $items_count = getItemsCount($category_url);

    // Store the category and its data
    $categories[] = [
        'category_name' => $category_name,
        'items_count' => $items_count,
        'subcategory_count' => $subcategory_count,
    ];
}
// Set the header to indicate JSON response
header('Content-Type: application/json');

// Output the result as JSON
echo json_encode($categories, JSON_PRETTY_PRINT);
