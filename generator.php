<?php
// PHP logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $keyword_to_replace = $_POST['keyword_to_replace'];
    $permalink_structure = $_POST['permalink_structure'];
    $keywords = explode("\n", trim($_POST['keywords']));
    $title_structure = $_POST['title_structure'];

    $index_content = file_get_contents('index.html');
    $created_pages = [];

    foreach ($keywords as $keyword) {
        $keyword = trim($keyword);
        if (empty($keyword)) continue;

        $new_content = str_replace($keyword_to_replace, $keyword, $index_content);
        $new_title = str_replace('[city]', $keyword, $title_structure);
        $new_content = str_replace('<title>.*?</title>', "<title>$new_title</title>", $new_content);

        $permalink = str_replace('[city]', strtolower(str_replace(' ', '-', $keyword)), $permalink_structure);
        $filename = "locations/$permalink.html";
        file_put_contents($filename, $new_content);
        $created_pages[] = $permalink;
    }

    // Generate locations.html
    $locations_content = "<html><head><title>Locations</title></head><body><h1>Our Locations</h1><div class='locations-grid'>";
    foreach ($created_pages as $page) {
        $locations_content .= "<div class='location-box'><a href='$page.html'>$page</a></div>";
    }
    $locations_content .= "</div></body></html>";
    file_put_contents('locations/locations.html', $locations_content);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
        }
        form {
            background: #f4f7f8;
            border-radius: 8px;
            padding: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        textarea {
            height: 100px;
        }
        input[type="submit"] {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
        .result {
            margin-top: 20px;
            padding: 10px;
            background-color: #e8f6fe;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Page Generator</h1>
    <form method="post" action="">
        <label for="keyword_to_replace">Keyword to replace:</label>
        <input type="text" id="keyword_to_replace" name="keyword_to_replace" placeholder="e.g. [city]" required>

        <label for="permalink_structure">Permalink structure:</label>
        <input type="text" id="permalink_structure" name="permalink_structure" placeholder="e.g. roofers-in-[city]" required>

        <label for="keywords">Keywords (one per line):</label>
        <textarea id="keywords" name="keywords" placeholder="Enter keywords, one per line" required></textarea>

        <label for="title_structure">Title structure:</label>
        <input type="text" id="title_structure" name="title_structure" placeholder="e.g. Roofers in [city]" required>

        <input type="submit" value="Generate Pages">
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($created_pages)): ?>
    <div class="result">
        <h2>Generated Pages:</h2>
        <ul>
            <?php foreach ($created_pages as $page): ?>
                <li><?php echo htmlspecialchars($page); ?>.html</li>
            <?php endforeach; ?>
        </ul>
        <p>locations.html has been generated with links to all created pages.</p>
    </div>
    <?php endif; ?>
</body>
</html>
