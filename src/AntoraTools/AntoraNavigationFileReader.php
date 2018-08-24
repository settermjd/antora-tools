<?php
declare(strict_types=1);

namespace AntoraTools;

class AntoraNavigationFileReader
{
	const INC_FILE_REGEX ='/(?<=:)(.*\.adoc)(?=\[)/m';

	/**
	 * @var string
	 */
	private $fileContents;
	private $filesList = [];

	public function __construct(string $fileContents)
	{
		$this->fileContents = $fileContents;
	}

	/**
	 * Parses the contents of the Antora nav.adoc navigation file
	 * supplied to the constructor, and returns all of the linked files
	 * that it contains.
	 *
	 * @see https://docs.antora.org/antora/1.0/navigation/list-structures/
	 * @return array
	 */
	public function parseNavigationFile() : array
	{
		preg_match_all(self::INC_FILE_REGEX, $this->fileContents, $matches, PREG_SET_ORDER, 0);

		if (count($matches) > 0) {
			array_walk($matches, function($item) {
				if (count($item) > 0) {
					$this->filesList[] = $item[0];
				}
			});
		}

		return $this->filesList;
	}
}
