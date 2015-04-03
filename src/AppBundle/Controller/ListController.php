<?php

namespace AppBundle\Controller;

use DavidBadura\Taskwarrior\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author David Badura <d.a.badura@gmail.com>
 *
 * @Route("/list")
 */
class ListController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function listAction()
    {
        return $this->filterTasks('status:pending');
    }

    /**
     * @Route("/all", name="list_all")
     */
    public function allAction()
    {
        return $this->filterTasks();
    }

    /**
     * @Route("/tag/{tag}", name="task_tag")
     */
    public function tagAction($tag)
    {
        return $this->filterTasks('+' . $tag . ' status:pending');
    }

    /**
     * @Route("/project/{project}", name="task_project")
     */
    public function projectAction($project)
    {
        return $this->filterTasks('project:' . $project . ' status:pending');
    }

    /**
     * @Route("/search", name="list_search")
     */
    public function searchAction(Request $request)
    {
        return $this->filterTasks($request->get('q', ''));
    }

    /**
     * @param $filter
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function filterTasks($filter = '')
    {
        $form = $this->get('form.factory')->createNamed(null, 'task_search', ['q' => $filter], [
            'action'          => $this->generateUrl('list_search'),
            'method'          => 'get',
            'csrf_protection' => false
        ]);

        $form->add('submit', 'submit');

        $tasks = $this->getTaskManager()->filterAll($filter);

        return $this->render("AppBundle:List:list.html.twig", [
            'filter' => $filter,
            'tasks'  => $tasks,
            'form'   => $form->createView()
        ]);
    }
}