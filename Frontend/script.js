import {drawBarChart, drawPieChart} from './chart.js';

document.addEventListener('DOMContentLoaded', () => {
    const categoriesBtn = document.getElementById('categoriesBtn');
    const subcategoriesBtn = document.getElementById('subcategoriesBtn');
    const itemsBtn = document.getElementById('itemsBtn');
    const tableHeader = document.getElementById('table-header');
    const tableBody = document.getElementById('table-body');
    const itemsChart = document.getElementById('itemsChart');

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

    // Event listener for the categories button
    categoriesBtn.addEventListener('click', () => {
        clearCharts();
        fetchData('../backend/get_categories.php', (data) => {
            const header = ['Category Name', 'Subcategory Count', 'Items Count'];
            const rows = data.map(item => [item.category_name, item.subcategory_count, item.items_count]);
            updateTable(header, rows);
            drawPieChart(data, 'chart1', 'items_count', "Number of Items in Each Category");
            drawPieChart(data, 'chart2', 'subcategory_count', "Number of Subcategories in Each Category");
        });
    });
    // Event listener for the categories button
    subcategoriesBtn.addEventListener('click', () => {
        clearCharts();
        fetchData('../backend/get_subcategories.php', (data) => {
            const header = ['Subcategory Name', 'Items Count'];
            const rows = data.map(item => [item.category_name,  item.items_count]);
            updateTable(header, rows);
            drawBarChart(data, 'chart1', "Number of Items in Each Subcategory");

        });
    });
    function clearCharts() {
        const chart1Ctx = chart1.getContext('2d');
        const chart2Ctx = chart2.getContext('2d');

        chart1Ctx.clearRect(0, 0, chart1.width, chart1.height);
        chart2Ctx.clearRect(0, 0, chart2.width, chart2.height);
    }
    itemsBtn.addEventListener('click', () => {
        clearCharts();
        fetchData('../backend/get_items.php', (data) => {
            const header = ['Name', 'Price', 'subcategory', 'category'];
            const rows = data.map(item => [item.item_name,  item.item_price, item.item_subcategory, item.item_category]);
            updateTable(header, rows);


        });
    });

});
