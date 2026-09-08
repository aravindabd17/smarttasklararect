import React from 'react';
import { Link, Outlet } from 'react-router-dom';

const DashboardLayout = () => {
  return (
    <div className='container-fluid '>
      <div className='row'>
        <aside className='col-md-2 bg-dark min-vh-100 p-4'>
          <h4 className='text-white pb-3'>Dashboard</h4>
            <ul className='nav flex-column'>
                <li className='nav-item'>
                    <Link className='nav-link text-white' to="/dashboard">Dashboard</Link>
                </li>
                <li className='nav-item'>
                    <Link className='nav-link text-white' to="/dashboard/task">Tasks</Link>
                </li>
                <li className='nav-item'>
                    <Link className='nav-link text-white' to="/dashboard/users">Users</Link>
                </li>
            </ul>            
        </aside>
        <main className='col-md-10 pt-4 ps-5'>
            <Outlet/>
        </main>
      </div>
    </div>
  )
}

export default DashboardLayout;
