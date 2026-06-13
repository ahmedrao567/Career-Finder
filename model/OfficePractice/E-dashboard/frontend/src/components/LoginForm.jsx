// import React, { useState } from "react";

// export default function LoginForm() {
//   const [form, setForm] = useState({ email: "", password: "" });

//   const handleChange = (e) => {
//     setForm({ ...form, [e.target.name]: e.target.value });
//   };

//   const handleSubmit = async  (e) => {
//     e.preventDefault();

//    const response = await fetch ("http://localhost:3000/api/auth/login", {
//     method:"POST",
//     headers: { "Content-Type": "application/json" },
//     body: JSON.stringify(form),
//    });  
//    const data = await response.json();
//    if (response.ok){
//     alert("Login successful!", data);
//    }
//    else{
//     alert("Error: " + data.message);
//    }
//   }

//   return (
//     <div className="flex items-center justify-center h-150 bg-gradient-to-r from-purple-500 to-blue-300 px-4 hover:from-blue-300 hover:to-purple-500 transition duration-500">
//       <div className="flex flex-col flex-row bg-white rounded-lg shadow-lg w-300">

//         <div className="w-1/2 p-8 rounded-l-lg">
//           <h2 className="text-4xl font-bold mb-6 text-center text-left">Login</h2>

//           <form onSubmit={handleSubmit}>
//             <div className="mb-4">
//               <input
//                 type="email"
//                 name="email"
//                 placeholder="Email"
//                 value={form.email}
//                 onChange={handleChange}
//                 className="w-full border-b-2 border-gray-300 outline-none py-2"
//                 required
//               />
//             </div>

//             <div className="mb-6">
//               <input
//                 type="password"
//                 name="password"
//                 placeholder="Password"
//                 value={form.password}
//                 onChange={handleChange}
//                 className="w-full border-b-2 border-gray-300 outline-none py-2"
//                 required
//               />
//             </div>

//             <button
//               type="submit"
//               className="w-full py-2 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 text-white font-semibold hover:from-blue-500 hover:to-purple-500 transition duration-300"
//             >
//               Log In
//             </button>
//           </form>
//         </div>

//         <div className="w-1/2 bg-gradient-to-tr from-purple-500 to-blue-500 text-white flex flex-col justify-center p-8 rounded-r-lg">
//           <h2 className="text-3xl font-bold mb-4">Welcome Back</h2>
//           <p className="mb-4">
//             Enter your credentials to access your account and continue your journey with us.
//           </p>
//           <p>Don't have an account? <a href="/Signup" className="underline font-semibold">Sign Up</a></p>
//         </div>

//       </div>
//     </div>
//   );
// }
import { set, useForm } from 'react-hook-form';
import { response1 } from '../apis/LoginApi';
import { useNavigate } from 'react-router-dom';
import { setCookie } from '../Utils/cookies';


export default function LoginForm() {
  const { register, handleSubmit, formState: { errors } } = useForm();
  const navigate = useNavigate();

  const onSubmit = async (data1) => {
    const data = await response1(data1);
    

    if (data.role === "admin" && data.token && data.userId) {
      setCookie("token", data.token, 1);
      setCookie("role", data.role, 1);
      setCookie("userId", data.userId, 1);
      navigate("/Dashboard");
    } else if (data.role === "user") {
      setCookie("token", data.token, 1);
      setCookie("role", data.role, 1);
      setCookie("userId", data.userId, 1);
      navigate("/");
    }


  };

  return (
    <div className="flex items-center justify-center h-150 bg-gradient-to-r from-purple-500 to-blue-300 px-4 hover:from-blue-300 hover:to-purple-500 transition duration-500">
      <div className="flex flex-col flex-row bg-white rounded-lg shadow-lg w-full max-w-4xl">

        <div className="w-full w-1/2 p-8 rounded-l-lg">
          <h2 className="text-4xl font-bold mb-6 text-left text-gray-800">Log In</h2>

          <form onSubmit={handleSubmit(onSubmit)}>
            <div className="mb-4">
              <input
                type='email'
                placeholder='Email'
                {...register("email", {
                  required: "Email is required",
                  pattern: { value: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/, message: "Invalid email address" }
                })}
                className="w-full border-b-2 border-gray-300 outline-none py-2 transition"
              />
              {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email.message}</p>}
            </div>

            <div className="mb-6">
              <input
                type='password'
                placeholder='Password'
                {...register("password", {
                  required: "Password is required",
                  pattern: { value: /^(?=.*[A-Za-z])(?=.*\d).{8,}$/, message: "Password must be at least 8 characters long and contain letters & numbers" }
                })}
                className="w-full border-b-2 border-gray-300 outline-none py-2 transition"
              />
              {errors.password && <p className="text-red-500 text-sm mt-1">{errors.password.message}</p>}
            </div>

            <button
              type='submit'
              className="w-full py-2 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 text-white font-semibold hover:from-blue-500 hover:to-purple-500 transition duration-300"
            >
              Log In
            </button>
          </form>
        </div>

        <div className="w-full w-1/2 bg-gradient-to-tr from-purple-500 to-blue-500 text-white flex flex-col justify-center p-8 rounded-r-lg">
          <h2 className="text-3xl font-bold mb-4">Welcome!</h2>
          <p className="mb-4">
            Log in to your account to continue. Enter your credentials and enjoy seamless access to all features.
          </p>
          <p>
            Don't have an account? <a href="/signup" className="underline font-semibold">Sign Up</a>
          </p>
        </div>

      </div>
    </div>
  );
}
