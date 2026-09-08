import { useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from './assets/vite.svg'
import heroImg from './assets/hero.png'
import './App.css'
import { BrowserRouter, Route, Router, Routes } from 'react-router-dom'
import Login from './pages/Login'
import Register from './pages/Register'
import ProtectedRoutes from './routes/protectedRoutes'
import Dashboard from './pages/Dashboard'
import Task from './pages/Task'
import DashboardLayout from './layouts/DashboardLayout'
import Users from './pages/Users'

function App() {
  const [count, setCount] = useState(0);

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<Login/>}/>
        <Route path="/register" element={<Register/>}/>
        <Route element={<ProtectedRoutes/>}>
          <Route path='/dashboard' element={<DashboardLayout/>}>
            <Route index element={<Dashboard/>}/>
            <Route path="task" element={<Task/>}/>
            <Route path="users" element={<Users/>}/>
          </Route>
        </Route>
      </Routes>
    </BrowserRouter>
  )
}

export default App;
