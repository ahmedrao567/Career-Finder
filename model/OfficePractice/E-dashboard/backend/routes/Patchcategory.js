const router = require("express").Router();
const Category = require("../models/Category");
router.patch("/:id", async (req, res) => {
    try {
        const { id } = req.params;
        const { title } = req.body;
        const category = await Category.findByPk(id);
        if (!category) {
            return res.status(404).json({ message: "Category not found" });
        }
        category.title = title || category.title;
        await category.save();
        res.json(category);
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Server error" });
    }
});
module.exports = router;