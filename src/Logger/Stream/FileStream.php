<?php

declare(strict_types=1);

namespace Shays\Logger\Stream;

use Shays\Logger\LogInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileStream implements StreamInterface
{
	/** @var string Path of file */
	private $path;

	/** @var FileSystem */
	private $file;

	/** @var int Lowest log level for file writing */
	private $lowestLevel;

	/** @var bool Whether the file is newly created */
	private $isEmpty;

	/**
	 * FileStream constructor.
	 *
	 * @param string $path Path of file
	 * @param int $lowestLevel Lowest log level to be handled
	 * @throws \Exception
	 */
	public function __construct(string $path, int $lowestLevel = 200)
	{
		$this->path = $path;

		// TODO: add highest level
		$this->lowestLevel = $lowestLevel;
		$this->file = new FileSystem();

		if ($this->file->exists($path) && file_get_contents($path) !== '') {
			$this->isEmpty = false;
			return;
		}

		try {
			$this->file->touch($path);
			$this->isEmpty = true;
		} catch (IOExceptionInterface $e) {
			throw new \Exception("An error occurred while creating your directory at " . $e->getPath());
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function write(string $log): void
	{
		// Append a trailing comma
		if (! $this->isEmpty) {
			// Is JSON? // TODO: improve, this is ugly
			if (is_object(json_decode($log))) {
				$this->file->appendToFile($this->path, ',');
			}
		}

		$this->file->appendToFile($this->path, $log);
	}

	/**
	 * {@inheritDoc}
	 */
	public function shouldWrite(LogInterface $log): bool
	{
		return $log->getLevel() >= $this->lowestLevel;
	}
}
