const express = require("express");
const router = express.Router();
const bcrypt = require("bcrypt");
const jwt = require("jsonwebtoken"); 
const { User } = require("../models"); 

router.post("/signup", async (req, res) => {
  try {
    const { name, email, password, role } = req.body;

    const existingUser = await User.findOne({ where: { email } });
    if (existingUser)
      return res.status(400).json({ message: "Email already used" });

    const hashedPassword = await bcrypt.hash(password, 10);

    const user = await User.create({
      name,
      email,
      password: hashedPassword,
      role
    });
    const token = jwt.sign(
      { id: user.id, role: user.role },
      "your_secret_key", 
      { expiresIn: "1d" }
    );
    const refreshToken = jwt.sign(
      { id: user.id, role: user.role },
      "your_refresh_secret_key",  
      { expiresIn: "7d" }
    );

    
    res.status(201).json({
      message: "User created successfully",
      userId:user.id,
      token: token,
      refreshToken: refreshToken,
      role: user.role,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: "Server error" });
  }
});

module.exports = router;
