<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{
    /**
     * @Route("/", name="to_do_list")
     */
    public function index(TaskRepository $taskRepository)
    {
        $tasks = $taskRepository->findBy([], ['id' => 'DESC']);
        return $this->render('index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/create", name="create_task", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $title = trim($request->request->get('title'));
        if (empty($title))
        return $this->redirectToRoute('to_do_list');

        $task = new Task();
        $task->setTitle($title);

        $manager->persist($task);
        $manager->flush();

        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/switch-status/{id}", name="switch_status")
     */
    public function switchStatus($id, EntityManagerInterface $manager, TaskRepository $taskRepository)
    {
        $task = $taskRepository->find($id);

        $task->setStatus(! $task->getStatus());
        $manager->flush();
        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/delete/{id}", name="task_delete")
     */
    public function delete($id, EntityManagerInterface $manager, TaskRepository $taskRepository)
    {
        $task = $taskRepository->find($id);

        $manager->remove($task);
        $manager->flush();
        return $this->redirectToRoute('to_do_list');
    }
}
