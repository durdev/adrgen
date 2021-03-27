<?php

use Illuminate\Filesystem\Filesystem;

uses(Tests\TestCase::class)->in('Feature');

function assertFilesAreCreated($action_path, $responder_path)
{
    $filesystem = new Filesystem();

    expect($filesystem->exists($action_path))->toBeTrue();
    expect($filesystem->exists($responder_path))->toBeTrue();
}
