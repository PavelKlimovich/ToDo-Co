<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Services\UserService;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/tasks/list/{type}", name="task_list", methods={"GET"})
     */
    public function listAction(string $type, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->findTaskList($type);

        return $this->render('task/list.html.twig', [
            'tasks' => $task,
            'type'  => $type
        ]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
             /** @var User $user */
            $user = $this->getUser();
            $task->setUser($user);
            $em->persist($task);
            $em->flush();
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list', [
                'type' => 'progress'
            ]);
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request): Response
    {
        $this->denyAccessUnlessGranted('TASK_EDIT', $task);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        $type = $task->isDone() ? 'ended' : 'progress';

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->flush();
            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list', [
                'type' => $type,
            ]);
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }


    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task): Response
    {
        $this->denyAccessUnlessGranted('TASK_TOGGLE', $task);
        $task->toggle(!$task->isDone());
        $message = $task->isDone() ? 'faite' : 'non terminée';
        $type = $task->isDone() ? 'ended' : 'progress';
        $this->doctrine->getManager()->flush();
        $this->addFlash('success', sprintf('La tâche %s a bien été marquée %s.', $task->getTitle(), $message));

        return $this->redirectToRoute('task_list', [
            'type' => $type
        ]);
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task): Response
    {
        $this->denyAccessUnlessGranted('TASK_DELETE', $task);
        $type = $task->isDone() ? 'ended' : 'progress';
        $em = $this->doctrine->getManager();
        $em->remove($task);
        $em->flush();
        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list', [
            'type' => $type
        ]);
    }
}
