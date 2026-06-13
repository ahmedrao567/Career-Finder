import { createSlice } from "@reduxjs/toolkit";
let id =0;
const todoSlice = createSlice({
  name: "todo",
  initialState : [],
  reducers: {
    addTodo: (state, action) => {
      const { name, desc } = action.payload;
      if (!Array.isArray(state)) {
        state = [];
      }
      state.push({ id: id++, name, desc });
    },
    deleteTodo: (state, action) => {
      return state.filter(todo => todo.id !== action.payload);
    },
},
});

export const { addTodo, deleteTodo } = todoSlice.actions;
export default todoSlice.reducer;


