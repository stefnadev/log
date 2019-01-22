<?php declare(strict_types=1);

namespace Stefna\Logger\Middleware;

use Stefna\Logger\NewRelic;
use Polus\Adr\Interfaces\ActionInterface;
use Polus\Router\RouteInterface;
use Polus\Router\RouterDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PolusNewRelicTransactionNamingMiddleware implements MiddlewareInterface
{
	/**
	 * @inheritdoc
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if (NewRelic::enabled()) {
			$name = $this->findTransactionNameFromRequest($request);
			if ($name) {
				NewRelic::nameTransaction($name);
			}
		}
		return $handler->handle($request);
	}

	private function findTransactionNameFromRequest(ServerRequestInterface $request): ?string
	{
		$route = $request->getAttribute('route');
		if ($route instanceof RouteInterface && $route->getStatus() === RouterDispatcherInterface::FOUND) {
			$handler = $route->getHandler();
			if ($handler instanceof ActionInterface) {
				$domain = $handler->getDomain();
				return NewRelic::cleanClassName($domain, ['Web_', 'Domain_']);
			}
			return NewRelic::cleanClassName($handler, ['Web_', 'Action_']);
		}
		return null;
	}
}
