import React, { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import Post from '../components/Post';
import Modal from '../components/Modal';

const task = () => {
    const [posts,setPosts]=useState([]);
    const [showModal,setShowModal]=useState(false);
    const [isEditing,setIsEditing]=useState(false);
    const [deleteId,setDeleteId]=useState(null);
    const [pagination,setPagination]=useState({
        totalPage:1,
        currentPage:1
    });
    const [formData,setFormData]=useState({
        title:"",
        body:""
    });

    
    const [search,setSearch]=useState("");
    const [currentPage,setCurrentPage]=useState(1);

    const postperpage=8;
    const filterPosts=posts.filter(post=>(
      post.title.toLowerCase().includes(search.toLowerCase()) ||
      post.body.toLowerCase().includes(search.toLowerCase())
    ));
    let startIndex=(currentPage - 1) * postperpage;
    let currentPosts=filterPosts.slice(startIndex,startIndex+postperpage);

    let totalPages=Math.ceil(filterPosts.length/postperpage);

  const handleChange=(e)=>{
    setFormData({
      ...formData,
      [e.target.name]:e.target.value
    })
  }
  const editHandler=(post)=>{
    setIsEditing(true);
    setShowModal(true);
    setFormData({
      id:post.id,
      title:post.title,
      body:post.body
    });
  }

  const deleteHandler=(id)=>{
    if(confirm("Are you sure want to delete the post?"))
    {
      setDeleteId(id);
      try {
        fetch(`https://jsonplaceholder.typicode.com/posts/${id}`,{
          method:"DELETE"
        });
        const filteredPost=posts.filter(post=>post.id!==id);
        setPosts(filteredPost);
        toast.success("Post deleted successfully");
      } catch (error) {
        console.log(error);
      }
    }
    else
    {
      console.log("not confirmed");
    }
  }
  
  const submitHandler=async(e)=>{
    e.preventDefault();
    if(isEditing)
    {
      try {
        const updatedPost=await fetch(`https://jsonplaceholder.typicode.com/posts/${formData.id}`,{
          method:"PUT",
          body:JSON.stringify(formData),
          headers:{
            'Content-Type':"application/json"
          }
        });
        const data=await updatedPost.json();
        toast.success("Post updated successfully");
        const updated=posts.map(post=>{
          if(post.id===formData.id)
            return formData;
          else
            return post;
        });
        setPosts(updated);

      } catch (error) {
        toast.error(error?.response?.error?.data);
      }
      finally
      {
        setIsEditing(false);
        setShowModal(false);
        setFormData({
          title:"",
          body:""
        });
      }
      
    }
    else 
    {
      try {
        const createdPost=await fetch("https://jsonplaceholder.typicode.com/posts",{
          method:"POST",
          body:JSON.stringify(formData),
          headers:{
            'Content-Type':"application/json"
          }
        });
        const data=await createdPost.json();
        setPosts([
          ...posts,
          data
        ]);
        setShowModal(false);
        toast.success("Post added successfully");
      } catch (error) {
        toast.error(error?.response?.error?.data);
      } 
    }
  }

  const fetchPost=async()=>{
    const allposts=await fetch("https://jsonplaceholder.typicode.com/posts");
    const data=await allposts.json();
    setPosts(data);
  }
  useEffect(()=>{
    fetchPost();
  },[]);
  if(!posts.length) return <p className='spinner-border text-primary spinner-border-md d-block m-auto mt-5'></p>;
  return (
    <>
      <div className='mt-5 pt-3 container'>
        <div className='d-flex justify-content-between align-items-center'>
          <h2>Posts</h2>
          <button type='button' className='btn btn-primary' onClick={()=>setShowModal(true)}>Create</button>
        </div>
        <div className='seachsection w-50 mx-auto d-flex'>
          <input type='text' name='search' placeholder='Enter your text' className='form-control' value={search} onChange={(e)=>setSearch(e.target.value)}/>
          <button type='button' className='btn btn-primary'>Search</button>
        </div>
        <div className='cards mt-3 row g-3'>
          {currentPosts.map((post,index)=>(
              <Post 
                key={index}
                post={post} 
                editHandler={editHandler} 
                deleteHandler={deleteHandler}/>
            ))}
        </div>
        <ul className='pagination'>
            <li 
              className={`page-item ${currentPage==1?"disabled":""}`}
            >
              <button 
                className='page-link'
                disabled={currentPage==1}
                onClick={()=>setCurrentPage(currentPage-1)}
              >
                Previous
              </button>
            </li>
            {Array.from(
              {length:totalPages},
              (_,index)=>index+1
            ).map(page=>(
              <li
                className={`page-item ${currentPage===page?"active":""}`}
              >
                <button
                  type='button'
                  className='page-link'
                  onClick={()=>setCurrentPage(page)}
                >
                  {page}
                </button>
              </li>
            ))}
            <li 
              className={`page-item ${currentPage==totalPages?"disabled":""}`}
            >
              <button 
                className='page-link'
                disabled={currentPage==totalPages}
                onClick={()=>setCurrentPage(currentPage+11)}
              >
                Next
              </button>
            </li>
        </ul>
      </div>
      <Modal 
        showModal={showModal} 
        setShowModal={setShowModal} 
        formData={formData}
        handleChange={handleChange}
        isEditing={isEditing}
        submitHandler={submitHandler}
      />
    </>
  )
}

export default task;