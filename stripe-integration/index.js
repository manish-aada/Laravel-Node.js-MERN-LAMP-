require('dotenv').config();
const express = require('express');
const axios = require('axios');
const stripe = require('stripe')(process.env.STRIPE_SECRET_KEY);
const cron = require('node-cron');
const { sequelize, SubscriptionPlan } = require('./models');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());

async function connectToDatabase() {
	try {
		await sequelize.authenticate(); 
		console.log('MySQL Connected...');
	} catch (error) {
		console.error('Unable to connect to the database:', error);
	}
}


async function retrieveAndInsertProducts() {
	try {
		const products = await stripe.products.list();
		const prices = await stripe.prices.list();

		for (const product of products.data) {
			const price = prices.data.find(price => price.product === product.id);

			const productData = {
				name: product.name,
				stripe_product_id: product.id, 
				stripe_price_id: price ? price.id : null, 
				interval: price ? price.recurring?.interval : null, 
				amount: price ? (price.unit_amount / 100).toFixed(2) : null, 
				currency: price ? price.currency : null, 
			};

			
			
			await SubscriptionPlan.upsert(productData);
			//console.log(`Inserted/Updated product: ${productData.name}`);
		}
	} catch (error) {
		console.error('Error retrieving products from Stripe:', error);
	}
}


app.get('/sync-products', async (req, res) => {
	await retrieveAndInsertProducts();
	res.status(200).send('Product synchronization started in the background.');
});


async function startServer() {
	await connectToDatabase();
	await sequelize.sync(); 
	app.listen(PORT, () => {
		console.log(`Server running on port ${PORT}`);
	});
}


startServer();

// Schedule a job to run every minute
cron.schedule('* * * * *', () => {
	console.log('Running product synchronization...');
	retrieveAndInsertProducts();
});
