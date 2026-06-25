<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["test_file"]["name"]);
    
    if (move_uploaded_file($_FILES["test_file"]["tmp_name"], $target_file)) {
        $message = "File uploaded successfully!";
    } else {
        $message = "Upload failed. Check folder permissions.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test File Upload</h2>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="test_file" class="form-control mb-3">
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
        
        <hr>
        <h4>Current Files in Uploads:</h4>
        <?php
        $files = glob("uploads/*");
        if (count($files) > 0) {
            echo "<ul>";
            foreach ($files as $file) {
                echo "<li>" . basename($file) . " - " . filesize($file) . " bytes</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No files found.</p>";
        }
        ?>
    </div>
</body>
</html>