<?php
declare(strict_types=1);

namespace AntoraTools;

use Zend\Expressive\Template\TemplateRendererInterface;

class AsciiDocBookWriter
{
    const BOOK_TEMPLATE = 'book.adoc.twig';

    /**
     * @var $templateRenderer TemplateRendererInterface
     */
    private $templateRenderer;

    /**
     * @var array core book.adoc file options
     */
    private $options;

    /**
     * AsciiDocBookWriter constructor.
     *
     * @param TemplateRendererInterface $templateRenderer
     * @param array $options
     */
    public function __construct(TemplateRendererInterface $templateRenderer, array $options)
    {
        $this->templateRenderer = $templateRenderer;
        $this->options = $options;
    }

    /**
     * Generate the book's contents
     *
     * @param array $filesList The list of files to include in the book
     * @return string
     */
    public function generateBookContents(array $filesList) : string
    {
        return $this->templateRenderer->render(self::BOOK_TEMPLATE, [
            'title' => $this->options['title'],
            'imagesdir' => $this->options['imagesDir'],
            'version' => $this->options['version'],
            'buildDate' => $this->options['buildDate'],
            'fileBasePath' => $this->options['fileBasePath'],
            'files' => $filesList,
        ]);
    }
}
