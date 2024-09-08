<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <script src="/js/app.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
</head>

<body>
    <h1 style="text-align: center; margin-bottom:20px;">Todo List</h1>

    <form class="row g-3" id="todo-form" style="display:flex; justify-content:center;">
        <div class="col-auto">
            <label for="staticEmail2" class="visually-hidden">Title</label>
            <input type="text" id="todo-title" class="form-control" name="title" placeholder="Add a todo">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3">SAVE</button>
        </div>
    </form>

    <!-- Todo List -->
    <ul id="todo-list" class="list-group"
        style="display:flex; justify-content:center; align-items:center; flex-direction:column;">
        @foreach ($todos as $todo)
            <li data-id="{{ $todo->id }}" class="list-group-item d-flex justify-content-between align-items-center"
                style="width: 50%;">
                <input type="text" value="{{ $todo->title }}" class="todo-title form-control"
                    style="border:none; width: 70%;">
                <div>
                    <button class="edit-todo btn btn-warning me-2">Update</button>
                    <button class="delete-todo btn btn-danger">Delete</button>
                </div>
            </li>
        @endforeach
    </ul>

    <script>
        const todoList = document.getElementById('todo-list');
        const todoForm = document.getElementById('todo-form');
        const todoTitleInput = document.getElementById('todo-title');
        const socket = io('http://localhost:8000');

        // Handle adding new todo
        todoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const title = todoTitleInput.value;

            fetch('/todos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        title
                    })
                })
                .then(response => response.json())
                .then(todo => {
                    socket.emit('createTodo', todo);
                    todoTitleInput.value = '';

                    // Success Notification for Create
                    Swal.fire({
                        icon: 'success',
                        title: 'Todo Added Successfully!',
                        showConfirmButton: true,
                        timer: 1500
                    });
                });
        });

        // Handle receiving new todo from Socket.IO
        socket.on('todoCreated', function(todo) {
            const li = document.createElement('li');
            li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
            li.style.width = '50%';
            li.setAttribute('data-id', todo.id);
            li.innerHTML = `
            <input type="text" class="todo-title form-control" style="border:none; width: 70%;" value="${todo.title}">
            <div>
                <button class="edit-todo btn btn-warning me-2">Update</button>
                <button class="delete-todo btn btn-danger">Delete</button>
            </div>
        `;
            todoList.appendChild(li);
        });

        // Handle editing/updating todo
        todoList.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-todo')) {
                const li = e.target.parentElement.parentElement;
                const id = li.getAttribute('data-id');
                const title = li.querySelector('.todo-title').value;

                fetch(`/todos/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            title
                        })
                    })
                    .then(response => response.json())
                    .then(updatedTodo => {
                        socket.emit('updateTodo', updatedTodo);

                        Swal.fire({
                            icon: 'success',
                            title: 'Todo Updated Successfully!',
                            showConfirmButton: true,
                            timer: 1500
                        });
                    });
            }
        });

        // Handle receiving updated todo from Socket.IO
        socket.on('todoUpdated', function(updatedTodo) {
            const todoItem = document.querySelector(`li[data-id="${updatedTodo.id}"]`);
            if (todoItem) {
                todoItem.querySelector('.todo-title').value = updatedTodo.title;
            }
        });

        // Handle deleting todo
        todoList.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-todo')) {
                const li = e.target.parentElement.parentElement;
                const id = li.getAttribute('data-id');

                fetch(`/todos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(() => {
                        socket.emit('deleteTodo', {
                            id
                        });

                        // Success Notification for Delete
                        Swal.fire({
                            icon: 'success',
                            title: 'Todo Deleted Successfully!',
                            showConfirmButton: true,
                            timer: 1500
                        });
                    });
            }
        });

        // Handle receiving deleted todo from Socket.IO
        socket.on('todoDeleted', function(data) {
            const todoItem = document.querySelector(`li[data-id="${data.id}"]`);
            if (todoItem) {
                todoItem.remove();
            }
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
