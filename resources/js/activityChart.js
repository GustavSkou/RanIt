import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function() {
    // Get the canvas element
    const ctx = document.getElementById('chart');
    
    if (!ctx) {
        console.error('Chart canvas not found');
        return;
    }

    const myChart = new Chart(ctx, {
        type: 'line', 
        data: {
            labels: getDistanceFromStartArray().map(distance => (Math.floor(distance * 100) / 100) + " km"),
            datasets: [
                {
                    label: 'Pace',
                    data: getSpeedArray(),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    radius: 0,
                },
                {
                    label: 'Heart rate',
                    data: window.points.map(point => point.heart_rate),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    radius: 0,
                }
            ]
        },
        options: {
            maintainAspectRatio: true,
            plugins: {
                title: {
                    display: false,
                    text: 'Activity Statistics'
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: false,
                        text: 'Value'
                    }
                },
                x: {
                    ticks: {
                        maxTicksLimit: 10 // Show maximum 10 labels
                    }
                }
            }
        }
    });
});

function getSpeedArray() {
    if (!window.points || window.points.length < 2) {
        console.error('Not enough points to calculate speed');
        return [];
    }
    
    const speedArray = [];
    
    for (let i = 1; i < window.points.length; i++) {
        const prevPoint = window.points[i - 1];
        const currentPoint = window.points[i];
        
        const lat1 = prevPoint.latitude;
        const lng1 = prevPoint.longitude;
        const lat2 = currentPoint.latitude;
        const lng2 = currentPoint.longitude;
        
        const distance = calculateDistance(lat1, lng1, lat2, lng2);
        
        const prevTime = new Date(prevPoint.timestamp);
        const currentTime = new Date(currentPoint.timestamp);
        
        // Calculate time difference in hours
        const timeDiffSeconds = (currentTime - prevTime) / 1000; // seconds
        const timeDiffHours = timeDiffSeconds / 3600; // hours
        
        let speed = 0;
        if (timeDiffHours > 0) {
            speed = distance / timeDiffHours; // km/h
        }
        
        speedArray.push(Math.round(speed * 100) / 100); 
    }
    
    return speedArray;
}

function getDistanceFromStartArray(){
    const distanceFromStartArray = [];
    distanceFromStartArray.push(0);

    for (let i = 1; i < window.points.length; i++) {
        const prevPoint = window.points[i - 1];
        const currentPoint = window.points[i];
        
        const lat1 = prevPoint.latitude;
        const lng1 = prevPoint.longitude;
        const lat2 = currentPoint.latitude;
        const lng2 = currentPoint.longitude;
        
        const distance = calculateDistance(lat1, lng1, lat2, lng2);

        distanceFromStartArray.push( distanceFromStartArray[distanceFromStartArray.length - 1] + distance ); 
    }

    console.log(distanceFromStartArray);
    return distanceFromStartArray;
}


function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371;
    
    const dLat = (lat2 - lat1) * (Math.PI / 180);
    const dLng = (lng2 - lng1) * (Math.PI / 180);
    
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
              Math.sin(dLng / 2) * Math.sin(dLng / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c;
    
    return distance;
}