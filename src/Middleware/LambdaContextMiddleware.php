<?php declare(strict_types=1);

namespace Stefna\Logger\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Bref\Context\Context;

final class LambdaContextMiddleware implements MiddlewareInterface
{
	public const LAMBDA_CONTEXT = 'lambda-context';

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$context = $request->getAttribute(self::LAMBDA_CONTEXT);
		if (!$context instanceof Context && isset($_SERVER['LAMBDA_INVOCATION_CONTEXT'])) {
			$lambdaContext = json_decode($_SERVER['LAMBDA_INVOCATION_CONTEXT'], true);
			$request = $request->withAttribute(self::LAMBDA_CONTEXT, new Context(
				$lambdaContext['awsRequestId'],
				$lambdaContext['deadlineMs'],
				$lambdaContext['invokedFunctionArn'],
				$lambdaContext['traceId']
			));
		}
		return $handler->handle($request);
	}
}
