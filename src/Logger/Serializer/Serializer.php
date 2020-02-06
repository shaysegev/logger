<?php

declare(strict_types=1);

namespace Shays\Logger\Serializer;

use Shays\Logger\Formatters\Format;
use Shays\Logger\LogInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class Serializer implements SerializerInterface
{
	/** @var SymfonySerializer */
	private $serializer;

	/** @var string Serializer format */
	private $format = Format::JSON;

	/**
	 * Logger library native serializer
	 *
	 * @param string $format
	 */
	public function __construct(string $format)
	{
		$this->format = $format;

		$encoders = [new JsonEncoder()];
		$normalizers = [new ObjectNormalizer()];

		$this->serializer = new SymfonySerializer($normalizers, $encoders);
	}

	/**
	 * {@inheritDoc}
	 */
	public function serialize(LogInterface $log)
	{
		return $this->serializer->serialize($log->serialize(), $this->format);
	}
}
