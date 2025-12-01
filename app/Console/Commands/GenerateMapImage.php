<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class GenerateMapImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:generate {pointsFilePath} {outputPath} {--width=800} {--height=600}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate map image from GPS points';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pointsFilePath = $this->argument('pointsFilePath');
        $outputPath = $this->argument('outputPath');
        $width = $this->option('width');
        $height = $this->option('height');

        // Create Node.js execution script
        $nodeScript = resource_path('js/execute-map-generator.js');

        $process = new Process([
            'node',
            $nodeScript,
            $pointsFilePath,
            $outputPath,
            $width,
            $height
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            Log::info('Failed to generate map image', ['error' => $process->getErrorOutput()]);
        } else {
            Log::info('Map image generated successfully', ['output path' => $outputPath]);
        }

        return $process->isSuccessful();
    }
}
