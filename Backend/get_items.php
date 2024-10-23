<?php

error_log('script started');
include 'get_item_count.php';  // Assuming getItems() is in get_items_function.php
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

// Prepare an array to store the items and their details
$items = [];

foreach ($category_nodes as $category_node) {
    // Extract the category name and URL
    $category_link = $xpath->query('.//a', $category_node);
    if ($category_link->length > 0) {
        $category_name = trim($category_link->item(0)->nodeValue);
        $category_url = $category_link->item(0)->getAttribute('href');
    } else {
        continue; // If no category name found, skip this entry
    }

    // Query for the subcategories within the current category
    $subcategory_nodes = $xpath->query('.//ul[contains(@class, "nav-list")]//li[contains(@class, "level1")]', $category_node);

    // If subcategories exist, loop through them
    if ($subcategory_nodes->length > 0) {
        foreach ($subcategory_nodes as $subcategory_node) {
            $subcategory_link = $xpath->query('.//a', $subcategory_node);
            if ($subcategory_link->length > 0) {
                $subcategory_name = trim($subcategory_link->item(0)->nodeValue);
                $subcategory_url = $subcategory_link->item(0)->getAttribute('href');

                // Fetch items from this subcategory
                $subcategory_items = getItems($subcategory_url);

                // Add the category and subcategory information to each item
                foreach ($subcategory_items as $item) {
                    $items[] = [
                        'item_name' => $item['name'],
                        'item_price' => $item['price'],
                        'item_category' => $category_name,
                        'item_subcategory' => $subcategory_name
                    ];
                }
            }
        }
    } else {
        // No subcategories, fetch items directly from the category page
        $category_items = getItems($category_url);

        // Add the category information to each item (no subcategory)
        foreach ($category_items as $item) {
            $items[] = [
                'item_name' => $item['name'],
                'item_price' => $item['price'],
                'item_category' => $category_name,
                'item_subcategory' => null
            ];
        }
    }
}

// Set the header to indicate JSON response
header('Content-Type: application/json');

// Output the result as JSON
echo json_encode($items);
