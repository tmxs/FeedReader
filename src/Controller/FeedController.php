<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Entity\Post;
use App\Entity\FeedCategory;
use App\Service\FeedReaderService;
use App\Form\FeedSourceFormType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class FeedController extends AbstractController
{

    /**
     * @Route("/", name="rssFeedOverview", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager)
    {
        $feedReaderService = new FeedReaderService();
        $feeds = $feedReaderService->getFeedData($entityManager);

        return $this->render('rssfeed/feedIndex.html.twig', [
            'feeds'  => $feeds
        ]);
    }

    /**
     * @Route("/feeds/add", name="addFeedSource")
     *
     */
    public function addFeedSource(EntityManagerInterface $entityManager, Request $request)
    {
        $form = $this->createForm(FeedSourceFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->checkIfLinkExists($data['url'])) {
                $feed = new Feed();
                $feed->setTitle($data['title']);
                //TODO: erst URL speichern, wenn curl 200 zurück gibt und das format xml ist
                $feed->setUrl($data['url']);
                $feed->setCategory($data['category']);

                $entityManager->persist($feed);
                $entityManager->flush();

                return $this->redirectToRoute('feedSourceIndex');
            } else {
                echo "URL is invalid";
            }
        }

        return $this->render('rssfeed/addFeed.html.twig', [
            'feedSourceForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/feeds/sources", name="feedSourceIndex", methods={"GET"})
     *
     */
    public function feedSourceIndex(EntityManagerInterface $entityManager)
    {
      $sources = $entityManager->getRepository(Feed::class)->findAll();
      $sourceCategories = $entityManager->getRepository(FeedCategory::class)->findall();
      $feedSources = array();

      for ($i=0; $i < sizeof($sourceCategories); $i++) {
          $feedSources[]['categoryName'] = $sourceCategories[$i]->getName();
          foreach ($sources AS $source) {
            if ($source->getCategory()->getId() == $sourceCategories[$i]->getId()) {
              $feedSources[$i]['title'][] = $source->getTitle();
            }
          }
      }

      return $this->render('rssfeed/sourceIndex.html.twig', [
        'sources' => $sources,
        'sourceCategories' => $sourceCategories,
        'feedSources' => $feedSources,
      ]);
    }

    /**
     * @Route("/feeds/edit/{id}", name="editFeedSource")
     *
     */
    public function editFeedSource(EntityManagerInterface $entityManager, Request $request, $id)
    {
        $feedSource = $this->getDoctrine()->getRepository(Feed::class)->find($id);
        $sourceCategories = $entityManager->getRepository(FeedCategory::class)->findall();

        $form = $this->createForm(FeedSourceFormType::class, $feedSource);
        $form->handleRequest($request);
        // $data = $form->getData();
        // var_dump($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // $feedSource->setCategory($data->category);
            // $entityManager->persist($feedSource);
            $entityManager->flush();

            return $this->redirectToRoute('feedSourceIndex');
        }

        return $this->render('rssfeed/addFeed.html.twig', [
            'feedSourceForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/feeds/delete/{id}")
     */
    public function deleteFeedSource(Request $request, $id)
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
    public function checkIfLinkExists($url) : bool
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
            CURLOPT_HEADER, true,
            CURLOPT_NOBODY, true
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