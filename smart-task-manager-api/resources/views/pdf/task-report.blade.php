<!DOCTYPE html>
<html>

<head>
    <style>

        body{
            font-family: DejaVu Sans;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        table,td,th{
            border:1px solid black;
            padding:8px;
        }

    </style>
</head>

<body>

<h2>Task Report</h2>

<table>

<tr>
    <th>Title</th>
    <td>{{ $task->title }}</td>
</tr>

<tr>
    <th>Description</th>
    <td>{{ $task->description }}</td>
</tr>

<tr>
    <th>Status</th>
    <td>{{ ucfirst($task->status) }}</td>
</tr>

<tr>
    <th>Created By</th>
    <td>{{ $task->user->name }}</td>
</tr>

<tr>
    <th>Email</th>
    <td>{{ $task->user->email }}</td>
</tr>

</table>

</body>
</html>