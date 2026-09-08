import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import api from '../api/axios';
import { useAuth } from '../context/AuthContext';
import toast from 'react-hot-toast';

const Register = () => {
  const [register,setRegister]=useState({name:"",email:"",password:"",password_confirmation:""});
  const [newErrors,setNewErrors]=useState([]);
  const {login}=useAuth();
  const navigate=useNavigate();
  const validate=()=>{
    const errors={};
    if(!register.name.trim())
    {
      errors.name="Name is required";
    }
    if(!register.email.trim())
    {
      errors.email="Email is required";
    }else if (
        !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(register.email)
    ) {
        errors.email = "Invalid email format";
    }
    if(!register.password.trim())
    {
      errors.password="Password is required";
    }else if(register.password<6)
    {
      errors.password="Invalid required";
    }

    if(!register.password_confirmation.trim())
    {
      errors.password_confirmation="Confirm Password is required";
    }else if(register.password!=register.password_confirmation)
    {
      errors.password="Password don't match";
    }
    setNewErrors(errors);
    return Object.keys(errors).length===0;
  }
  const onChangeHandler=(e)=>{
    setRegister({
      ...register,
      [e.target.name]:e.target.value
    });
  }
  const handleSubmit=async(e)=>{
    e.preventDefault();
    if(!validate())
    {
      return;
    }
    try {
      const status=await api.post("/register",{
        name:register.name,
        email:register.email,
        password:register.password,
        password_confirmation:register.password_confirmation
      });
      console.log(status);
      toast.success("Loggedin successfully!");
      login(status.data.token);
      navigate("/dashboard");
    } catch (error) {
      toast.error(error?.response?.data?.msg || "login failed");
    }
  }
  return (
    <div className='container mt-5 pt-5 w-50'>
      <h3 className='text-primary'>Register</h3>
      <form method='post'>
        <div className='form-group'>
          <label className='text-start d-block' htmlFor='name'>Name</label>
          <input type='text' className={`form-control ${newErrors.name?"is-valid":""}`} name='name' alt='name' value={register.name} onChange={onChangeHandler}/>
          <div className='invalid-feedback'>
            {newErrors.name}
          </div>
        </div>
        <div className='form-group mt-2'>
          <label className='text-start d-block' htmlFor='email'>Email</label>
          <input type='email' className={`form-control ${newErrors.email?"is-valid":""}`} name='email' alt='email' value={register.email} onChange={onChangeHandler}/>
          <div className='invalid-feedback'>
            {newErrors.email}
          </div>
        </div>
        <div className='form-group mt-2'>
          <label className='text-start d-block' htmlFor='password'>Password</label>
          <input type='password' className={`form-control ${newErrors.password?"is-valid":""}`} name='password' alt='password' value={register.password} onChange={onChangeHandler}/>
          <div className='invalid-feedback'>
            {newErrors.password}
          </div>
        </div>
        <div className='form-group mt-2'>
          <label className='text-start d-block' htmlFor='password_confirmation'>Confirm Password</label>
          <input type='password' className={`form-control ${newErrors.password_confirmation?"is-valid":""}`} name='password_confirmation' alt='password' value={register.password_confirmation} onChange={onChangeHandler}/>
          <div className='invalid-feedback'>
            {newErrors.confirm_password}
          </div>
        </div>
        <button className='btn btn-primary mt-4 w-100' type='button' onClick={handleSubmit}>Submit</button>
        <p className='mt-3'>Already have a account ?<Link to="/login"> Login here</Link></p>
      </form>
    </div>
  )
}

export default Register;
