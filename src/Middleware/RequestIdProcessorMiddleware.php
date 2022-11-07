<?php declare(strict_types=1);

namespace Stefna\Logger\Middleware;

use Monolog\LogRecord;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stefna\Logger\Logger;

final class RequestIdProcessorMiddleware implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$requestId = $request->getAttribute(Tracer::REQUEST_ID);
		$mainLogger = Logger::getManager()->getMainLogger();
		if ($requestId && $mainLogger instanceof \Monolog\Logger) {
			$mainLogger->pushProcessor(static function (LogRecord $record) use ($requestId) {
				$recordContext = $record->context;
				$recordContext['requestId'] = $requestId;
				return $record->with(context: $recordContext);
			});
		}
		else {
			$mainLogger->warning('No request id found!');
		}
		return $handler->handle($request);
	}
}
