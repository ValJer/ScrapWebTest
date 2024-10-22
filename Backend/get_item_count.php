<?php
error_log("get_item_count script started");

function getItemsCount($category) {
    // Base URL of the site
    error_log("get_item_count function started");

    // Convert category to lowercase and replace spaces with hyphens
    $formattedCategory = strtolower(str_replace(' ', '-', $category));

    $baseUrl = 'https://www.nailpassion.ee/';
    $url = $baseUrl . $formattedCategory;
    error_log("Formatted URL is " . $url);

    // Initialize a cURL session
    $ch = curl_init();

    // Set the URL to scrape
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
        return 0;
    }

    // Close the cURL session
    curl_close($ch);

    // Create a DOMDocument to parse the HTML
    $dom = new DOMDocument();

    // Suppress errors due to invalid HTML
    @$dom->loadHTML($response);

    // Create a new DOMXPath instance
    $xpath = new DOMXPath($dom);

    // XPath query to find products
    $items = $xpath->query('//ul[contains(@class, "products")]//li[contains(@class, "entry")]//li[@class="title"]//h2');

    $totalItemsCount = $items->length;
    error_log("Total items count for category $category: " . $totalItemsCount);

    // Return the total count of items found
    return $totalItemsCount;
}

