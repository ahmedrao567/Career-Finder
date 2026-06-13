const jwt = require("jsonwebtoken");

module.exports = function (req, res, next) {
    const authHeader =  req.headers.authorization;
    if (!authHeader) {
        return res.status(401).json({ message: "No token provided" });
    }
    const token = authHeader.split(" ")[1];
    if (!token) {
        return res.status(401).json({ message: "No token provided" });
    }

    try {
        const decode = jwt.verify(token, "MY_SECRET_KEY");
        req.user = decode;
        next();
    } catch (err) {
        return res.status(401).json({ message: "Invalid token" });
    }
    
};