const { generateMapImage } = require('./map-generator.js');

const [,, pointsJson, outputPath, width, height] = process.argv;

const points = JSON.parse(pointsJson);
const options = {
    width: parseInt(width) || 800,
    height: parseInt(height) || 600
};

generateMapImage(points, outputPath, options)
    .then(() => {
        console.log('Map image generated successfully');
        process.exit(0);
    })
    .catch(error => {
        console.error('Error generating map:', error);
        process.exit(1);
    });