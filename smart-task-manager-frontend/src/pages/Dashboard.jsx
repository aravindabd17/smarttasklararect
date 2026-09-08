import React from 'react';
import { useAuth } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';

const Dashboard = () => {
  const {logout,setUser}=useAuth();
  const navigate=useNavigate();
  const handleLogout=()=>{
    logout();
    navigate("/login");
    setUser(null);
  }
  return (
    <div>
      <div>
        <h2>Dashboard</h2>
        <button className='btn btn-primary' type='button' onClick={handleLogout}>Logout</button>
      </div>
    </div>
  )
}

export default Dashboard;
