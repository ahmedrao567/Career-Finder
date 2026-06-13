import React, { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { getCookie, deleteCookie } from "../Utils/cookies";


export default function Nav() {
  const [token, setToken] = useState(getCookie("token"));
  const [role, setRole] = useState(getCookie("role"));
  const navigate = useNavigate();

  
  useEffect(() => {
    const interval = setInterval(() => {
      setToken(getCookie("token"));
      setRole(getCookie("role"));
    }, 500); 
    return () => clearInterval(interval);
  }, []);

  
  const handleLogout = () => {
    deleteCookie("token");
    deleteCookie("role");
    setToken(null); 
    setRole(null);
    navigate("/LoginForm");
  };

  return (
    <nav className="bg-gradient-to-r from-purple-500 to-blue-300 shadow-md py-4 px-6">
      <ul className="flex justify-center gap-15">

        <li>
          <Link
            to="/"
            className="text-white font-semibold hover:text-yellow-300 transition-colors duration-200"
          >
            Home
          </Link>
        </li>

        


        {token && (
          <li>
            <button
              onClick={handleLogout}
              className="text-white font-semibold hover:text-yellow-300 transition-colors duration-200"
            >
              Logout
            </button>
          </li>
        )}

        {token && role === "admin" && (
          <li>
            <Link
              to="/Dashboard"
              className="text-white font-semibold hover:text-yellow-300 transition-colors duration-200"
            >
              Dashboard
            </Link>
          </li>
        )}

        {!token && (
          <>
            <li>
              <Link
                to="/Signup"
                className="text-white font-semibold hover:text-yellow-300 transition-colors duration-200"
              >
                Signup
              </Link>
            </li>
            <li>
              <Link
                to="/LoginForm"
                className="text-white font-semibold hover:text-yellow-300 transition-colors duration-200"
              >
                Login
              </Link>
            </li>
          </>
        )}

      </ul>
    </nav>
  );
}
