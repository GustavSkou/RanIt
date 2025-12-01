import { launch } from 'puppeteer';

async function generateMapImage(points, outputPath, options = {}) {
    const browser = await launch();
    const page = await browser.newPage();
    
    // Set viewport size
    await page.setViewport({ 
        width: options.width || 800, 
        height: options.height || 600 
    });
    
    // Create HTML with Leaflet map
    const html = `
    <!DOCTYPE html>
    <html>
    <head>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <style>
            #map { height: 100vh; width: 100vw; }
            body { margin: 0; padding: 0; }
        </style>
    </head>
    <body>
        <div id="map"></div>
        <script>
            const points = ${JSON.stringify(points)};
            
            // Calculate bounds
            const lats = points.map(p => p[0]);
            const lngs = points.map(p => p[1]);
            const bounds = [
                [Math.min(...lats), Math.min(...lngs)],
                [Math.max(...lats), Math.max(...lngs)]
            ];
            
            // Create map
            const map = L.map('map', { zoomControl: false }).fitBounds(bounds, {padding: [20, 20]});
            
            // Add tile layer
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Add polyline
            L.polyline(points, {
                color: 'red',
                weight: 3,
                opacity: 0.8
            }).addTo(map);
            
            // Signal that map is ready
            window.mapReady = true;
        </script>
    </body>
    </html>
    `;
    
    await page.setContent(html);
    
    // Wait for map to load and take a screenshot
    await page.waitForFunction(() => window.mapReady === true);
    await page.screenshot({ 
            path: outputPath,
            type: 'png',
            fullPage: false
        });
}

// Export as named export
export { generateMapImage };