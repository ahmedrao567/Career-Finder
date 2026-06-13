import React from 'react';
import { useForm } from 'react-hook-form';

export default function ReactHookForm() {

  const { register, handleSubmit, formState: { errors } } = useForm();

  const onSubmit = (data) => {
    console.log(data);
    
  };

  return (
    <div>
      <form onSubmit={handleSubmit(onSubmit)}>
        <input 
          type='text' 
          placeholder='Name' 
          {...register("name", { 
            required: "Name is required", 
            minLength: { value: 3, message: "Name must be at least 3 characters" } 
          })} 
        />
        {errors.name && <p>{errors.name.message}</p>}

        <input 
          type='email' 
          placeholder='Email' 
          {...register("email", { 
            required: "Email is required", 
            pattern: { value: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/, message: "Invalid email address" } 
          })} 
        />
        {errors.email && <p>{errors.email.message}</p>}

        <input 
          type='password' 
          placeholder='Password' 
          {...register("password", { 
            required: "Password is required", 
            pattern: { value: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/, message: "Password must be at least 8 characters long and contain both letters and numbers" } 
          })} 
        />
        {errors.password && <p>{errors.password.message}</p>}

        <button type='submit'>Submit</button>
      </form>
    </div>
  );
}
