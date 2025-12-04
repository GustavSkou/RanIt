import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

document.addEventListener('DOMContentLoaded', function() { 
    const mapElementId = 'map';
    const points = window.points;

    createMap(mapElementId, points);
})

function createMap(elementId, points) {
     

    const lats = points.map(p => p['latitude']);
    const lngs = points.map(p => p['longitude']);
    const latlngs = lats.map( (lat, i) => [lat, lngs[i]] );

    const bounds = [
        [Math.min(...lats), Math.min(...lngs)],
        [Math.max(...lats), Math.max(...lngs)]
    ];

    // Create map
    const map = L.map('map', {
        zoomControl: true
    }).fitBounds(bounds);

    // Add tile layer
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add polyline
    L.polyline(latlngs, {
        color: 'red',
        weight: 3,
        opacity: 0.8
    }).addTo(map);
}