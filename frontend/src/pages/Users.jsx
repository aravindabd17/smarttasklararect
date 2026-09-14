import React, { useEffect, useState } from 'react';
import axios from 'axios';
import toast from 'react-hot-toast';

const Users = () => {
  const [allUsers,setAllUsers]=useState([]);
  const [isEditing,setIsEditing]=useState(false);
  const [deleteId,setDeleteId]=useState(null);
  const [showModal,setShowModal]=useState(false);
  const [search,setSearch]=useState("");
  const [currentPage,setCurrentPage]=useState(1);
  const [formData,setFormData]=useState({
    name:"",
    username:"",
    email:"",
    address:{
        city:""
    }
  });
  const editHandler=(user)=>{
    setIsEditing(true);
    setFormData(user);
    setShowModal(true);
  }

  const filteredUsers=allUsers.filter(user=>user.name.toLowerCase().includes(search.toLowerCase()) ||
    user.username.toLowerCase().includes(search.toLowerCase()) ||
    user.email.toLowerCase().includes(search.toLowerCase())
  );

  const perpage=5;
  const startIndex=(currentPage - 1) * perpage;
  const currentUsers=filteredUsers.slice(startIndex,startIndex + perpage);
  const totalPages=Math.ceil(filteredUsers.length/perpage);
  console.log(currentUsers.length);
  console.log(totalPages);
  
  const fetchUsers=async()=>{
    try {
        const response=await axios.get("https://jsonplaceholder.typicode.com/users");
        setAllUsers(response.data);
    } catch (error) {
        toast.error(error?.response?.data?.message);
    }
  }
  const changeHandler=(e)=>{
    if(e.target.name=="city")
    {
        setFormData({
            ...formData,
            address:{
                ...formData.address,
                [e.target.name]:e.target.value
            }
        });
    }
    else 
    {
        setFormData({
            ...formData,
            [e.target.name]:e.target.value
        });
    }
  }
  const deleteHandler=async(id)=>{
    if(confirm("Are you sure want to delete this user?"))
    {
        setDeleteId(id);
        try {
            const response=await axios.delete(`https://jsonplaceholder.typicode.com/users/${id}`);
            const updatedUer=allUsers.filter(user=>user.id!==id);
            setAllUsers(updatedUer);
            toast.success("User deleted successfully!");
            setDeleteId(id);
        } catch (error) {
            toast.error(error?.response?.data?.error);
        }
    }
  }
  useEffect(()=>{
      fetchUsers();
  },[]);

  const submitHandler=async(e)=>{
    e.preventDefault();
    if(isEditing)
    {
        try {
            const newpost=await axios.put(`https://jsonplaceholder.typicode.com/users/${formData.id}`,{
            formData
            });
            console.log("newpost"+newpost);
            const updatedUser=allUsers.map(user=>{
                if(user.id===formData.id)
                {
                    return formData
                }
                else 
                {
                    return user;
                }
            })

            setAllUsers(updatedUser);
            toast.success("User updated Successfully");
            setShowModal(false);
            setIsEditing(null);
            setFormData({
                name:"",
                username:"",
                email:"",
                address:{
                    city:""
                }
            });

        } catch (error) {
            console.log(error);
        }
    }
    else 
    {
        try {
            const newpost=await axios.post("https://jsonplaceholder.typicode.com/users",{
            formData
            });
            setAllUsers([
                ...allUsers,
                newpost.data.formData
            ]);
            toast.success("User Created Successfully");
            setShowModal(false);
            setFormData({
                name:"",
                username:"",
                email:"",
                address:{
                    city:""
                }
            });

        } catch (error) {
            console.log(error);
        }
    }
  }
  if(allUsers.length<1) return <h3>Post not found</h3>;
  return (
    <>
    <div className='mt-4 pt-3'>
        <div className='d-flex justify-content-between align-item-center'>
            <h3>Users</h3>
            <button 
                type='button' 
                nam="adduser"
                className='btn btn-primary'
                onClick={()=>setShowModal(true)}
            >
                Add User
            </button>
        </div>
        <div>
            <form>
                <div 
                    className='form-group w-50 m-auto'
                >
                    <input 
                        type='text' 
                        name='search' 
                        className='form-control' 
                        placeholder='Enter the search'
                        onChange={e=>setSearch(e.target.value)}
                    />
                </div>
            </form>
        </div>
        <table className='table mt-4'>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>City</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {
                    currentUsers.map((user,index)=>(
                        <tr key={index}>
                            <td>{index+1}</td>
                            <td>{user.name}</td>
                            <td>{user.email}</td>
                            <td>{user.username}</td>
                            <td>{user.address.city}</td>
                            <td>
                                <button
                                    type='button'
                                    className='btn btn-secondary me-2'
                                    onClick={()=>editHandler(user)}
                                >
                                    Edit
                                </button>
                                <button
                                    type='button'
                                    className='btn btn-warning'
                                    onClick={()=>deleteHandler(user.id)}
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    ))
                }
            </tbody>
        </table>
        <ul className='pagination mt-4 justify-content-center'>
            <li
                className={`page-item ${currentPage==1?"disabled":""}`}
            >
                <button
                    type='button'
                    className='page-link'
                    disabled={currentPage==1}
                    onClick={()=>setCurrentPage(currentPage-1)}
                >   
                    Previous
                </button>
            </li>
            {
                Array.from(
                {length:totalPages},
                (_,index)=>index+1
                ).map(page=>(
                    <li
                        className={`page-item ${page==currentPage?"disabled":""}`}
                    >
                        <button
                            type='button'
                            className='page-link'
                            disabled={page==currentPage}
                            onClick={()=>setCurrentPage(page)}
                        >   
                            {page}
                        </button>
                    </li>
                ))
            }
            <li
                className={`page-item ${currentPage==1?"disabled":""}`}
            >
                <button
                    type='button'
                    name='Next'
                    className='page-link'
                    disabled={currentPage==totalPages}
                    onClick={()=>setCurrentPage(currentPage+1)}
                >   
                    Next
                </button>
            </li>
        </ul>
    </div>
    {
        showModal && <div className='usermodal'>
            <div className='userdialog'>
                <div 
                    className='userheader d-flex justify-content-between'
                >
                    <h3 className='mb-2'>{isEditing?"Edit User":"Add New User"}</h3>
                    <button 
                        type='button' 
                        className='btn btn-danger'
                        onClick={()=>setShowModal(false)}
                        >&times;</button>
                </div>
                <form method='post' onSubmit={submitHandler}>
                    <div className='modal-body mt-3'>
                        <div className='form-group mb-4'>
                            <label htmlFor='name'>Name</label>
                            <input 
                                id='name' 
                                name='name'
                                className='form-control'
                                value={formData.name}
                                onChange={changeHandler}
                            /> 
                        </div>
                        <div className='form-group mb-2'>
                            <label htmlFor='email'>Email</label>
                            <input 
                                id='email' 
                                name='email'
                                className='form-control'
                                value={formData.email}
                                onChange={changeHandler}
                            /> 
                        </div>
                        <div className='form-group mb-2'>
                            <label htmlFor='username'>Username</label>
                            <input 
                                id='username' 
                                name='username'
                                className='form-control'
                                value={formData.username}
                                onChange={changeHandler}
                            /> 
                        </div>
                        <div className='form-group mb-2'>
                            <label htmlFor='city'>City</label>
                            <select
                                name='city'
                                id="city"
                                className='form-control'
                                value={formData.address.city}
                                onChange={changeHandler}
                            >
                                <option>-- Select City --</option>
                                <option value="Chennai">Chennai</option>
                                <option value="Bangalore">Bangalore</option>
                                <option value="Covai">Covai</option>
                            </select>
                        </div>
                        <button 
                            className='btn btn-warning w-100 mt-3' 
                            type='submit'
                        >
                            {isEditing?"Update":"Submit"}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    }
    </>
  )
}

export default Users;