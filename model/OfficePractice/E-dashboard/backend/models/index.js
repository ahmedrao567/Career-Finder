'use strict';
const sequelize = require('../db');
const db = {};
db.Category = require("./Category");
db.User = require("./User");    
db.Product = require("./Product"); 
db.User.hasMany(db.Product, { foreignKey: "userId" });
db.Product.belongsTo(db.User, { foreignKey: "userId" });
db.Product.belongsToMany(db.Category, {
    through: "ProductCategory",
    foreignKey: "productId",
    otherKey: "categoryId",
});

db.Category.belongsToMany(db.Product, {
    through: "ProductCategory",
    foreignKey: "categoryId",
    otherKey: "productId",
});
db.sequelize = sequelize;
module.exports = db;
