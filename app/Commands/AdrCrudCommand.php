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
    protected $signature = 'make
        {model : The name of the entity}
        {--dir= : Your actions classes folder}';

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
        $dir       = null;
        $modelName = ucfirst($this->argument('model'));

        try {
            $dir = $this->validateDirOption();
        } catch (RuntimeException $runtimeException) {
            $this->comment($runtimeException->getMessage(), $runtimeException->getCode());
            return $runtimeException->getCode();
        }

        $targetDir = $dir . '/' . $modelName;

        $this->comment(($targetDir . ' existe? ') . ($files->exists($targetDir) ? 'sim' : 'nao'));

        if ($files->exists($targetDir)) {
            $this->line('Iterando sobre os arquivos do diretorio ' . $targetDir);

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
        } else {
            $this->line('criando diretorio ' . $targetDir);
            $files->makeDirectory($targetDir, 0777);
            $this->line('copiando arquivos de ' . __DIR__ . '/../../stubs/Entity para ' . $targetDir);
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

        $this->info('User ADR folders and classes created successfuly.');
    }

    private function validateDirOption()
    {
        if($this->option('dir') == null) {
            throw new RuntimeException('Target directory is required.', 1);
        }

        $dir = realpath($this->option('dir'));

        if($dir === false) {
            throw new RuntimeException('Target directory does not exists.', 2);
        }

        return $dir;
    }

}
