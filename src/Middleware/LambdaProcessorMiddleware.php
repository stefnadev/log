<?php declare(strict_types=1);

namespace Stefna\Logger\Middleware;

use Monolog\LogRecord;
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
		if ($context instanceof Context && $mainLogger instanceof \Monolog\Logger) {
			$mainLogger->pushProcessor(static function (LogRecord $record) use ($context) {
				$recordContext = $record['context'];
				$recordContext['requestId'] = $context->getAwsRequestId();
				return $record->with(context: $recordContext);
			});
		}
		else {
			$mainLogger->warning('No lambda context found!');
		}
		return $handler->handle($request);
	}
}
