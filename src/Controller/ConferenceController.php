<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;
use App\Repository\CommentRepository;
use App\Entity\Conference;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ConferenceRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ConferenceController extends AbstractController
{
     private $twig;

     public function __construct(Environment $twig)
     {
        $this->twig = $twig;
     }

    /**
     * @Route("/conference", name="homepage")
     */
    public function index(ConferenceRepository $conferenceRepository)
    {
        return new Response($this->twig->render('conference/index.html.twig'));
    }

    /**
    * @Route("/conference/{slug}", name="conference")
    */
    public function show(Request $request, ConferenceRepository $conferenceRepository, Conference $conference, CommentRepository $commentRepository)
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]));

    }
}
