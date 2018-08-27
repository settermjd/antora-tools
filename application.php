#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Zend\Expressive\Twig\TwigRenderer;

$application = new Application();

$renderer = new TwigRenderer(
	new \Twig_Environment(
		new \Twig_Loader_Filesystem(__DIR__ . '/src/AntoraTools/templates'),
		[]
	)
);

// ... register commands
$application->add(new \AntoraTools\Command\GenerateAsciiDocBookFileCommand($renderer));

$application->run();
