<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Form\FeedSourceFormType;
use App\Service\FeedReaderService;
use App\Repository\FeedRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FeedCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FeedController extends AbstractController
{

    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @param FeedRepository $feedRepository
     *
     * @return Response
     */
    public function index(FeedRepository $feedRepository)
    {
        $feedReaderService = new FeedReaderService();
        $feeds = $feedReaderService->getFeedData($feedRepository);

        return $this->render('feed/feedIndex.html.twig', [
            'feeds' => $feeds
        ]);
    }

    /**
     * @Route("/feeds/add", name="add_feed_source")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function addFeedSource(Request $request)
    {
        $form = $this->createForm(FeedSourceFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->checkIfLinkExists($data['url'])) {

                // TODO: Prüfe ob Feed/Kategorie nicht schon existiert
                // TODO: In Übersicht darstellen ob Daten gezogen werden können

                $feed = new Feed();
                $feed->setTitle($data['title']);
                $feed->setUrl($data['url']);
                $feed->setCategory($data['category']);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($feed);
                $entityManager->flush();

                return $this->redirectToRoute('source_index');
            } else {
                echo "URL is invalid";
            }
        }

        return $this->render('feed/addFeed.html.twig', [
            'feedSourceForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/feeds", name="source_index", methods={"GET"})
     *
     * @param FeedCategoryRepository $categoryRepository
     * @param FeedRepository $feeds
     *
     * @return Response
     */
    public function feedSourceIndex(FeedCategoryRepository $categoryRepository, FeedRepository $feeds)
    {
        return $this->render('feed/sourceIndex.html.twig', [
            'feeds' => $feeds->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/feeds/edit/{id}", name="edit_feed")
     *
     * @param FeedRepository $feedRepository
     * @param FeedCategoryRepository $categoryRepository
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse|Response
     */
    public function editFeed(FeedRepository $feedRepository, Request $request, $id) {
        $feed = $feedRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(FeedSourceFormType::class, $feed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($feed);
            $entityManager->flush();

            return $this->redirectToRoute('feedSourceIndex');
        }

        return $this->render('feed/addFeed.html.twig', [
            'feedSourceForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/feeds/delete/{id}")
     * @param $id
     *
     * @return void
     */
    public function deleteFeed($id) :void
    {
        $feedSource = $this->getDoctrine()->getRepository(Feed::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($feedSource);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }

    /**
     * Checks the HTTP-Statuscode by the given URL
     *
     * @param $modifiedUrl string
     * @param $originUrl string
     * @param $logNonExistentLink boolean
     *
     * @return bool
     */
    public function checkIfLinkExists($url): bool
    {
        $curlResource = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER,
            true,
            CURLOPT_NOBODY,
            true
        );

        curl_setopt_array($curlResource, $options);
        curl_exec($curlResource);
        $status = curl_getinfo($curlResource, CURLINFO_HTTP_CODE);

        if ($status == 200) {
            return true;
        }
        curl_close($curlResource);
    }

    public function importFeedSources()
    {
        // code...
    }
}
