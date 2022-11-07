<?php declare(strict_types=1);

namespace Stefna\Logger\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Stefna\Logger\Logger;

final class RequestIdProcessorMiddleware implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$requestId = $request->getAttribute(Tracer::REQUEST_ID);
		$mainLogger = Logger::getManager()->getMainLogger();
		if ($requestId) {
			if ($mainLogger instanceof \Monolog\Logger) {
				$mainLogger->pushProcessor(static function ($record) use ($requestId) {
					$record['context']['requestId'] = $requestId;
					return $record;
				});
			}
		}
		else {
			$mainLogger->warning('No request id found!');
		}
		return $handler->handle($request);
	}
}
