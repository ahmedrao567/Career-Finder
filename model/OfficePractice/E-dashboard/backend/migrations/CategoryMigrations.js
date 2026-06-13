"use strict";

module.exports = {
    async up(queryInterface,sequelize) {
        await queryInterface.createTable("Category",{
            id: {
                allowNull: false,
                autoIncrement: true, 
                primaryKey: true,
                type: sequelize.INTEGER
            },
            title: {
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
        await queryInterface.dropTable("Category");
    }
};