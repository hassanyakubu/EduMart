# Admin Resource Upload Guide for EduMart

## How to Upload Resources as Admin

### Step 1: Log in as Admin
1. Go to: `http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/auth/login.php`
2. Log in with your admin credentials
3. You'll be redirected to the Admin Dashboard

### Step 2: Access Upload Page
From the navigation menu, click **"Upload"** or go directly to:
`http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/resources/upload.php`

### Step 3: Fill in Resource Details

#### Required Fields:

1. **Resource Title**
   - Example: "WASSCE Mathematics Past Questions 2020-2024"
   - Example: "Core Mathematics Notes - SHS 1"
   - Example: "Integrated Science Practical Guide"

2. **Category** (Choose from existing):
   - SHS Mathematics
   - SHS Science
   - BECE English
   - University MIS
   - General Knowledge

3. **Creator** (Choose from existing):
   - Mr. Opoku (Math Tutor)
   - Approachers Series
   - Dr. K. Mensah (CS Dept)

4. **Price (₵)**
   - Example: 15.00 (for ₵15)
   - Example: 25.50 (for ₵25.50)
   - Can be 0.00 for free resources

5. **Description**
   - Detailed description of the resource
   - What students will learn
   - Topics covered
   - Example: "Complete WASSCE Mathematics past questions from 2020-2024 with detailed solutions. Covers all topics including Algebra, Geometry, Trigonometry, and Statistics."

6. **Keywords** (Optional but recommended)
   - Comma-separated tags for better search
   - Example: "wassce, mathematics, past questions, algebra, geometry"
   - Example: "shs, science, chemistry, biology, physics"

7. **Thumbnail Image**
   - Upload a cover image (JPG, PNG, GIF)
   - Recommended size: 800x600px or similar
   - This is what students see when browsing

8. **Resource File**
   - The actual learning material
   - Supported formats: PDF, DOCX, MP4, ZIP

### Step 4: Click "Upload Resource"
The system will:
- Upload both files to the server
- Create the resource in the database
- Redirect you to the resources list

---

## What Types of Resources to Upload

### 📚 Educational Materials for Ghana

#### 1. **WASSCE/BECE Past Questions**
- **Format**: PDF
- **Examples**:
  - WASSCE Mathematics 2015-2024
  - WASSCE Integrated Science 2018-2024
  - BECE English Language Past Questions
  - WASSCE Social Studies Questions
- **Price Range**: ₵10 - ₵30

#### 2. **SHS Study Notes**
- **Format**: PDF or DOCX
- **Examples**:
  - Core Mathematics Notes (SHS 1-3)
  - Elective Mathematics Complete Guide
  - Chemistry Notes with Diagrams
  - Physics Practical Manual
  - Biology Revision Notes
- **Price Range**: ₵15 - ₵40

#### 3. **Video Tutorials**
- **Format**: MP4 or ZIP (multiple videos)
- **Examples**:
  - Algebra Video Lessons (10 videos)
  - Chemistry Experiments Demonstrations
  - English Grammar Tutorials
  - Mathematics Problem-Solving Sessions
- **Price Range**: ₵25 - ₵60

#### 4. **Exam Preparation Bundles**
- **Format**: ZIP (containing multiple PDFs)
- **Examples**:
  - Complete WASSCE Prep Pack (All subjects)
  - SHS 3 Final Exam Bundle
  - BECE Complete Revision Package
- **Price Range**: ₵50 - ₵100

#### 5. **University Course Materials**
- **Format**: PDF, DOCX, or ZIP
- **Examples**:
  - MIS 101 Lecture Notes
  - Database Management Systems Guide
  - Business Statistics Workbook
  - Programming Fundamentals (Python/Java)
- **Price Range**: ₵20 - ₵50

#### 6. **Practice Worksheets**
- **Format**: PDF
- **Examples**:
  - 100 Mathematics Practice Problems
  - English Comprehension Exercises
  - Science Multiple Choice Questions
  - Essay Writing Practice Sheets
- **Price Range**: ₵5 - ₵15

---

## File Preparation Tips

### For PDF Files:
- Ensure text is readable and clear
- Include table of contents for long documents
- Add page numbers
- Use bookmarks for easy navigation

### For Video Files:
- Keep videos under 500MB each
- Use MP4 format (most compatible)
- Include clear audio
- Add intro/outro with your branding

### For ZIP Files:
- Organize files in folders
- Include a README.txt explaining contents
- Keep total size under 100MB if possible

### For Images (Thumbnails):
- Use eye-catching designs
- Include the title on the image
- Show what the resource is about
- Use bright, professional colors

---

## Sample Resources to Start With

### Resource 1: WASSCE Math Past Questions
- **Title**: WASSCE Mathematics Past Questions 2020-2024
- **Category**: SHS Mathematics
- **Creator**: Mr. Opoku (Math Tutor)
- **Price**: ₵25.00
- **Description**: Complete collection of WASSCE Mathematics past questions from 2020-2024 with detailed step-by-step solutions. Perfect for final year SHS students preparing for their exams.
- **Keywords**: wassce, mathematics, past questions, shs, exam prep
- **Files**: PDF document + Cover image

### Resource 2: Core Math Notes
- **Title**: Core Mathematics Complete Notes - SHS 1 to 3
- **Category**: SHS Mathematics
- **Creator**: Approachers Series
- **Price**: ₵35.00
- **Description**: Comprehensive mathematics notes covering all topics from SHS 1 to SHS 3. Includes examples, practice problems, and exam tips.
- **Keywords**: core mathematics, shs notes, algebra, geometry, trigonometry
- **Files**: PDF document + Cover image

### Resource 3: Science Practical Guide
- **Title**: Integrated Science Practical Manual
- **Category**: SHS Science
- **Creator**: Dr. K. Mensah (CS Dept)
- **Price**: ₵20.00
- **Description**: Step-by-step guide for all SHS science practicals. Includes diagrams, safety procedures, and expected results.
- **Keywords**: science, practical, laboratory, experiments, shs
- **Files**: PDF document + Cover image

---

## After Uploading

### What Happens Next:
1. Resource appears on the homepage (if featured)
2. Students can browse and view it in the resources list
3. Students must log in to view details or purchase
4. After purchase, students can download the files
5. Students can leave reviews and ratings

### Managing Resources:
- Go to Admin Dashboard to view all resources
- Edit or delete resources as needed
- Monitor sales and downloads
- Check reviews and ratings

---

## File Storage Location
Uploaded files are stored in:
- **Images**: `public/uploads/images/`
- **Files**: `public/uploads/files/`

Make sure these directories have write permissions (777).

---

## Need More Categories or Creators?

If you need to add more categories or creators, you can:
1. Go to Admin Dashboard
2. Use the "Manage Categories" or "Manage Creators" options
3. Or directly insert into the database:

```sql
-- Add new category
INSERT INTO categories (cat_name) VALUES ('BECE Mathematics');

-- Add new creator
INSERT INTO creators (creator_name, created_by) VALUES ('Prof. Ama Serwaa', 11);
```

---

## Quick Checklist Before Uploading
- [ ] Resource file is ready and tested
- [ ] Thumbnail image is attractive and clear
- [ ] Title is descriptive and searchable
- [ ] Description explains what students will get
- [ ] Price is competitive and fair
- [ ] Keywords are relevant for search
- [ ] Files are virus-free and safe
- [ ] Content is original or properly licensed
