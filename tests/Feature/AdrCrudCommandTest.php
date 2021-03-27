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
    $this->artisan("make:crud {$this->model_name} --actions_dir={$this->actions_dir}")
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
