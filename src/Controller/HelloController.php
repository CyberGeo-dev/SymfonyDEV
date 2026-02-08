<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;

class HelloController extends AbstractController
{
    protected $logger;
    private $title = "Hello ";

    /*
     * @Route("/hello", name="hello")
     */

    /**
     * @param $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function test($name)
    {
        $this->title .= $name;

        return $this->render(
            'View/item/hello.html.twig',
            ['title' => $this->title]
        );
    }
}
