export default function Post({post,editHandler,deleteHandler})
{
    console.log("post:"+post);
    return (
        <div className='col-md-3'>
            <div className='card p-3'>
                <h3 className='card-title'>{post.title}</h3>
                <p className='card-content'>{post.body}</p>
                <div className='buttoncontainer d-flex gap-2'>
                <button type='button' className='btn btn-warning' onClick={()=>editHandler(post)}>Edit</button>
                <button type='button' className='btn btn-danger' onClick={()=>deleteHandler(post.id)}>Delete</button>
                </div>
            </div>
        </div>
    )
}