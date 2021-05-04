<?php

namespace App\Commands;

use Illuminate\Filesystem\Filesystem;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;

class AdrCrudCommand extends Command
{

    private $actions = ['index', 'create', 'edit', 'store', 'update', 'delete'];

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
        } catch (RuntimeException $runtime_exception) {
            $this->comment($runtime_exception->getMessage(), $runtime_exception->getCode());
            return 1;
        }

        $actions_dir = $this->option('actions-dir');
        $model_name  = ucfirst($this->argument('model'));
        $target_dir  = $actions_dir . '/' . $model_name;

        if ($files->exists($target_dir) === false) {
            $files->copyDirectory(__DIR__ . '/../../stubs/Entity', $target_dir);

            foreach($files->allFiles($target_dir) as $file) {
                $new_name      = str_replace('Entity', $model_name, $file->getFilename());
                $new_path      = $file->getPath() . '/' . $new_name;
                $new_namespace = trim(ucwords($target_dir, '/'), '/');

                $files->move($file->getRealPath(), $new_path);

                $content = $files->get($new_path);

                if (str_contains($content, '{namespace}')) {
                    $content = str_replace('{namespace}', $new_namespace, $content);
                }

                if (str_contains($content, 'Entity')) {
                    $content = str_replace('Entity', $model_name, $content);
                }

                $files->put($new_path, $content);
            }
        }

        if ($files->exists($target_dir)) {
            foreach($files->allFiles($target_dir) as $file) {
                if ($files->exists($file->getPathname()) === false) {
                    $new_name      = str_replace('Entity', $model_name, $file->getFilename());
                    $new_path      = $file->getPath() . '/' . $new_name;
                    $new_namespace = trim(ucwords($target_dir, '/'), '/');

                    $files->move($file->getRealPath(), $new_path);

                    $content = $files->get($new_path);

                    if (str_contains($content, '{namespace}')) {
                        $content = str_replace('{namespace}', $new_namespace, $content);
                    }

                    if (str_contains($content, 'Entity')) {
                        $content = str_replace('Entity', $model_name, $content);
                    }

                    $files->put($new_path, $content);
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
