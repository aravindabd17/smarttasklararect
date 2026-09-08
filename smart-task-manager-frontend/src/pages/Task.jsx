import axios from 'axios';
import React, { useEffect, useState } from 'react'
import api from '../api/axios';
import toast from 'react-hot-toast';
import { useAuth } from '../context/AuthContext';

const Task = () => {
  const [tasklist,setTaskList]=useState([]);
  const [loading,setLoading]=useState(false);
  const [newtask,setNewtask]=useState({
    title:"",
    description:"",
    status:"pending"
  });
  const [errors,setErrors]=useState({});
  const [showModal,setShowModal]=useState(false);
  const [submitting,setSubmitting]=useState(false);
  const [isEditing,setIsEditing]=useState(null);
  const [showDeleteModal,setShowDeleteModal]=useState(false);
  const [deleteTaskId,setDeleteTaskId]=useState(null);
  const [deleteTask,setDeleteTask]=useState(null);
  const [search,setSearch]=useState('');
  const [status,setStatus]=useState('');
  const [pagination,setPagination]=useState({
    currentPage:1,
    total:1,
    lastPage:0
  });
  const {user}=useAuth();


  const handleEdit=(task)=>{
    setIsEditing(task);
    setShowModal(true);
    setNewtask({
      title:task.title,
      description:task.description,
      status:task.status
    });
  }

  const handleDelete=(task)=>{
    setDeleteTask(task);
    setShowDeleteModal(true);
  }
  const cancelDelete=()=>{
    setDeleteTaskId(null);
    setDeleteTask(null);
    setShowDeleteModal(false);
  }

  const fetchTask=async(page=1,searchValue=search,statusFilter=status)=>{
    try {
        setLoading(true);
        const response=await api.get('/tasks',{
          params:{
            search:searchValue,
            status:statusFilter,
            page:page
          }
        });
        setPagination({
          currentPage:response.data.meta.current_page,
          lastPage:response.data.meta.last_page,
          total:response.data.meta.total
        });
        setTaskList(response?.data?.data || response?.data);
    } catch (error) {
        toast.error(error?.response?.data?.message || "Failed to fetch data");
    }finally{
      setLoading(false)
    }
  }

  const handleDeleteItem=async()=>{
    try {
      if(!deleteTask.id)
        return;
      setDeleteTaskId(deleteTask.id);
      const deletedTasks=await api.delete(`/tasks/${deleteTask.id}`);
      // const updatedTaskList=tasklist.filter(task=>task.id!==deleteTask.id);
      // setTaskList(updatedTaskList);
      toast.success("Task deleted successfully!");
      setDeleteTaskId(null);
      setDeleteTask(null);
      setShowDeleteModal(false);
      let pageload= pagination.currentPage;
      if(tasklist.length == 1 && pagination.currentPage > 1)
      {
        pageload = pagination.currentPage - 1;
      }
      await fetchTask(
        pageload,search,status
      );
    } catch (error) {
      toast.error(error?.response?.data?.message);
    }
    finally{
      setDeleteTaskId(null);
    }
  }

  useEffect(()=>{
    fetchTask();
  },[]);

  if(loading)
  {
    return (
      <div className='text-center mt-4'>
        <div 
          className='spinner-border text-primary'
          role='status'
        >
          <span className='visually-hidden'>
            Loading
          </span>
        </div>

      </div>
    )
  }

  const onChangeHandler=(e)=>{
    setNewtask({
      ...newtask,
      [e.target.name]:e.target.value
    });
  }

  const validate=()=>{
    const newErrors={};
    if(!newtask.title.trim())
      newErrors.title="Title required";
    if(!newtask.description.trim())
      newErrors.description="Description required";
    if(!newtask.status.trim())
      newErrors.status="Status required";

    setErrors(newErrors);

    return Object.keys(newErrors).length===0;
  }

  const resetForm=()=>{
      setErrors({});
      setNewtask({
        title:"",
        description:"",
        status:"pending"
      });
  }

  const handleAddTask=async(e)=>{
    e.preventDefault();
    if(!validate())
      return;
    setSubmitting(true);
    try {
      if(isEditing)
      {
        const updateTask=await api.put(`/tasks/${isEditing.id}`,{
            title:newtask.title,
            description:newtask.description,
            status:newtask.status
        });
        toast.success("Task Updated successfully!");
        
      }
      else 
      {
          const createTask=await api.post("/tasks",{
            title:newtask.title,
            description:newtask.description,
            status:newtask.status
          });
          toast.success("Task added successfully!");
      }
      setIsEditing(null);
      setShowModal(false);
      await fetchTask(pagination.currentPage,search,status);
      resetForm();
    }catch (error) {
      if(error?.response?.data?.status===422)
        setErrors(error?.response?.data?.errors);
      else 
      {
        toast.error(error?.response?.data?.message);
      }
    }finally{
      setSubmitting(false);
    }
  }

  const handlePage=(page)=>{
    if(page<1 || page>pagination.lastPage ||  page===pagination.currentPage)
      return;
    
    fetchTask(page,search,status);
  }
  return (
    <div>
        <div className='d-flex justify-content-between align-items-center mb-4'>
          <h3 className='text-primary'>Create Task</h3>
          <button 
            type='button' 
            className='btn btn-primary'
            onClick={()=>{
              setShowModal(true);
              setIsEditing(null);
              resetForm();
            }}
          >Add Task</button>
        </div>
        <div className='searchsection m-auto w-75'>
          <div className='row mb-4 justify-content-center'>
            <div className='col-md-6 d-flex gap-2'>
              <input 
                type='text'
                className='form-control py-2'
                onChange={e=>setSearch(e.target.value)}
                placeholder='Enter the search'
                value={search}
              />
              <select 
                name='searchFilter'
                className='form-control'
                value={status}
                onChange={e=>setStatus(e.target.value)}
              >
                <option value="">-- Select Status --</option>
                <option value="pending">Pending</option>
                <option value="in-progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
            </div>
            <div className='col-md-2'>
              <button
                type='button'
                className='btn btn-primary'
                onClick={()=>fetchTask(1,search,status)}
              >
                Search
              </button>
              {
                search && (
                  <button
                    type='button'
                    className='btn btn-secondary'
                    onClick={()=>{
                      setSearch("");
                      fetchTask(1,"");
                    }}
                  >
                    Clear
                  </button>
                )
              }
            </div>
          </div>
        </div>
        <div>
          <p className='text-muted'>Total tasks : {pagination.total}</p>
        </div>
        {
          tasklist.length===0?(
            <div className='alert alert-primary'>
              No tasks found
            </div>
          ):(
            <div className='table-responsive'>
              <table className='table'>
                <thead className='mb-4'>
                  <th>S.No</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  {
                    tasklist.map((task,index)=>{
                      const isOwner=user?.id===task.user_id;
                      return <tr key={index}>
                        <td>{index+1}</td>
                        <td>{task.title}</td>
                        <td>{task.description}</td>
                        <td>
                          <span className='badge bg-primary'>
                            {task.status}
                          </span>
                        </td>
                        <td className='d-flex g-3'>
                          {
                            isOwner &&
                            <>
                              <button 
                                type='button' 
                                className='btn btn-warning me-2'
                                onClick={()=>handleEdit(task)}
                              >Edit</button>
                              <button 
                                type='button' 
                                className='btn btn-danger'
                                onClick={()=>handleDelete(task)}
                                disabled={task.id===deleteTaskId}
                              >
                                Delete
                              </button>
                            </>
                          }
                        </td>
                      </tr>
                    })
                  }
                </tbody>
              </table>
            </div>
        )}

        {
          showModal && (
            <div 
              className='modal d-block ' 
              tabIndex="-1"
              style={{backgroundColor:"rgba(0,0,0,0.5)"}}
            >
              <div className='modal-dialog'>
                <div className='modal-content p-3'>
                  <div className='modal-header'>
                    <h3 className='text-primary'>{isEditing?"Edit Task":"Add Task"}</h3>
                    <button
                      className='btn btn-primary btn-close'
                      onClick={()=>setShowModal(false)}
                    />
                  </div>
                  <form method='post' onSubmit={handleAddTask}>
                    <div className='modal-body'>
                      <div className='form-group mb-3'>
                        <label className='form-title'>Title</label>
                        <input
                          type='text'
                          name='title'
                          className={`form-control mt-1 ${errors.title?"is-invalid":""}`}
                          onChange={onChangeHandler}
                          value={newtask.title}
                        />
                        {
                          errors.title && (
                            <div className='invalid-feedback'>
                              {
                                Array.isArray(errors.title)?errors.title[0]:errors.title
                              }
                            </div>
                          )
                        }
                      </div>
                      <div className='form-group mb-3'>
                        <label className='form-title'>Decription</label>
                        <textarea
                          type='text'
                          name='description'
                          className={`form-control mt-1 ${errors.description?"is-invalid":""}`}
                          onChange={onChangeHandler}
                          value={newtask.description}
                        />
                        {
                          errors.description && (
                            <div className='invalid-feedback'>
                              {
                                Array.isArray(errors.description)?errors.description[0]:errors.description
                              }
                            </div>
                          )
                        }
                      </div>
                      <div className='form-group mb-3'>
                        <label className='form-title'>Decription</label>
                        <select
                          type='text'
                          name='status'
                          className={`form-control mt-1 ${errors.status?"is-invalid":""}`}
                          onChange={onChangeHandler}
                          value={newtask.status}
                        >
                          <option value=''>Select Status</option>
                          <option value="pending">Pending</option>
                          <option value="in-progress">In Progress</option>
                          <option value="completed">Completed</option>
                        </select>
                        {
                          errors.status && (
                            <div className='invalid-feedback'>
                              {
                                Array.isArray(errors.status)?errors.status[0]:errors.status
                              }
                            </div>
                          )
                        }
                      </div>
                      <button 
                        type='submit' 
                        className='btn btn-primary mt-3 w-100 mb-4'
                        disabled={submitting}
                      >
                        {
                          submitting?(
                          <>
                             <span className='spinner-border spinner-border-sm me-2'></span>
                              Submitting
                          </>
                          ):(
                            isEditing?"Update":"Submit"
                          ) 
                        }
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          )
        }
        {
          showDeleteModal && (
            <div 
              className='modal d-block show'
              style={{backgroundColor:"rgba(0,0,0,0.5)"}}
            >
              <div className='modal-dialog'>
                <div className='modal-content'>
                  <div className='modal-header'>
                    <h5 className='modal-title'>
                      Delete Modal
                    </h5>
                    <button className='btn btn-primary btn-close' type='button' onClick={()=>cancelDelete()}/>
                  </div>
                  <div className='modal-body'>
                    <p>Are you sure want delete the task <strong>{deleteTask?.title}</strong></p>
                  </div>
                  <div className='modal-footer'>
                    <button  
                      className='btn btn-danger'
                      onClick={handleDeleteItem}
                      disabled={deleteTaskId!==null}
                    >
                      {
                        deleteTaskId?(
                          <>
                            <span className='spinner-border spinner-border-sm me-2'/>
                       
                            Deleting ....
                          </>
                        ):"Delete"
                      }
                      
                    </button>
                    <button 
                      className='btn btn-warning'
                      onClick={cancelDelete}
                    >
                      Cancel
                    </button>
                  </div>
                </div>
              </div>
            </div>
          )
        }
        {
          pagination.lastPage>1 && (
            <ul className='pagination mt-2'>
              <li 
                className='page-item'
              >
                <button 
                  className={`page-link ${pagination.currentPage===1 ?"disabled":""}`}
                  onClick={()=>handlePage(pagination.currentPage-1)}
                >
                  Previous
                </button>
              </li>
              {
                Array.from(
                  {length:pagination.lastPage},
                  (_,index)=>index+1)
                  .map((page)=>(
                  <li 
                    key={page}
                    className={`page-item ${pagination.currentPage===page ?"active":""}`} 
                  >
                    <button
                      className={`page-link`}
                      onClick={()=>handlePage(page)}
                    >
                      {page}
                    </button>
                  </li>
                ))
              }
              <li 
                className='page-item'
              >
                <button 
                  className={`page-link ${pagination.currentPage===pagination.lastPage ?"disabled":""}`}
                  onClick={()=>handlePage(pagination.currentPage+1)}
                >
                  Last
                </button>
              </li>
            </ul>
          )
        }
    </div>
    
  )
}

export default Task;
