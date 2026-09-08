<?php

namespace App\Services;

use App\Models\Task;
use Barryvdh\DomPDF\Facade\Pdf;

class TaskPdfService
{
    /**
     * Create a new class instance.
     */
    public function generate(Task $task):string
    {
        return Pdf::loadView('pdf.task-report',compact('task'))->output();
    }
}
