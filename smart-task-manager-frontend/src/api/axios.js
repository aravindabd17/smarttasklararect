import axios from 'axios';

const api = axios.create({
    baseURL:"http://127.0.0.1:8000/api",
    headers:{
        Accept:"application/json",
    }
});

api.interceptors.request.use((request)=>{
    const token=localStorage.getItem("token");
    if(token)
        request.headers.Authorization=`Bearer ${token}`;
    return request;
});

api.interceptors.response.use(
    (response)=>{
        if(response)
            return response;
    },
    (error)=>{
        if(error.response?.status==401)
        {
            localStorage.removeItem("token");
            window.location.href="/login";
        }
        return Promise.reject(error);
    }
)

export default api;