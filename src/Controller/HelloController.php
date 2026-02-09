<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    protected LoggerInterface $logger;
    private string $title = 'Hello ';

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route(
        '/test/{name}',
        name: 'test',
        methods: ['GET', 'POST'],
        host: 'localhost',
        schemes: ['http', 'https'],
        defaults: ['name' => 'world']
    )]
    public function test(string $name): Response
    {
        $this->title .= $name;

        return $this->render(
            view: 'View/item/hello.html.twig',
            parameters: ['title' => $this->title]
        );
    }
}
