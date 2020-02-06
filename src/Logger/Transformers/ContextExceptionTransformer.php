<?php

declare(strict_types=1);

namespace Shays\Logger\Transformers;

use Throwable;

class ContextExceptionTransformer implements TransfomerInterface
{
	/**
	 * {@inheritDoc}
	 */
	public static function transform($context)
	{
		foreach ($context as $key => $contextValue) {
			if (is_a($contextValue, 'Throwable')) {
				// Override the exception with some useful
				// data which would be added to the log
				/** @type $e Throwable */
				$e = $contextValue;
				$context[$key] = [
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					// TODO: improve stack trace readability
					'trace' => $e->getTraceAsString(),
				];
			}
		}

		return $context;
	}
}
