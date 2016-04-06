<?php

namespace OroB2B\Bundle\FrontendBundle\Controller;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\FlattenException as DeprecatedFlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

use FOS\RestBundle\Controller\ExceptionController as BaseExceptionController;

use Oro\Bundle\LayoutBundle\Annotation\Layout;

class ExceptionController extends BaseExceptionController
{
    /**
     * @Layout(vars={"exception_thrown"})
     * @param Request $request
     * @param FlattenException|DeprecatedFlattenException $exception
     * @param DebugLoggerInterface|null $logger
     * @return Response
     */
    public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null)
    {
        if (!$this->container->get('orob2b_frontend.request.frontend_helper')->isFrontendRequest($request)) {
            return parent::showAction($request, $exception, $logger);
        }
        $code = $this->getStatusCode($exception);
        return [
//            'theme' => 'default',
            'exception_thrown' => true,
            'data' => [
                'status_code' => $code,
                'status_text' => $this->getStatusText($code),
            ],
        ];
    }

    /**
     * @param $code
     * @return string
     */
    protected function getStatusText($code)
    {
        return array_key_exists($code, Response::$statusTexts) ? Response::$statusTexts[$code] : "error";
    }
}
