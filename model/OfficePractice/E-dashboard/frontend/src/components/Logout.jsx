import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { deleteCookie } from "../Utils/cookies";

export default function Logout() {
  const navigate = useNavigate();




  useEffect(() => {
    deleteCookie("token");
    deleteCookie("role");
      deleteCookie("userId");
    navigate("/LoginForm"); 
    window.location.href = "/LoginForm";
  }, [navigate]);
  window.location.href = "/LoginForm";
  

  return null;
}
