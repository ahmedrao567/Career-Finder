import { createSlice } from "@reduxjs/toolkit";


const form = createSlice({
    
    name: "form",
    initialState: { 
        name: "",
        email: "" },
    reducers: {
        setName : (state,action)=>{
            state.name = action.payload;
        },
    }
});

export const { setName } = form.actions;
export default form.reducer;
        