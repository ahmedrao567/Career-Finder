const express = require("express");
const router = express.Router();
const jwt = require("jsonwebtoken");
router.post("/", (req, res) => {
  const { refreshToken } = req.body;
  if (!refreshToken)
    return res.status(401).json({ message: "No refresh token provided" });

  try {
    const decode = jwt.verify(refreshToken, "MY_REFRESH_SECRET_KEY");
    const newAccessToken = jwt.sign(
      { id: decode.id, email: decode.email },
      "MY_SECRET_KEY",
      { expiresIn: "1h" }
    );
    res.json({ token: newAccessToken });
  } catch (err) {
    res.status(401).json({ message: "Invalid refresh token" });
  }
});
module.exports = router;