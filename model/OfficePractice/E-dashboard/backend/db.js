const { Sequelize } = require("sequelize");

const sequelize = new Sequelize("mydb", "postgres", "1234", {
  host: "localhost",
  dialect: "postgres",
});

(async () => {
  try {
    await sequelize.authenticate();
    await sequelize.sync({ alter: true });
    console.log("Database connected!");
  } catch (error) {
    console.error("Unable to connect:", error);
  }
})();






module.exports = sequelize;
