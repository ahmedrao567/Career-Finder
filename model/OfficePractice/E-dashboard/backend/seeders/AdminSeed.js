const bcrypt = require("bcrypt");
const User= require("../models/User"); 

async function seedAdmin() {
  try {
    const hashedPassword = await bcrypt.hash("admin123", 10);

    const [user, created] = await User.findOrCreate({
      where: { email: "admin@example.com" },
      defaults: {
        name: "Super Admin",
        email: "admin@example.com",
        password: hashedPassword,
        role: "admin",
      },
    });
    if (created) {
      console.log("Admin user created successfully!");
    } else {
      console.log("Admin user already exists.");
    }

    process.exit();
  } catch (err) {
    console.error("Error creating admin:", err);
    process.exit(1);
  }
}

seedAdmin();
