import { createSlice } from "@reduxjs/toolkit";
import { incrementCounter } from "./counterThunk";


const counter = createSlice({
    
    name: "counter",
    initialState: { value: 0,
         loading:false,
     },
   
    reducers: {},
    extraReducers:(builder)=>{
        builder 
        .addCase(incrementCounter.pending,(state)=>{
            state.loading=true;
        })
        .addCase(incrementCounter.fulfilled,(state,action)=>{
            state.loading=false;
            state.value += action.payload;
        })
        .addCase(incrementCounter.rejected,(state)=>{
            state.loading=false;
        })
    }
});

export const { increment } = counter.actions;
export default counter.reducer;


        