const express = require('express');
const router = express.Router();
const db = require('../config/db'); // Ensure this path is correct
const multer = require('multer');
const path = require('path');

// Configure multer for file uploads
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, path.join(__dirname, '../public/profile_pictures')); // Ensure correct path
    },
    filename: (req, file, cb) => {
        const ext = path.extname(file.originalname);
        cb(null, `${Date.now()}${ext}`); // Create a unique filename
    }
});

const upload = multer({ storage: storage });

// Middleware to ensure user is authenticated
function isAuthenticated(req, res, next) {
    if (req.isAuthenticated()) {
        return next();
    }
    res.status(401).json({ success: false, message: 'Unauthorized' });
}

// Profile endpoint
router.get('/', isAuthenticated, (req, res) => {
    const userId = req.user.id; // req.user should be populated if user is authenticated
    db.query('SELECT name, email FROM users WHERE id = ?', [userId], (err, results) => {
        if (err) {
            console.error('Database query error:', err);
            return res.status(500).json({ success: false, message: 'Database error' });
        }
        if (results.length > 0) {
            res.json({ success: true, user: results[0] });
        } else {
            res.status(404).json({ success: false, message: 'User not found' });
        }
    });
});

// Update profile endpoint
router.post('/update', isAuthenticated, upload.single('profilePicture'), (req, res) => {
    const userId = req.user.id; // req.user should be populated if user is authenticated
    const { name, email } = req.body;
    const profilePicture = req.file ? req.file.filename : null;

    if (!name || !email) {
        return res.status(400).json({ success: false, message: 'Name and email are required.' });
    }

    // Prepare the query parameters
    const queryParams = [name, email,  userId];
    let query = 'UPDATE users SET name = ?, email = ?';

    // Include profile picture update if a new picture is uploaded

    query += ' WHERE id = ?';

    db.query(query, queryParams, (err, results) => {
        if (err) {
            console.error('Database update error:', err);
            return res.status(500).json({ success: false, message: 'Database error' });
        }
        res.json({ success: true, message: 'Profile updated successfully!' });
    });
});

module.exports = router;
