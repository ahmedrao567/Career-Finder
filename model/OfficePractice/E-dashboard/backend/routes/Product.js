const express = require("express");
const router = express.Router();
const { Product, Category } = require("../models");
const ProductCategorypivot = require("../migrations/ProductCategorypivot");
router.post("/", async (req, res) => {
  try {
    const { title, price, userId, categoryIds } = req.body; 

    if (!title || !price || !userId) {
      return res.status(400).json({ error: "title, price, and userId are required" });
    }

    const newProduct = await Product.create({ title, price, userId });
    if (categoryIds && categoryIds.length > 0) {
      await newProduct.addCategories(categoryIds);
    }
    const productWithCategories = await Product.findByPk(newProduct.id, {
      include: {
        model: Category,
        through: { attributes: [] }, 
      },
    });

    res.status(201).json(productWithCategories);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Server error" });
  }
});


router.get("/", async (req, res) => {
  try {
    const categoryId = req.query.categoryId;
    const page = parseInt(req.query.page) || 1;
    const limit = parseInt(req.query.limit) || 5 ;
    const offset = (page - 1) * limit;

   
    let include = {
      model: Category,
      through: { attributes: [] },
    };

    if (categoryId) {
      include.where = { id: categoryId };
    }

    const products = await Product.findAll({
      include,
      limit,
      offset,
      
    });

    res.json(products);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Server error" });
  }
});

module.exports = router;
