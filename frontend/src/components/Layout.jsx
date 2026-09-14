import React from 'react'
import { Outlet } from 'react-router-dom';
import Sidebar from './Sidebar';

const Layout = () => {
  return (
    <div className='row'>
        <div className='col-md-3'>
            <Sidebar/>
        </div>
        
        <div className='col-md-9'>
            <Outlet/>
        </div>
    </div>
  )
}

export default Layout;