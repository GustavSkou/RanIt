<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class GenerateMapImage extends Command
{
    protected $signature = 'map:generate {pointsJson} {outputPath} {--width=800} {--height=600}';
    protected $description = 'Generate map image from GPS points';

    public function handle()
    {
        $pointsJson = $this->argument('pointsJson');
        $outputPath = $this->argument('outputPath');
        $width = $this->option('width');
        $height = $this->option('height');

        // Create Node.js execution script
        $nodeScript = resource_path('js/execute-map-generator.js');

        $process = new Process([
            'node',
            $nodeScript,
            $pointsJson,
            $outputPath,
            $width,
            $height
        ]);

        $process->run();
        
        if (!$process->isSuccessful()) {
            Log::info('Failed to generate map image', $process->getErrorOutput());
        } else {
            Log::info('Map image generated successfully', $outputPath);
        }

        return $process->isSuccessful();
    }
}
