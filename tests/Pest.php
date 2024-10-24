<?php

use Combindma\FacebookPixel\MetaPixel;
use Combindma\FacebookPixel\Tests\TestCase;

uses(TestCase::class)->beforeEach(function () {
    $this->metaPixel = new MetaPixel;
})->in(__DIR__);
