<?php

use Illuminate\Filesystem\Filesystem;

beforeEach(function () {
    $this->modelName  = 'user';
    $this->actionsDir = '/tmp/app/actions';
    $this->className  = ucfirst($this->modelName);
    $this->modelDir   = "{$this->actionsDir}/{$this->className}";
});

afterAll(function () {
    (new Filesystem())->deleteDirectory('/tmp/app');
});

test('required options', function () {
    $this->artisan("make:crud {$this->modelName}")->assertExitCode(1);
});

test('test success', function () {
    $this->artisan("make:crud {$this->modelName} --actions-dir={$this->actionsDir}")
        ->expectsOutput('User ADR folders and classes created successfuly.')
        ->assertExitCode(0);

    $actions = ['Index', 'Create', 'Store', 'Edit', 'Update', 'Delete'];
    foreach ($actions as $action) {
        assertFilesAreCreated(
            "{$this->modelDir}/{$action}/{$this->className}{$action}Action.php",
            "{$this->modelDir}/{$action}/{$this->className}{$action}Responder.php"
        );
    }
});

test('test relative actions dir path success', function () {
    chdir('/tmp');

    $this->artisan("make:crud {$this->modelName} --actions-dir=./app/actions")
        ->expectsOutput('User ADR folders and classes created successfuly.')
        ->assertExitCode(0);

    $actions = ['Index', 'Create', 'Store', 'Edit', 'Update', 'Delete'];
    foreach ($actions as $action) {
        assertFilesAreCreated(
            "{$this->modelDir}/{$action}/{$this->className}{$action}Action.php",
            "{$this->modelDir}/{$action}/{$this->className}{$action}Responder.php"
        );
    }
});

test('don\'t overwirte existent files', function () {
    $filesystem = new Filesystem();
    $filesystem->ensureDirectoryExists("{$this->modelDir}/Index");
    $filesystem->put("{$this->modelDir}/Index/{$this->className}IndexAction.php", 'content');
    $filesystem->put("{$this->modelDir}/Index/{$this->className}IndexResponder.php", 'content');

    $this->artisan("make:crud {$this->modelName} --actions-dir={$this->actionsDir}")
        ->expectsOutput('User ADR folders and classes created successfuly.')
        ->assertExitCode(0);

    $content1 = $filesystem->get("{$this->modelDir}/Index/{$this->className}IndexAction.php");
    $content2 = $filesystem->get("{$this->modelDir}/Index/{$this->className}IndexResponder.php");

    expect($content1)->toBe('content');
    expect($content2)->toBe('content');
});
