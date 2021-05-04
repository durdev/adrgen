<?php

namespace App\Commands;

use Illuminate\Filesystem\Filesystem;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;

class AdrCrudCommand extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make:crud
        {model : The name of the entity}
        {--actions-dir= : Your actions classes folder}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a set of files and folders to accomplish the ADR pattern';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Filesystem $files)
    {
        try {
            $this->validateOptions();
        } catch (RuntimeException $runtimeException) {
            $this->comment($runtimeException->getMessage(), $runtimeException->getCode());
            return 1;
        }

        $actionsDir = $this->option('actions-dir');
        $modelName  = ucfirst($this->argument('model'));
        $targetDir  = $actionsDir . '/' . $modelName;

        if ($files->exists($targetDir) === false) {
            $files->copyDirectory(__DIR__ . '/../../stubs/Entity', $targetDir);

            foreach($files->allFiles($targetDir) as $file) {
                $newName      = str_replace('Entity', $modelName, $file->getFilename());
                $newPath      = $file->getPath() . '/' . $newName;
                $newNamespace = trim(ucwords($targetDir, '/'), '/');

                $files->move($file->getRealPath(), $newPath);

                $content = $files->get($newPath);

                if (str_contains($content, '{namespace}')) {
                    $content = str_replace('{namespace}', $newNamespace, $content);
                }

                if (str_contains($content, 'Entity')) {
                    $content = str_replace('Entity', $modelName, $content);
                }

                $files->put($newPath, $content);
            }
        }

        if ($files->exists($targetDir)) {
            foreach($files->allFiles($targetDir) as $file) {
                if ($files->exists($file->getPathname()) === false) {
                    $newName      = str_replace('Entity', $modelName, $file->getFilename());
                    $newPath      = $file->getPath() . '/' . $newName;
                    $newNamespace = trim(ucwords($targetDir, '/'), '/');

                    $files->move($file->getRealPath(), $newPath);

                    $content = $files->get($newPath);

                    if (str_contains($content, '{namespace}')) {
                        $content = str_replace('{namespace}', $newNamespace, $content);
                    }

                    if (str_contains($content, 'Entity')) {
                        $content = str_replace('Entity', $modelName, $content);
                    }

                    $files->put($newPath, $content);
                }
            }
        }

        $this->info('User ADR folders and classes created successfuly.');
    }

    private function validateOptions()
    {
        if($this->option('actions-dir') == null) {
            throw new RuntimeException('Actions target directory is required.', 1);
        }
    }

}
