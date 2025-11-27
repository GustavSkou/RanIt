import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

document.addEventListener('DOMContentLoaded', function() {
    // go trough all map containers
    const mapContainers = document.querySelectorAll('.map-container');    
    mapContainers.forEach(container => {
        const activityId = container.dataset.activityId;
        const elementId = container.id;
        
        // Create map for this activity
        let map = createMap(elementId);
        drawRoute(points);
        // Fetch points data and draw route
        
    });
});

/**
   * Create Map and bind it to the html element using the elementId
   * @param {String} elementId;
   * @returns {L.Map}
   */
function createMap(elementId) {
    var map = L.map(elementId, { zoomControl: false }).setView([0, 0], 19);
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);
    return map;
}

  /**
   * draws the point onto the map
   * [
   *    [1.1, 2.1]
   *    [1.3, 2.2]
   * ]
   * @param {L.Map} map 
   * @returns {L.Polygon}
   */
  function drawRoute(points, map) {
    var polyline = new L.Polyline(pointList, {
        color: 'red',
        weight: 3,
        opacity: 0.5,
        smoothFactor: 1
        });

        polyline.addTo(map);
  }