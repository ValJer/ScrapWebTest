export function drawPieChart(data, chartId, countType, chartName) {
    const chartCanvas = document.getElementById(chartId);
    const ctx = chartCanvas.getContext('2d');

    // Clear previous chart
    ctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);

    const labels = data.map(item => `${item.category_name} (${item[countType]})`);
    const values = data.map(item => item[countType]);

    const total = values.reduce((acc, val) => acc + val, 0);
    const colors = generateDistinctColors(values.length);

    const centerX = chartCanvas.width / 2;
    const centerY = chartCanvas.height / 2;
    const radius = Math.min(centerX, centerY) - 40; // Adjust padding
    let startAngle = 0;

    // Draw each segment
    values.forEach((value, index) => {
        const sliceAngle = (value / total) * 2 * Math.PI;

        // Draw the slice
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.arc(centerX, centerY, radius, startAngle, startAngle + sliceAngle);
        ctx.fillStyle = colors[index];
        ctx.fill();

        startAngle += sliceAngle;
    });

    // Add category names outside the chart
    startAngle = 0;
    const textOffset = radius + 20; // Move text outside the chart

    values.forEach((value, index) => {
        const sliceAngle = (value / total) * 2 * Math.PI;
        const textX = centerX + textOffset * Math.cos(startAngle + sliceAngle / 2);
        const textY = centerY + textOffset * Math.sin(startAngle + sliceAngle / 2);

        ctx.fillStyle = '#000';
        ctx.font = 'bold 12px Arial';
        ctx.fillText(labels[index], textX, textY);

        startAngle += sliceAngle;
    });

    // Draw the chart title dynamically
    ctx.fillStyle = '#000';
    ctx.font = 'bold 16px Arial';
    ctx.textAlign = 'center';
    ctx.fillText(chartName, centerX, 15);
}

function generateDistinctColors(numColors) {
    const colors = [];
    for (let i = 0; i < numColors; i++) {
        const hue = (i * 360 / numColors) % 360; // Spread colors evenly around the hue circle
        const color = `hsl(${hue}, 70%, 50%)`; // Set saturation and lightness
        colors.push(color);
    }
    return colors;
}
