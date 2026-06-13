"use strict";

module.exports = {
    async up(queryInterface,sequelize) {
        await queryInterface.createTable("User",{
            id: {
                allowNull: false,
                autoIncrement: true, 
                primaryKey: true,
                type: sequelize.INTEGER
            },
            name: {
                type: sequelize.STRING,
                allowNull: false
            },
            email: {
                type: sequelize.STRING,
                allowNull: false,
                unique: true
            },
            password: {
                type: sequelize.STRING,
                allowNull: false
            },
            createdAt: {
                allowNull: false,
                type: sequelize.DATE
            },
            updatedAt: {
                allowNull: false,
                type: sequelize.DATE
            }
        })
    },

    async down(queryInterface, sequelize) {
        await queryInterface.dropTable("Users");
    }
};