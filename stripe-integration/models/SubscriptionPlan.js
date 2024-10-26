const { Model, DataTypes } = require('sequelize');

module.exports = (sequelize) => {
	class SubscriptionPlan extends Model {}

	SubscriptionPlan.init({
		name: {
			type: DataTypes.STRING,
			allowNull: false,
		},
		stripe_product_id: {
			type: DataTypes.STRING,
			allowNull: false,
			unique: true,
		},
		stripe_price_id: {
			type: DataTypes.STRING,
			allowNull: false,
		},
		interval: {
			type: DataTypes.STRING,
			allowNull: false,
		},
		amount: {
			type: DataTypes.DECIMAL(8, 2),
			allowNull: false,
		},
		currency: {
			type: DataTypes.STRING(3),
			allowNull: false,
		},
		created_at: {
			type: DataTypes.DATE,
			defaultValue: DataTypes.NOW,
			allowNull: true,
		},
		updated_at: {
			type: DataTypes.DATE,
			defaultValue: DataTypes.NOW,
			allowNull: true,
		},
	}, {
		sequelize,
		modelName: 'SubscriptionPlan',
		underscored: true, 
	});

	return SubscriptionPlan; 
};
