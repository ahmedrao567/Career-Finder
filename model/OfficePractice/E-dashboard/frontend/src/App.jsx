import './App.css'
import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Nav from './components/Nav'
import Footer from './components/Footer'
import Signup from './components/Signup'
import LoginForm from './components/LoginForm'
import Logout from './components/Logout'
import Dashboard from './components/Dashboard'
import ProtectedRoute from './components/ProtectedRoute'

function App() {
  return (
    <>
      <BrowserRouter>
        <Nav />
        <Routes >
          <Route
          path="/"
          element={
            <ProtectedRoute>
              <h1>Home Page</h1>
            </ProtectedRoute>
          }
        />
          <Route path="Logout" element={<Logout />} />  
           <Route
          path="/Dashboard"
          element={
            <ProtectedRoute adminOnly={true}>
              <Dashboard />
            </ProtectedRoute>
          }
        />
        
          <Route path="Signup" element={<Signup/>} />
          <Route path="LoginForm" element={<LoginForm/>} />
        </Routes>
        {/* <Footer/> */}
      </BrowserRouter>
      
    </>

  )
}

export default App
