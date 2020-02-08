<?php

declare(strict_types=1);

namespace Tests\Mocks;

use Shays\Logger\LogInterface;
use Shays\Logger\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class XmlEncoderMock implements SerializerInterface
{
	/** @var XmlEncoder */
	private $encoder;

	public function __construct()
	{
		$this->encoder = new XmlEncoder();
	}

	public function serialize(LogInterface $log)
	{
		return $this->encoder->encode($log->toArray(), 'xml');
	}
}
