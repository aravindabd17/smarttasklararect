import './App.css';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import Home from './pages/Home';
import Task from './pages/Task';
import Layout from './components/Layout';
import Users from './pages/Users';

function App() {
return (
    <>
      <BrowserRouter>
        <Routes>
          <Route element={<Layout/>}>
            <Route path='/' element={<Home/>}/>
            <Route path='/task' element={<Task/>}/>
            <Route path='/user' element={<Users/>}/>
          </Route>
        </Routes>
      </BrowserRouter>
    </>
    
  )
}

export default App
