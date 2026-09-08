<h2>Hello, {{$user->name}}</h2>

<p>Here are your pending tasks</p>
<ul>
    @foreach($tasks as $task)
        <li>{{$task->title}}</li>
    @endforeach
</ul>
<p>Total pending tasks: {{count($tasks)}}</p>