<?php

namespace App\Controller;

use App\Entity\ParsedStatus;
use App\Entity\ParsedUrl;
use App\Form\ParseProductType;
use App\Service\UrlCollector;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParseProductController extends AbstractController
{
    public function __construct(
        private readonly UrlCollector $urlCollector,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('/parse/product', name: 'app_parse_product')]
    public function index(Request $request): Response
    {
        $form = $this
            ->createForm(ParseProductType::class)
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Url submitted to parse.');

            $this->urlCollector->addUrl($form->getData());

            return $this->redirectToRoute('app_parse_product_list');
        }

        return $this->render('parse_product/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/parse/product/list', name: 'app_parse_product_list')]
    public function list(): Response
    {
        $parsedRepo = $this->entityManager->getRepository(ParsedUrl::class);

        /** @var ParsedUrl[] $urls */
        $urls = $parsedRepo->createQueryBuilder('url')
            ->select('url')
            ->andWhere('url.status IN (:shownStatuses)')
            ->setParameter('shownStatuses', [ParsedStatus::PENDING, ParsedStatus::ERROR])
            ->addOrderBy('url.dateTime', Criteria::DESC)
            ->getQuery()->toIterable()
            ;

        return $this->render('parse_product/list.html.twig', [
            'urls' => $urls,
        ]);
    }
}
