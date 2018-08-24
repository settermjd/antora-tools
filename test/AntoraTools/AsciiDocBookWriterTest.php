<?php
declare(strict_types=1);

namespace AntoraToolsTest;

use AntoraTools\AsciiDocBookWriter;
use PHPUnit\Framework\TestCase;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Zend\Expressive\Twig\TwigRenderer;

class AsciiDocBookWriterTest extends TestCase
{
	public function testCanCreateAnAsciiDocBookFile()
	{
		$generatedFile =<<<EOF
= ownCloud Developer Manual
:homepage: https://github.com/owncloud/docs
:toc:
:imagesdir: ./public/
:icons: font
:icon-set: octicon
%s, %s

include::modules/developer_manual/pages/core/acceptance-tests.adoc[]

include::modules/developer_manual/pages/core/ui-testing.adoc[]

include::modules/developer_manual/pages/core/configfile.adoc[]

include::modules/developer_manual/pages/core/externalapi.adoc[]


EOF;

		$renderer = new TwigRenderer(
			new Twig_Environment(
				new Twig_Loader_Filesystem(__DIR__ . '/../../src/AntoraTools/templates'),
				[]
			)
		);

		$options = [
			'title' => 'ownCloud Developer Manual',
			'homePage' => 'https://github.com/owncloud/docs',
			'imagesDir' => './public/',
			'version' => '0.0.1',
			'buildDate' => '2018-08-24',
			'fileBasePath' => 'modules/developer_manual/pages/',
		];
		
		$filesList = [
			'core/acceptance-tests.adoc',
			'core/ui-testing.adoc',
			'core/configfile.adoc',
			'core/externalapi.adoc',
		];

		$fileWriter = new AsciiDocBookWriter($renderer, $options);

		$this->assertEquals(
			sprintf($generatedFile, "0.0.1", date('Y-m-d')),
			$fileWriter->generateBookContents($filesList)
		);
	}
}
