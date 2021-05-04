<?php

use Illuminate\Filesystem\Filesystem;

beforeEach(function () {
    $this->model_name  = 'user';
    $this->actions_dir = '/tmp/app/actions';
    $this->class_name  = ucfirst($this->model_name);
    $this->model_dir   = "{$this->actions_dir}/{$this->class_name}";
});

afterAll(function () {
    (new Filesystem())->deleteDirectory('/tmp/app');
});

test('required options', function () {
    $this->artisan("make:crud {$this->model_name}")->assertExitCode(1);
});

test('test success', function () {
    $this->artisan("make:crud {$this->model_name} --actions-dir={$this->actions_dir}")
        ->expectsOutput('User ADR folders and classes created successfuly.')
        ->assertExitCode(0);

    $actions = ['Index', 'Create', 'Store', 'Edit', 'Update', 'Delete'];
    foreach ($actions as $action) {
        assertFilesAreCreated(
            "{$this->model_dir}/{$action}/{$this->class_name}{$action}Action.php",
            "{$this->model_dir}/{$action}/{$this->class_name}{$action}Responder.php"
        );
    }
});

test('don\'t overwirte existent files', function () {
    $filesystem = new Filesystem();
    $filesystem->ensureDirectoryExists("{$this->model_dir}/Index");
    $filesystem->put("{$this->model_dir}/Index/{$this->class_name}IndexAction.php", 'content');
    $filesystem->put("{$this->model_dir}/Index/{$this->class_name}IndexResponder.php", 'content');

    $this->artisan("make:crud {$this->model_name} --actions-dir={$this->actions_dir}")
        ->expectsOutput('User ADR folders and classes created successfuly.')
        ->assertExitCode(0);

    $content1 = $filesystem->get("{$this->model_dir}/Index/{$this->class_name}IndexAction.php");
    $content2 = $filesystem->get("{$this->model_dir}/Index/{$this->class_name}IndexResponder.php");

    expect($content1)->toBe('content');
    expect($content2)->toBe('content');
});
