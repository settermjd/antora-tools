<?php

namespace AntoraToolsTest\Command;


use AntoraTools\Command\GenerateAsciiDocBookFileCommand;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\{
	Application,
	Tester\CommandTester
};
use Twig_Environment;
use Twig_Loader_Filesystem;
use Zend\Expressive\Twig\TwigRenderer;

class GenerateAsciiDocBookFileCommandTest extends TestCase
{
	private $fileSystem;
	private $navFileBasePath;

	public function setUp()
	{
		$content = <<<EOF
* xref:index.adoc[User Manual]
** xref:whats_new.adoc[What's New]
** xref:files/webgui/overview.adoc[The WebUI]
*** xref:webinterface.adoc[Web Interface]
*** xref:userpreferences.adoc[User Preferences]
*** xref:files/webgui/navigating.adoc[Navigating the WebUI]
*** xref:files/webgui/comments.adoc[Comments]
*** xref:files/webgui/custom_groups.adoc[Custom Groups]
EOF;
		$directory = [
			'var' => [
				'www' => [
					'ownCloud' => [
						'modules' => [
							'ROOT' => [
								'pages' => []
							],
							'developer_manual' => [
								'pages' => []
							],
							'user_manual' => [
								'pages' => []
							],
						]
					]
				]
			]
		];

		// setup and cache the virtual file system
		$this->fileSystem = vfsStream::setup('/', null, $directory);
		$this->navFileBasePath = 'var/www/ownCloud/modules/developer_manual';

		vfsStream::newFile('nav.adoc')
			->at($this->fileSystem->getChild($this->navFileBasePath))
			->setContent($content);
	}

	public function testExecute()
	{
		$renderer = new TwigRenderer(
			new Twig_Environment(
				new Twig_Loader_Filesystem(__DIR__ . '/../../../src/AntoraTools/templates'),
				[]
			)
		);

		$application = new Application();
		$application->add(new GenerateAsciiDocBookFileCommand($renderer));

		$command = $application->find('antora:create-asciidoc-book-file');
		$commandTester = new CommandTester($command);

		$navFile = 'nav.adoc';
		$bookFile = 'book.adoc';
		$expectedFileContent = <<<EOF
= ownCloud Developer Manual
:homepage: https://github.com/owncloud/docs
:toc:
:imagesdir: ./public/
:icons: font
:icon-set: octicon
0.0.1, %s

include::modules/developer_manual/pages/index.adoc[]

include::modules/developer_manual/pages/whats_new.adoc[]

include::modules/developer_manual/pages/files/webgui/overview.adoc[]

include::modules/developer_manual/pages/webinterface.adoc[]

include::modules/developer_manual/pages/userpreferences.adoc[]

include::modules/developer_manual/pages/files/webgui/navigating.adoc[]

include::modules/developer_manual/pages/files/webgui/comments.adoc[]

include::modules/developer_manual/pages/files/webgui/custom_groups.adoc[]


EOF;

		$commandTester->execute([
			'command'  => $command->getName(),
			'--book-file' => vfsStream::url('var/www/ownCloud/') . $bookFile,
			'--nav-file' => vfsStream::url($this->navFileBasePath) . '/' . $navFile,
			'--manual-name' => 'ownCloud Developer Manual',
			'--file-version' => '0.0.1',
		]);

		// the output of the command in the console
		$output = $commandTester->getDisplay();

		$this->assertContains(
			sprintf('Reading Antora navigation file: %s.', vfsStream::url($this->navFileBasePath) . '/' . $navFile),
			$output
		);

		$this->assertContains(
			sprintf('Generating AsciiDoc file: %s', vfsStream::url('var/www/ownCloud/') . $bookFile),
			$output
		);

		$this->assertFileExists(vfsStream::url('var/www/ownCloud/') . $bookFile);
		$this->assertEquals(sprintf($expectedFileContent, date('Y-m-d')), file_get_contents(vfsStream::url('var/www/ownCloud/') . $bookFile));
	}
}
