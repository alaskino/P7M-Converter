<?php
/**
 * File Name: index.php
 * Description:
 * This PHP script handles file uploads by users, processes them through an external bash script, and provides an interface to download the converted files.
 * It uses a unique directory management system for each user to ensure isolated processing.
 * It bypasses paid programs that allow you to extract multiple files simultaneously. The project is open source.
 *
 * Created by: Mathieu Licata
 * Creation Date: 03/11/2024
 *
 * Key Features:
 * - Allows users to upload multiple files through an HTML form.
 * - Executes an external bash script to convert the uploaded files.
 * - Enables downloading of the converted files, either individually or as a ZIP archive.
 * - Automatically removes temporary files and directories after download to keep the system clean.
 *
 * Requirements:
 * - Server with PHP support.
 * - Write permissions on the 'uploads/' and 'output/' directories.
 * - Properly configured bash script "script_conv.sh".
 *
 * Note:
 * - This script does not implement advanced security mechanisms such as input sanitization or file type validation.
 *   It is recommended to add such controls before using the system in production.
 *
 * Disclaimer:
 * - This script is provided "as is," and the author assumes no responsibility for any damages caused by its use.
 */

$uploadDir = 'uploads/';
$outputDir = 'output/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $uniqueId = uniqid(); //Unique ID for users.
    $userUploadDir = $uploadDir . $uniqueId . '/';
    mkdir($userUploadDir, 0777, true);
    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        $fileName = basename($_FILES['files']['name'][$key]);
        move_uploaded_file($tmp_name, $userUploadDir . $fileName);
    }
    $outputUserDir = $outputDir . $uniqueId . '/';
    mkdir($outputUserDir, 0777, true);
    shell_exec('debian -c "/home/XX/process_p7m.sh /mnt/c/XAMPP/htdocs/XX/' . $userUploadDir . ' /mnt/c/XAMPP/htdocs/XX/' . $outputUserDir . '"');

    header("Location: ?action=download&folder=$outputUserDir&uploadDir=$userUploadDir");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'download' && isset($_GET['folder'])) {
    $folder = $_GET['folder'];
    $files = array_diff(scandir($folder), ['.', '..']);
    $userUploadDir = $_GET['uploadDir'];
    if (isset($_POST['download'])) {
        $selectedFiles = isset($_POST['files']) ? $_POST['files'] : $files;
        $downloadAll = ($_POST['download'] === 'all');
        if ($downloadAll) {
            $zip = new ZipArchive();
            $zipFile = $folder . "/File_Convertiti.zip";

            if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
                foreach ($files as $file) {
                    $zip->addFile($folder . '/' . $file, $file);
                }
                $zip->close();
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="all_files.zip"');
                header('Content-Length: ' . filesize($zipFile));
                readfile($zipFile);
                deleteDir($folder);
                deleteDir($userUploadDir);
                unlink($zipFile);
                header("Location: /"); // Redirect to homepage
                exit();
            }
        } else {
            foreach ($selectedFiles as $file) {
                $filePath = $folder . '/' . $file;
                if (file_exists($filePath)) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                    header('Content-Length: ' . filesize($filePath));
                    readfile($filePath);
                }
            }
            deleteDir($folder);
            deleteDir($userUploadDir);
            header("Location: /"); // Redirect to homepage
            exit();
        }
    }
}

function deleteDir($dirPath) {
    if (!is_dir($dirPath)) { return; }
    $files = array_diff(scandir($dirPath), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $dirPath . '/' . $file;
        is_dir($filePath) ? deleteDir($filePath) : unlink($filePath);
    }
    rmdir($dirPath);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="pdf_icon.png">
    <title>Carica e Scarica File</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        h1 {
            color: #4e8c4a;
            margin-bottom: 20px;
            text-align: center;
            font-size: 2rem;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            animation: slideIn 1s ease-out;
        }

        @keyframes slideIn {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        input[type="file"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #fafafa;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="file"]:hover {
            background-color: #eaeaea;
        }

        button {
            padding: 12px 25px;
            background-color: #4e8c4a;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        button:hover {
            background-color: #3b6f39;
        }

        button:active {
            transform: scale(0.98);
        }

        button:disabled {
            background-color: #c1c1c1;
            cursor: not-allowed;
        }

        .file-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            background-color: #f8f8f8;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            animation: fadeInItem 0.5s ease-out;
        }

        @keyframes fadeInItem {
            0% { opacity: 0; transform: translateX(-30px); }
            100% { opacity: 1; transform: translateX(0); }
        }

        .file-item input {
            margin-right: 10px;
        }

        .file-item label {
            font-size: 1rem;
            color: #555;
        }

        @media (max-width: 600px) {
            body {
                padding: 20px;
            }

            h1 {
                font-size: 1.5rem;
            }

            form {
                width: 100%;
                max-width: 400px;
            }

            button {
                width: 100%;
            }
        }

        footer {
            margin-top: 50px;
            font-size: 1.2rem;
            color: #4e8c4a;
            animation: slideUp 1s ease-out;
            text-align: center;
            font-weight: bold;
        }

        footer p {
            margin: 0;
        }

        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <?php if (!isset($_GET['action'])): ?>
        <h1>Welcome! Upload yours files!</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="files[]" multiple>
            <button type="submit">Upload</button>
        </form>
    <?php elseif ($_GET['action'] === 'download' && isset($files)): ?>
        <h1>Select what you want to downloads</h1>
        <form action="" method="post">
            <?php foreach ($files as $file): ?>
                <div>
                    <input type="checkbox" name="files[]" value="<?= $file ?>" checked>
                    <label><?= $file ?></label>
                </div>
            <?php endforeach; ?>
            <button type="submit" name="download" value="selected">Download selected</button>
            <button type="submit" name="download" value="all">Download all</button>
        </form>
    <?php endif; ?>

    <footer>
        <p>Made with ❤️ by Mathieu</p>
    </footer>
</body>
</html>

