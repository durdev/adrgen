<?php

namespace App\Commands;

use Illuminate\Filesystem\Filesystem;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;
use Symfony\Component\Finder\Finder;

class AdrCrudCommand extends Command
{

    private $fileManager;
    private $targetDir;
    private $modelName;

    public function __construct(Filesystem $fileManager)
    {
        parent::__construct();

        $this->fileManager = $fileManager;
    }

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
    public function handle()
    {
        try {
            $this->parseArgsOpts();
        } catch (RuntimeException $runtimeException) {
            $this->comment($runtimeException->getMessage(), $runtimeException->getCode());
            return $runtimeException->getCode();
        }

        if ($this->directoryExists($this->targetDir)) {
            $this->onlyWriteNewFiles();
        } else {
            $this->createDirAndFiles();
        }

        $this->info('User ADR folders and classes created successfuly.');
    }

    private function onlyWriteNewFiles()
    {
        foreach($this->fileManager->allFiles($this->targetDir) as $file) {
            if ($this->fileDontExists($file)) {
                $renamedFile = $this->renameFilename($file);
                $this->replaceDefaultContent($renamedFile);
            }
        }
    }

    private function createDirAndFiles()
    {
        $this->fileManager->copyDirectory(__DIR__ . '/../../stubs/Entity', $this->targetDir);

        foreach($this->fileManager->allFiles($this->targetDir) as $file) {
            $renamedFile = $this->renameFilename($file);
            $this->replaceDefaultContent($renamedFile);
        }
    }

    private function parseArgsOpts()
    {
        $this->modelName = ucfirst($this->argument('model'));
        $this->targetDir = $this->validateDirOption() . '/' . $this->modelName;
    }

    private function directoryExists($dir)
    {
        return $this->fileManager->exists($dir);
    }

    private function fileDontExists($file)
    {
        return !$this->fileManager->exists($file->getPathname());
    }

    private function renameFilename($file)
    {
        $modelName    = ucfirst($this->argument('model'));
        $newName      = str_replace('Entity', $modelName, $file->getFilename());
        $newPath      = $file->getPath() . '/' . $newName;

        $this->fileManager->move($file->getRealPath(), $newPath);

        $finder = new Finder();
        $finder->in($file->getPath())->files()->name($newName);

        $renamedFile = null;

        foreach ($finder as $file) {
            if ($file->getFilename() == $newName) {
                $renamedFile = $file;
            }
        }

        return $renamedFile;
    }

    private function replaceDefaultContent($renamedFile)
    {
        $content = $this->fileManager->get($renamedFile);

        if (str_contains($content, '{namespace}')) {
            $content = $this->replaceNamespace(trim(ucwords($this->targetDir, '/'), '/'), $content);
        }

        if (str_contains($content, 'Entity')) {
            $content = $this->replaceEntity($content);
        }

        $this->fileManager->put($renamedFile, $content);
    }

    private function replaceNamespace($namespace, $content)
    {
        return str_replace('{namespace}', $namespace, $content);
    }

    private function replaceEntity($content)
    {
        return str_replace('Entity', $this->modelName, $content);
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
