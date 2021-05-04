<?php

use Illuminate\Filesystem\Filesystem;

uses(Tests\TestCase::class)->in('Feature');

function assertFilesAreCreated($actionPath, $responderPath)
{
    $filesystem = new Filesystem();

    expect($filesystem->exists($actionPath))->toBeTrue();
    expect($filesystem->exists($responderPath))->toBeTrue();
}
