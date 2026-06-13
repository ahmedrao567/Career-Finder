"use client";
import {  useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from '/vite.svg'
import { useSelector, useDispatch } from 'react-redux';
import { increment } from './counter/counterSlice.js';
import { setName } from './form/formSlice.js';
import './App.css'
import { addTodo } from './todo/todoSlice.js';
import { deleteTodo } from './todo/todoSlice.js';
import { incrementCounter } from './counter/counterThunk.js';




function App() {

  const [name, setname] = useState("");
  const [desc, setdesc] = useState("")
 const todo = useSelector((state) => state.todo)
 



 const { value, loading } = useSelector((state) => state.counter);

  const form = useSelector((state) => state.form);
  const dispatch = useDispatch();
  console.log("component rendered");
  function handleFormSubmit(e) {
    e.preventDefault();
    console.log("Form submitted with name:")
  }
  function handletodosubmit(e) {
    e.preventDefault();
    dispatch(addTodo({ name, desc }));
    setname(" ");
    setdesc(" ");
  }
  return (
    <>
      <div>
        <div>
          <button
           onClick={() => dispatch(incrementCounter())}
          >
            {loading ? "Loading..." : "Increment after 2s"}
          </button>
          <span >{value}</span>
          <h1>Hello Vite + React!</h1>
          <button
            aria-label="Decrement value"

          >
            Decrement
          </button>
        </div>
        <form onSubmit={handleFormSubmit}>
          <input type="text" placeholder='Name' value={form.name} onChange={(e) => dispatch(setName(e.target.value))} />
          <button type='submit'>Submit</button>
        </form>
      </div>


      <div>
        <h1>
          todo will be down here
        </h1>
        <form action="" onSubmit={handletodosubmit} >
          <input type="text" value={name} onChange={(e) => setname(e.target.value)} placeholder='title'/>
          <input type="text" value={desc} onChange={(e) => setdesc(e.target.value)} placeholder='enter desc' />
          <button type='submit'>save</button>
        </form>
        <div>
          <ul >
            {
              todo.map((t) => (
                <li key={t.id}>
                  {t.name} - {t.desc}
                  <button onClick={() => dispatch(deleteTodo(t.id))}> delete</button>
                </li>
              ))
            }


          </ul>
        </div>


      </div>
    
     
    </>
  )
}

export default App
