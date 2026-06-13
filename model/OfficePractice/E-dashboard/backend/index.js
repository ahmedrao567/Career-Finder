const express = require("express");
const User = require("./models/User"); 
const signupRoute = require("./routes/SignUp");
const loginRoute = require("./routes/Login");
const userRoute = require("./routes/user"); 
const refreshTokenRoute = require("./routes/token");
const categoryRoute = require("./routes/Category");
const productRoute = require("./routes/Product");
const DelcategoryRoute = require("./routes/Delcategory");
const patchcategoryRoute = require("./routes/Patchcategory");
const cors = require("cors");
const app = express();
app.use(express.json());
app.use(cors());
app.use("/api/auth1", signupRoute);
app.use("/api/auth", loginRoute);
app.use("/api/user", userRoute);
app.use("/api/token", refreshTokenRoute);
app.use("/api/products", productRoute);
app.use("/api/delcategory", DelcategoryRoute);
app.use("/api/patchcategory", patchcategoryRoute);
app.use("/api/categories", categoryRoute);
const db = require("./models");
app.listen(3000, () => console.log("Server running on port 3000"));




// app.patch("/users/:id", async (req, res) => {
//   try {
//     const user = await Users.findByPk(req.params.id);
//     if (!user) {
//       return res.status(404).json({ message: "User not found" });
//     }
//     await user.update(req.body);

//     res.json(user);
//   } catch (err) {
//     res.status(400).json({ error: err.message });
//   }
// });
app.get("/user", async (req, res) => {
  try {
    const users = await User.findAll();  
    res.json(users);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// app.delete("/users/:id", async (req, res) => {
//   try {
//     const user = await User.findByPk(req.params.id); 
//     if (!user) {
//       return res.status(404).json({ message: "User not found" });
//     }
//     await user.destroy();
//     res.json({ message: "User deleted successfully" });
//   } catch (err) {
//     res.status(500).json({ error: err.message });
//   }
// });






// index.js
// const express = require("express");
// const app = express();

// app.use(express.json());

// // Import routes
// const authRoutes = require("./routes/authroutes");
// const userRoutes = require("./routes/userroutes");

// // Use routes
// app.use("/auth", authRoutes);
// app.use("/users", userRoutes);

// app.listen(5000, () => console.log("Server running on port 5000"));
