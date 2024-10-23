export function drawPieChart(canvasId, data, labels) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');

    const total = data.reduce((sum, value) => sum + value, 0); // Calculate total items
    let currentAngle = 0;

    data.forEach((value, index) => {
        const sliceAngle = (value / total) * 2 * Math.PI; // Calculate the angle for each slice
        ctx.beginPath();
        ctx.moveTo(canvas.width / 2, canvas.height / 2); // Move to the center of the canvas
        ctx.arc(canvas.width / 2, canvas.height / 2, 100, currentAngle, currentAngle + sliceAngle); // Draw the slice
        ctx.closePath();

        // Set color for the slice
        ctx.fillStyle = `hsl(${(index * 360) / data.length}, 100%, 50%)`;
        ctx.fill();

        currentAngle += sliceAngle; // Update the angle for the next slice
    });


    ctx.fillStyle = '#000'; // Set text color
    ctx.font = '14px Arial';
    labels.forEach((label, index) => {
        const angle = (currentAngle - sliceAngle / 2) + (index * (sliceAngle / 2));
        const x = canvas.width / 2 + Math.cos(angle) * 70; // X position for the label
        const y = canvas.height / 2 + Math.sin(angle) * 70; // Y position for the label
        ctx.fillText(label, x, y); // Draw the label
    });
}