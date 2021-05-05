<?php

use Illuminate\Filesystem\Filesystem;

afterAll(function () {
    (new Filesystem())->deleteDirectory('/tmp/User');
});

test('required options', function () {
    $this->artisan("make user")->assertExitCode(1);
});

test('test directory does not exists error', function () {
    $this->artisan("make user --dir=/tmp/app/actions")->assertExitCode(2);
});

test('test success', function () {
    $this->artisan("make user --dir=/tmp")
        ->expectsOutput('User ADR folders and classes created successfuly.')
        ->assertExitCode(0);

    foreach (['Index', 'Create', 'Store', 'Edit', 'Update', 'Delete'] as $action) {
        assertFilesAreCreated(
            "/tmp/User/{$action}/User{$action}Action.php",
            "/tmp/User/{$action}/User{$action}Responder.php"
        );
    }
});

test('test relative actions dir path success', function () {
    chdir('/tmp');
    $modelDir = realpath('.') . '/User';

    $this->artisan("make user --dir=.")
        ->expectsOutput('User ADR folders and classes created successfuly.')
        ->assertExitCode(0);

    foreach (['Index', 'Create', 'Store', 'Edit', 'Update', 'Delete'] as $action) {
        assertFilesAreCreated(
            "{$modelDir}/{$action}/User{$action}Action.php",
            "{$modelDir}/{$action}/User{$action}Responder.php"
        );
    }
});

test('don\'t overwirte existent files', function () {
    $filesystem = new Filesystem();
    $filesystem->ensureDirectoryExists("/tmp/User/Index");
    $filesystem->put("/tmp/User/Index/UserIndexAction.php", 'content');
    $filesystem->put("/tmp/User/Index/UserIndexResponder.php", 'content');

    $this->artisan("make user --dir=/tmp")
        ->expectsOutput('User ADR folders and classes created successfuly.')
        ->assertExitCode(0);

    $content1 = $filesystem->get("/tmp/User/Index/UserIndexAction.php");
    $content2 = $filesystem->get("/tmp/User/Index/UserIndexResponder.php");

    expect($content1)->toBe('content');
    expect($content2)->toBe('content');
});
