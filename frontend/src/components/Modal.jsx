import React from 'react';

const Modal = ({showModal,setShowModal,formData,handleChange,isEditing,submitHandler}) => {
  return (
    <>
        {
        showModal && <div className='formmodal'>
          <div className='formmodal-dialog'>
            <form method='post' onSubmit={submitHandler}>
              <div className='formmodal-header d-flex justify-content-between align-items-center'>
                <h3>{isEditing?"Edit Post":"Add Post"}</h3>
                <button className='btn btn-primary' type='button' onClick={()=>setShowModal(false)}>&times;</button>
              </div>
              <div className='formmodal-body mt-3'>
                <div className='form-group'>
                  <label>Title</label>
                  <input type='text' className='form-control' name='title' value={formData.title} onChange={handleChange}/>
                </div>
                <div className='form-group mt-3'>
                  <label>Body</label>
                  <textarea name='body' className='form-control' value={formData.body} onChange={handleChange} rows="4"/>
                </div>
                <button type='submit' className='btn btn-primary mt-3 w-100' >{isEditing?"Update":"Add"}</button>
              </div>
            </form>
          </div>
        </div>
        }
    </>
    
  )
}

export default Modal;