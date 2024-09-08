const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});

app.use(cors());

io.on('connection', (socket) => {
    console.log('User connected:', socket.id);

    socket.on('createTodo', (data) => {
        io.emit('todoCreated', data);
    });

    socket.on('updateTodo', (data) => {
        io.emit('todoUpdated', data);
    });

    socket.on('deleteTodo', (data) => {
        io.emit('todoDeleted', data);
    });

    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
    });
});

server.listen(8000, () => {
    console.log('WebSocket server running on port 8000');
});
