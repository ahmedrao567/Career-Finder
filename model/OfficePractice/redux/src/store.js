import { configureStore, combineReducers } from "@reduxjs/toolkit";
import counterReducer from "./counter/counterSlice";
import { persistStore, persistReducer } from 'redux-persist';
import storage from 'redux-persist/lib/storage';
import formReducer from "./form/formSlice";
import todoReducer from "./todo/todoSlice";

const rootReducer = combineReducers({
  counter: counterReducer,
  form: formReducer,
  todo: todoReducer,
});
const persistConfig = {
  key: 'root', 
  storage,
  whitelist: ['counter', 'todo'], 
};
const persistedReducer = persistReducer(persistConfig, rootReducer);
export const store = configureStore({
  reducer: persistedReducer,
});
export const persistor = persistStore(store);