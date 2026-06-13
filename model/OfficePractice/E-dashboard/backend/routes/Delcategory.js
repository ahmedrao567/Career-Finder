const express = require("express");
const router = express.Router();
const Category = require("../models/Category");


router.delete("/:id", async (req, res) => {
  try {
    const { id } = req.params;
    const deleted = await Category.destroy({ where: { id } });
    if (deleted) {
      res.json({ message: "Category deleted successfully" });
    } else {
        res.status(404).json({ message: "Category not found" });
    }
    } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Server error" });
    }
});

module.exports = router;