<?php

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
$main_category_nodes = $xpath->query('//div[@id="category_menu"]//li[contains(@class, "level0")]');

// Check if any main category nodes were found
if ($main_category_nodes->length === 0) {
    echo json_encode(['error' => 'No main category nodes found. Check the XPath expression or page structure.']);
    exit;
}

// Prepare an array to store the subcategories and their data
$subcategories = [];

foreach ($main_category_nodes as $main_category_node) {
    // Extract the main category name and URL (optional, can skip if not needed)
    $main_category_link = $xpath->query('.//a', $main_category_node);
    if ($main_category_link->length > 0) {
        $main_category_name = trim($main_category_link->item(0)->nodeValue);
        $main_category_url = $main_category_link->item(0)->getAttribute('href');
    } else {
        continue; // If no main category name found, skip this entry
    }

    // Query the subcategories within the current main category
    $subcategory_nodes = $xpath->query('.//ul[contains(@class, "nav-list")]//li[contains(@class, "level1")]', $main_category_node);

    // Check if subcategories are found
    if ($subcategory_nodes->length > 0) {
        foreach ($subcategory_nodes as $subcategory_node) {
            // Extract the subcategory name and URL
            $subcategory_link = $xpath->query('.//a', $subcategory_node);
            if ($subcategory_link->length > 0) {
                $subcategory_name = trim($subcategory_link->item(0)->nodeValue);
                $subcategory_url = $subcategory_link->item(0)->getAttribute('href');
            } else {
                continue; // If no subcategory name found, skip this entry
            }

            // Call getItemsCount.php to get the item count for the subcategory
            $items_count = getItemsCount($subcategory_url);

            // Store the subcategory and its data
            $subcategories[] = [
                'category_name' => $subcategory_name,
                'items_count' => $items_count,

            ];
        }
    }
}

// Set the header to indicate JSON response
header('Content-Type: application/json');

// Output the result as JSON
echo json_encode($subcategories, JSON_PRETTY_PRINT);
