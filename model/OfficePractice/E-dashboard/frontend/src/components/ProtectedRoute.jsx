import React from "react";
import { Navigate } from "react-router-dom";
import { getCookie } from "../Utils/cookies";

export default function ProtectedRoute({ children, adminOnly = false }) {
  const token = getCookie("token");
  const role = getCookie("role");

  if (!token) {
    return <Navigate to="/LoginForm" replace />;
  }
 
  if (adminOnly && role !== "admin") {
    return <Navigate to="/" replace />;
  }

  
  return children;
}
