#!/bin/bash

echo "🚀 Setting up Quiz Feature..."
echo ""

# Create upload directory
echo "📁 Creating upload directory..."
mkdir -p public/uploads/quiz_resources
chmod -R 777 public/uploads/quiz_resources
echo "✓ Upload directory created"
echo ""

# Install database tables
echo "💾 Installing database tables..."
php install_quiz_feature.php
echo ""

echo "✅ Quiz feature setup complete!"
echo ""
echo "📝 You can now:"
echo "   1. Access quizzes at: http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/quiz/list.php"
echo "   2. Create a quiz by clicking '📝 Quizzes' in the navigation"
echo "   3. View quiz results on your profile dashboard"
echo ""
echo "📖 For more information, see QUIZ_FEATURE_GUIDE.md"
