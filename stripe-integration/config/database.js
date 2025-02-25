require('dotenv').config();
const { Sequelize } = require('sequelize');
const sequelize = new Sequelize(
	process.env.DB_DATABASE, 
	process.env.DB_USERNAME, 
	process.env.DB_PASSWORD, 
	{
		host: process.env.DB_HOST, 
		dialect: 'mysql', 
		logging: console.log, 
		define: {
			timestamps: true, 
			underscored: true, 
		},
	}
);

module.exports = sequelize;
