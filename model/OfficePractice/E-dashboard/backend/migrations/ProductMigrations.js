"use strict";

module.exports = {
    async up(queryInterface, Sequelize) {
        await queryInterface.createTable("Products", {
            id: {
                allowNull: false,
                autoIncrement: true,
                primaryKey: true,
                type: Sequelize.INTEGER
            },
            title: {
                type: Sequelize.STRING,
                allowNull: false
            },
            price: {
                type: Sequelize.INTEGER,
                allowNull: false
            },
            userId: {
                type: Sequelize.INTEGER,
                allowNull: false,
                references: { model: 'Users', key: 'id' }, 
                onUpdate: 'CASCADE',
                onDelete: 'CASCADE'
            },
            categoryId: {
                type: Sequelize.INTEGER,
                allowNull: false,
                references: { model: 'Categories', key: 'id' }, 
                onUpdate: 'CASCADE',
                onDelete: 'CASCADE'
            },
            createdAt: {
                allowNull: false,
                type: Sequelize.DATE
            },
            updatedAt: {
                allowNull: false,
                type: Sequelize.DATE
            }
        });
    },

    async down(queryInterface, Sequelize) {
        await queryInterface.dropTable("Products"); 
    }
};
