<?php
declare(strict_types=1);

namespace AntoraTools\Command;

use AntoraTools\AntoraNavigationFileReader;
use AntoraTools\AsciiDocBookWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateAsciiDocBookFileCommand extends Command
{
    const DEFAULT_BOOK_FILE = 'book.adoc';
    const DEFAULT_NAV_FILE = 'nav.adoc';
    const DEFAULT_IMAGES_DIR = './public/';

    private $renderer;

    /**
     * GenerateAsciiDocBookFileCommand constructor.
     * @param $renderer
     */
    public function __construct($renderer)
    {
        parent::__construct();

        $this->renderer = $renderer;
    }

    protected function configure()
    {
        $this
            // Core command configuration
            ->setName('antora:create-asciidoc-book-file')
            ->setDescription('Creates an AsciiDoc book configuration file.')
            ->setHelp(
                'This command allows you to create an AsciiDoc book configuration file from an Antora navigation file'
            )

            // Command options & arguments
            ->addOption(
                'nav-file',
                null,
                InputOption::VALUE_REQUIRED,
                'The Antora navigation file to read',
                self::DEFAULT_NAV_FILE
            )
            ->addOption(
                'book-file',
                null,
                InputOption::VALUE_REQUIRED,
                'The name of the file to write the AsciiDoc book file to.',
                self::DEFAULT_BOOK_FILE
            )
            ->addOption(
                'manual-name',
                null,
                InputOption::VALUE_REQUIRED,
                'The ownCloud manual\'s name'
            )
            ->addOption(
                'file-version',
                null,
                InputOption::VALUE_REQUIRED,
                'The file version.'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $navFile = $input->getOption('nav-file');
        $bookFile = $input->getOption('book-file');
        $options = [
            'buildDate' => date('Y-m-d'),
            'fileBasePath' => $this->getFileBasepath($navFile),
            'imagesDir' => self::DEFAULT_IMAGES_DIR,
            'title' => $input->getOption('manual-name'),
            'version' => $input->getOption('file-version'),
        ];
        $reader = new AntoraNavigationFileReader(file_get_contents($navFile));

        $output->writeln(sprintf('Reading Antora navigation file: %s.', $navFile));

        file_put_contents(
            $bookFile,
            (new AsciiDocBookWriter($this->renderer, $options))
                ->generateBookContents($reader->parseNavigationFile())
        );

        $output->writeln(sprintf('Generating AsciiDoc file: %s', $bookFile));
    }

    /**
     * Retrieve the directory path to the module's pages directory
     *
     * @param string $navFile
     * @return string
     */
    protected function getFileBasepath(string $navFile): string
    {
        return sprintf('%s/pages/', substr(dirname($navFile), strpos(dirname($navFile), 'modules')));
    }
}
