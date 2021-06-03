<?php declare(strict_types=1);

namespace Stefna\Logger\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Bref\Context\Context;
use Stefna\Logger\Logger;

final class LambdaProcessorMiddleware implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$context = $request->getAttribute(LambdaContextMiddleware::LAMBDA_CONTEXT);
		$mainLogger = Logger::getManager()->getMainLogger();
		if ($context instanceof Context) {
			$mainLogger->pushProcessor(static function ($record) use ($context) {
				$record['context']['requestId'] = $context->getAwsRequestId();
				return $record;
			});
		}
		else {
			$mainLogger->warning('No lambda context found!');
		}
		return $handler->handle($request);
	}
}
