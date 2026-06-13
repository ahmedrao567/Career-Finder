import { createAsyncThunk } from "@reduxjs/toolkit";


export const incrementCounter = createAsyncThunk(
    "counter/incrementCounter",

    async ()=>{
        await new Promise((resolve)=>setTimeout(resolve,2000))
        return 1;
    }
)