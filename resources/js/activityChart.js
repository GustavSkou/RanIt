import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function() {
    const canvasElement = document.getElementById('chart');

    if (!canvasElement) {
        console.error('Chart canvas not found');
        return;
    }

    const speedArray = formatSpeed(getSpeedArray());

    const myChart = new Chart(canvasElement, {
        type: 'line', 
        data: {
            labels: getDistanceFromStartArray().map(distance => (Math.floor(distance * 100) / 100) + " km"),
            datasets: [
                {
                    label: 'Speed',
                    data: speedArray,
                    borderColor: 'rgba(92, 163, 225, 1)',
                    borderWidth: 2,
                    radius: 0,
                    yAxisID: 'y',
                },
                {
                    label: 'Heart rate',
                    data: window.points.map(point => point.heart_rate),
                    borderColor: 'rgba(249, 83, 119, 1)',
                    borderWidth: 2,
                    radius: 0,
                    yAxisID: 'yHeartRate',
                },
                {
                    label: 'Elevation',
                    data: window.points.map(point => point.elevation),
                    borderColor: 'rgba(0, 0, 0, 0)',
                    backgroundColor: 'rgba(0, 0, 0, 0.55)',
                    radius: 0,
                    fill: true,
                    yAxisID: 'yElevation',
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
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    reverse: shouldAxisBeReversed(),
                    title: {
                        display: true,
                        text: getSpeedUnit()   
                    }
                },
                yHeartRate: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    display: false,
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: "elevation"
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' m'; 
                        }
                    }
                },
                yElevation: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: "elevation"
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' m'; 
                        }
                    }
                },
                x: {
                    ticks: {
                        maxTicksLimit: 13,
                        
                    }
                }
            }
        }
    });
});

function formatSpeed(speedArray){
    switch (window.activity['type']) {
        case "running":
            speedArray = speedArray.map(speed => {
                let formattedSpeed = 60 / speed;
                if (formattedSpeed > 10 ){
                    return 10;
                } else {
                    return formattedSpeed;
                }
            } );
            return speedArray
        case "cycling":
            return speedArray;
        default:
            break;
    }
}

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

    const avgSpeedArray = [];
    const sliceSize = 10;
    let speedSum = 0;
    for (let i = 0; i < speedArray.length; i++) {
        for (let j = i; j < sliceSize + i; j++) {
            if (j >= speedArray.length) {
                continue;
            }
            speedSum = speedSum + speedArray[j];
        }
        avgSpeedArray[i] = speedSum / sliceSize;
        speedSum = 0;        
    }

    return avgSpeedArray;
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

function getSpeedUnit() {
    switch (window.activity['type']) {
        case "running":
            return 'min/km';
        case "cycling":
            return 'km/h';
        default:
            return 'min/km';
    }
}

function shouldAxisBeReversed() {
    switch (window.activity['type']) {
        case "running":
            return true;
        case "cycling":
            return false;
        default:
            return true;
    }
}