import { generateMapImage } from './map-generator.js';
import { readFileSync } from 'fs';

const [,, pointsFilePath, outputPath, width, height] = process.argv;

let points = '';
try {
    const pointsJson = readFileSync(pointsFilePath, 'utf8');
    points = JSON.parse(pointsJson);
} catch (error) {
    console.error('Error reading points file:', error);
    process.exit(1);
}

const options = {
    width: parseInt(width) || 550,
    height: parseInt(height) || 211
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