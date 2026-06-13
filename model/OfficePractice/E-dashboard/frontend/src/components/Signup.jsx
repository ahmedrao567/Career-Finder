import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { setCookie } from "../Utils/cookies";


export default function SignupForm() {
  const [form, setForm] = useState({ name: "", email: "", password: "", role: "user" });
  const navigate = useNavigate();


  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const response = await fetch("http://localhost:3000/api/auth1/signup", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });
      const data = await response.json();
      if (response.ok) {
        if (data.role === "user" && data.token && data.userId) {
          setCookie("token", data.token, 1);
          setCookie("role", data.role, 1);
          console.log("Navigating now...");
          navigate("/", { replace: true });
        } else {
          console.log("Signup returned unexpected data:", data);
          alert("Signup succeeded but role/token is missing");
        }
      } else {
        
        alert("Error: " + (data.message || "Unknown error"));
      }
    } catch (error) {
      alert("Network error: " + error.message);
    }
  };


  return (
    <div className="flex items-center justify-center h-150 bg-gradient-to-r from-purple-500 to-blue-300 px-4 hover:from-blue-300 hover:to-purple-500 transition duration-500">

      <div className="flex flex-col flex-row bg-white rounded-r-lg rounded-l-lg shadow-lg  w-300">
        <div className="w-1/2 p-8 rounded-l-lg">
          <h2 className="text-3xl font-bold mb-6 text-center md:text-left rounded">Create Account</h2>

          <form onSubmit={handleSubmit}>
            <div className="mb-4">
              <input
                type="text"
                name="name"
                placeholder="Full Name"
                value={form.name}
                onChange={handleChange}
                className="w-full border-b-2 border-gray-300 outline-none py-2"
                required
              />
            </div>

            <div className="mb-4">
              <input
                type="email"
                name="email"
                placeholder="Email"
                value={form.email}
                onChange={handleChange}
                className="w-full border-b-2 border-gray-300 outline-none py-2"
                required
              />
            </div>

            <div className="mb-6">
              <input
                type="password"
                name="password"
                placeholder="Password"
                value={form.password}
                onChange={handleChange}
                className="w-full border-b-2 border-gray-300 outline-none py-2"
                required
              />
            </div>
            <button
              type="submit"
              className="w-full py-2 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 text-white font-semibold hover:from-blue-500 hover:to-purple-500 transition duration-300"
            >
              Sign Up
            </button>
          </form>
        </div>
        <div className="w-1/2 bg-gradient-to-tr from-purple-500 to-blue-500 text-white flex flex-col justify-center p-8 rounded-r-lg">
          <h2 className="text-3xl font-bold mb-4">Welcome </h2>
          <p className="mb-4">
            Join our community and start your journey with us. We provide amazing features and
            a smooth experience to help you achieve your goals.
          </p>
          <p>Already have an account? <a href="/LoginForm" className="underline font-semibold">Log In</a></p>
        </div>

      </div>
    </div>
  );
}
