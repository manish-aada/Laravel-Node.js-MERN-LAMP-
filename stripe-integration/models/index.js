const fs = require('fs');
const path = require('path');
const { Sequelize, DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const models = {};
models.SubscriptionPlan = require('./SubscriptionPlan')(sequelize, DataTypes);

fs.readdirSync(__dirname)
	.filter(file => file.endsWith('.js') && file !== 'index.js')
	.forEach(file => {
		const model = require(path.join(__dirname, file))(sequelize, DataTypes);
		models[model.name] = model;
	});

models.sequelize = sequelize; 
models.Sequelize = Sequelize; 

module.exports = models;
