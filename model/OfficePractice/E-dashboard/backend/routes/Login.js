const express = require("express");
const router = express.Router();
const bcrypt = require("bcrypt");
const jwt = require("jsonwebtoken"); 
const User = require("../models/User");

router.post("/login", async (req, res) => {
  try {
    const { email, password ,role } = req.body;
    const user = await User.findOne({ where: { email } });
    if (!user)
      return res.status(400).json({ alert: "Invalid credentials" });
    const match = await bcrypt.compare(password, user.password);
    if (!match)
      return res.status(400).json({ alert: "Invalid credentials" });
    if (role && user.role !== role) {
      return res.status(403).json({ alert: "Access denied for this role" });
    }
    
    const token = jwt.sign(
      { id: user.id, email: user.email, role: user.role }, 
      "MY_SECRET_KEY",                  
      { expiresIn: "1h" }                 
    );
    const refreshToken = jwt.sign(
      { id: user.id, email: user.email, role: user.role }, 
      "MY_REFRESH_SECRET_KEY",  
      { expiresIn: "7d" }                 
    );
    res.json(
      { token, refreshToken , role: user.role ,userId: user.id},
      
    );
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Server error" });
  }
});

module.exports = router;
