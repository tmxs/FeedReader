<?php


namespace App\Controller;


use App\Entity\Feed;
use App\Entity\FeedCategory;
use App\Form\CategoryFormType;
use App\Form\FeedSourceFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FeedCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/add", name="addCategory")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     *
     * @return RedirectResponse|Response
     */
    public function addCategory(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(CategoryFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $category = new FeedCategory();
            $category->setName($data['name']);

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('feedSourceIndex');
        }

        return $this->render('feed/addCategory.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/edit/{id}", name="editCategory")
     *
     * @param FeedCategoryRepository $category
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse|Response
     */
    public function editCategory(FeedCategoryRepository $category, Request $request, $id)
    {
        $form = $this->createForm(CategoryFormType::class, $category->findOneBy(['id' =>  $id]));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('feedSourceIndex');
        }

        return $this->render('feed/addCategory.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/delete/{id}")
     *
     * @param $id
     * @param FeedCategoryRepository $category
     */
    public function deleteCategory(int $id, FeedCategoryRepository $category)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category->findOneBy(['id' =>  $id]));
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }

}
