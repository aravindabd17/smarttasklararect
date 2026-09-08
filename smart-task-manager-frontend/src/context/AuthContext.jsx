import { createContext, useContext, useState } from "react";


const AuthContext=createContext();

export function AuthProvider({children}){
    const [token,setToken]=useState(localStorage.getItem('token'));
    const [user,setUser]=useState(null);
    const login=(token)=>
    {
        if(token)
        {
            localStorage.setItem("token",token);
            setToken(token)
        }
    }
    const logout=()=>{
        localStorage.removeItem("token");
        setToken(null);
    }

    return (
        <AuthContext.Provider value={{login,logout,token,setUser,user}}>
            {children}
        </AuthContext.Provider>
    );
}

export const useAuth=()=>useContext(AuthContext);