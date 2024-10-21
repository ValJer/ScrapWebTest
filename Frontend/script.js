document.addEventListener('DOMContentLoaded', () => {
    const categoriesBtn = document.getElementById('categoriesBtn');
    const subcategoriesBtn = document.getElementById('subcategoriesBtn');
    const itemsBtn = document.getElementById('itemsBtn');
    const tableHeader = document.getElementById('table-header');
    const tableBody = document.getElementById('table-body');

    // Function to make an AJAX request to the backend
    function fetchData(url, callback) {
        fetch(url)
            .then(response => response.json())
            .then(data => callback(data))
            .catch(error => console.error('Error fetching data:', error));
    }

    // Function to update table header and body
    function updateTable(header, rows) {
        // Clear the table
        tableHeader.innerHTML = '';
        tableBody.innerHTML = '';

        // Populate the table header
        header.forEach(heading => {
            const th = document.createElement('th');
            th.textContent = heading;
            tableHeader.appendChild(th);
        });

        // Populate the table body
        rows.forEach(row => {
            const tr = document.createElement('tr');
            row.forEach(cellData => {
                const td = document.createElement('td');
                td.textContent = cellData;
                tr.appendChild(td);
            });
            tableBody.appendChild(tr);
        });
    }

    // Event listeners for the buttons
    categoriesBtn.addEventListener('click', () => {
        fetchData('../backend/get_categories.php', (data) => {
            const header = ['Category Name', 'Subcategory Count', 'Items Count'];
            const rows = data.map(item => [item.category_name, item.subcategory_count, item.items_count]);
            updateTable(header, rows);
        });
    });

    subcategoriesBtn.addEventListener('click', () => {
        fetchData('../backend/get_subcategories.php', (data) => {
            const header = ['Subcategory Name', 'Items Count'];
            const rows = data.map(item => [item.subcategory_name, item.items_count]);
            updateTable(header, rows);
        });
    });

    itemsBtn.addEventListener('click', () => {
        fetchData('../backend/get_items.php', (data) => {
            const header = ['Item Name', 'Item Price', 'Discount Price'];
            const rows = data.map(item => [item.item_name, item.item_price, item.discount_price || 'N/A']);
            updateTable(header, rows);
        });
    });
});
