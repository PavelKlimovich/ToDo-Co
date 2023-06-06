<?php

namespace App\Services;

use App\Repository\TaskRepository;

class TaskService
{
    private TaskRepository $taskRepository; 

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }
}