import React, { useState } from 'react';
import api from '../api/axios';
import { useAuth } from '../context/AuthContext';
import { Link, useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';

const Login = () => {
  const [loginContent,setLoginContent]=useState({email:"",password:""});
  const [newErrors,setNewErrors]=useState({});
  const {login,setUser}=useAuth();
  const navigate = useNavigate();
  const onChangeHandler=(e)=>{
    setLoginContent({
      ...loginContent,
      [e.target.name]:e.target.value
    });
  }
  const validate=()=>{
    const errors={};
    if(!loginContent.email.trim())
    {
      errors.email="Email is required";
    }else if (
        !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(loginContent.email)
    ) {
        errors.email = "Invalid email format";
    }
    if(!loginContent.password.trim())
    {
      errors.password="Password is required";
    }
    setNewErrors(errors);
    return Object.keys(errors).length===0;
  }
  const handleSubmit=async(e)=>{
    e.preventDefault();
    if(!validate())
    {
      return;
    }
    try {
      const response=await api.post("/login",{
        email:loginContent.email,
        password:loginContent.password
      });
      setUser(response?.data?.user);
      login(response.data.token);
      toast.success("Loggedin successfully!");
      navigate("/dashboard");
    } catch (error) {
      toast.error(error?.response?.data?.msg || "login failed");
    }
  }
  return (
    <div className='container mt-5 pt-5 w-50'>
      <h3 className='text-primary'>Login</h3>
      <form method='post' onSubmit={handleSubmit}>
        <div className='form-group'>
          <label className='text-start d-block' htmlFor='email'>Email</label>
          <input type='email' className={`form-control ${newErrors.email?"is-invalid":""}`} name='email'value={loginContent.email} onChange={onChangeHandler}/>
          <div className='invalid-feedback'>
            {newErrors.email}
          </div>
        </div>
        <div className='form-group pt-3'>
          <label className='text-start d-block' htmlFor='password'>Password</label>
          <input type='password' className={`form-control ${newErrors.password?"is-invalid":""}`} name='password' value={loginContent.password} onChange={onChangeHandler}/>
          <div className='invalid-feedback'>
            {newErrors.password}
          </div>
        </div>
        <button className='btn btn-primary mt-4 w-100' type='submit' onClick={handleSubmit}>Submit</button>
        <p className='mt-3'>Don't have an account ?<Link to="/register"> Register here</Link></p>
      </form>
    </div>
  )
}

export default Login;
