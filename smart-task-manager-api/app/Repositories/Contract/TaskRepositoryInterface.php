<?php
namespace App\Repositories\Contract;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function getAll(array $filters):LengthAwarePaginator;
    public function find(Task $task):Task;
    public function store(array $data):Task;
    public function update(Task $task,array $data):Task;
    public function delete(Task $task):bool;
}

?>