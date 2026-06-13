module.exports = {
  async up(queryInterface, Sequelize) {
    await queryInterface.addColumn('User', 'role', {
      type: Sequelize.STRING,
      allowNull: true
    });
  },

  async down(queryInterface, Sequelize) {
    await queryInterface.removeColumn('User', 'role');
  }
};
