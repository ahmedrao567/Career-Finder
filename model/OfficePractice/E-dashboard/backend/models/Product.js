const {DataTypes} = require("sequelize");
const sequelize = require("../db");


const Product = sequelize.define("Products", {
    id: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
    },
    title: {
        type: DataTypes.STRING,
        allowNull: false,
    },
    price: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    userId: {
        type: DataTypes.INTEGER,
        allowNull: false,
    }
}, 
);


module.exports = Product;
