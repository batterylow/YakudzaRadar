<?php

namespace App\Controller;

use App\Service\Telegram\TelegramService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Основной контроллер
 */
class MainController extends AbstractController
{

    /**
     * Обработчик сообщений от ТГ
     *
     * @return Response
     */
    #[
        Route('/{token}', name: 'hook', methods: ['GET', 'POST']),
    ]
    public function hook(
        string $token,
        TelegramService $telegramService,
        ParameterBagInterface $parameterBag,
    ): Response
    {

        if($token != $parameterBag->get('telegram.api.key')){
            throw new \Exception('Неверный токен');
        }

        $telegramService->provider->handleRequest();

        return new Response();
    }

    #[
        Route('/file/{fileId}', name: 'get_file_url', methods: ['GET']),
    ]
    public function getFileUrl(
        string $fileId,
        TelegramService $telegramService
    ): Response
    {
        $filePath = $telegramService->downloadFile($fileId);
        return new BinaryFileResponse($filePath);
    }
}