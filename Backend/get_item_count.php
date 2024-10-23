<?php
function getItemsCount($url) {  //count items on page when url provided
    // Initialize total items count
    $total_items = 0;
    $current_page = 1; // Start from the first page

    while (true) {
        // Create a new cURL session for the current page
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $url . '?page=' . $current_page); // Append the page number
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute the cURL request and fetch the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            error_log('cURL error while fetching items count for page ' . $current_page . ': ' . curl_error($ch));
            curl_close($ch);
            break; // Exit loop on error
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

        // Query for the items on the current page
        $item_count_nodes = $xpath->query('//div[contains(@class, "item")]');
        $total_items += $item_count_nodes->length; // Increment total items with the count on this page

        // Query for the pagination section
        $pagination_nodes = $xpath->query('//div[contains(@class, "pagination listing_header_row2")]//div[contains(@class, "links")]');

        // Check if pagination exists
        if ($pagination_nodes->length > 0) {
            $links = $pagination_nodes->item(0)->getElementsByTagName('a');
            $next_page_exists = false;

            // Check for the next page link
            foreach ($links as $link) {
                // Check if the link contains the next page number
                $href = $link->getAttribute('href');
                if (strpos($href, 'page=' . ($current_page + 1)) !== false) {
                    $next_page_exists = true; // Next page exists
                    break; // Exit the loop as we found the next page
                }
            }

            // If next page does not exist, stop the loop
            if (!$next_page_exists) {
                break; // Exit the loop
            }
        } else {
            // If no pagination found, exit the loop
            break; // Exit the loop
        }

        // Increment to the next page
        $current_page++;
    }

    // Return the total count of items found
    return $total_items;
}
function getItems($url) {   //fetches item names and prices when url provided
    // Initialize an empty array to store item details
    $items = [];
    $current_page = 1; // Start from the first page

    while (true) {
        // Create a new cURL session for the current page
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $url . '?page=' . $current_page); // Append the page number
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute the cURL request and fetch the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            error_log('cURL error while fetching items for page ' . $current_page . ': ' . curl_error($ch));
            curl_close($ch);
            break; // Exit loop on error
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

        // Query for the items on the current page
        $item_nodes = $xpath->query('//div[contains(@class, "item")]');

        // Loop through each item and fetch details
        foreach ($item_nodes as $item_node) {
            // Get item name
            $name_node = $xpath->query('.//div[contains(@class, "product-name")]/a', $item_node);
            $item_name = $name_node->length > 0 ? trim($name_node->item(0)->nodeValue) : 'Unknown';

            // Get item price
            $price_node = $xpath->query('.//div[contains(@class, "product-price")]', $item_node);
            if ($price_node->length > 0) {
                $item_price_raw = trim($price_node->item(0)->nodeValue);
                // Use regex to extract the numeric price value along with the currency symbol
                if (preg_match('/([0-9.,]+)(€)?/', $item_price_raw, $matches)) {
                    $item_price = trim($matches[0]); // Get the matched price value
                } else {
                    $item_price = 'Unknown';
                }
            } else {
                $item_price = 'Unknown';
            }

            // Add the item details to the array
            $items[] = [
                'name' => $item_name,
                'price' => $item_price
            ];
        }

        // Query for the pagination section
        $pagination_nodes = $xpath->query('//div[contains(@class, "pagination listing_header_row2")]//div[contains(@class, "links")]');

        // Check if pagination exists
        if ($pagination_nodes->length > 0) {
            $links = $pagination_nodes->item(0)->getElementsByTagName('a');
            $next_page_exists = false;

            // Check for the next page link
            foreach ($links as $link) {
                // Check if the link contains the next page number
                $href = $link->getAttribute('href');
                if (strpos($href, 'page=' . ($current_page + 1)) !== false) {
                    $next_page_exists = true; // Next page exists
                    break; // Exit the loop as we found the next page
                }
            }

            // If next page does not exist, stop the loop
            if (!$next_page_exists) {
                break; // Exit the loop
            }
        } else {
            // If no pagination found, exit the loop
            break; // Exit the loop
        }

        // Increment to the next page
        $current_page++;
    }

    // Return the array of items with their names and prices
    return $items;
}

