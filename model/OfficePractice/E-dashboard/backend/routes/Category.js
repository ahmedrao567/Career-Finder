const express = require("express");
const router = express.Router();
const { Category } = require("../models"); 
router.post("/add", async (req, res) => {
  const {  title } = req.body;
  const newCategory = await Category.create({ title });

  if (!title) {
    return res.status(400).json({ error: "Title is required" });
  }
    res.status(201).json(newCategory);  

});
router.get("/", async (req, res) => {
  try {
    const page = parseInt(req.query.page) || 1;
    const limit = parseInt(req.query.limit) || 5;

    const offset = (page - 1) * limit;

    const categories = await Category.findAll({
      limit,
      offset
    });
    res.json(categories);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});




module.exports = router;
