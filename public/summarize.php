<?php
require __DIR__ . '/vendor/autoload.php';

use Spatie\PdfToText\Pdf;
// Assuming $_POST is used for handling form data in the absence of Laravel's Request object
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation
    if (empty($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        die("Error: PDF file is required.");
    }

    $pdfMimeType = mime_content_type($_FILES['pdf']['tmp_name']);
    if ($pdfMimeType !== 'application/pdf') {
        die("Error: Invalid PDF file format.");
    }

    if (empty($_POST['question'])) {
        die("Error: Question is required.");
    }

    if (isset($_FILES['pdf'])) {
        $uploadDirectory = 'uploads/';  // Specify your upload directory

        $pdfFile = $_FILES['pdf'];
        $pdfFileName =  time() ."_". $pdfFile['name'] ;
        $pdfFilePath = $uploadDirectory . $pdfFileName;

        // Check if the file has been successfully uploaded
        if (move_uploaded_file($pdfFile['tmp_name'], $pdfFilePath)) {
            echo "File uploaded successfully."; 
            // Store the uploaded PDF
            
            $question = $_POST['question'];
            $fileName = $pdfFile['name'];
            
            // Extract text from the PDF
            // Assuming Pdf::getText() is a custom function for extracting text from a PDF
            $text = Pdf::getText($pdfFilePath);
            
            // OpenAI API call
    $headers = [
        'Authorization: Bearer ' . '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',
        'Content-Type: application/json',
    ];

    $postData = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => $text,
            ],
            [
                'role' => 'user',
                'content' => $question,
            ],
        ],
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    $summary = $responseData['choices'][0]['message']['content'];
     
    if (!empty($summary)) {
        // Save data to database
        $pdfChatContent = new stdClass(); // Assuming PdfChatContent is a custom class or data structure
        $pdfChatContent->file_name = $fileName;
        $pdfChatContent->file_content = $text;
        $pdfChatContent->question = $question;
        $pdfChatContent->answer = $summary;
        $pdfChatContent->created_at = date("Y-m-d H:i:s");
        $pdfChatContent->updated_at = date("Y-m-d H:i:s");

        // Save to the database (implementation depends on your database system)
        // For example, using MySQLi:
        $mysqli = new mysqli('localhost', 'admin', 'admin', 'chatgpt-demo');
        $stmt = $mysqli->prepare("INSERT INTO pdf_chat_content (file_name, file_content, question, answer,created_at,updated_at) VALUES (?, ?, ?, ?,?,?)");
        $stmt->bind_param('ssssss', $pdfChatContent->file_name, $pdfChatContent->file_content, $pdfChatContent->question, $pdfChatContent->answer,$pdfChatContent->created_at,$pdfChatContent->updated_at);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
        ?>
        <h1>OutPut</h1>
        <p><?php echo $summary;?></p>
        <?php
    }
             
            // Do further processing here, such as database insertion or additional actions.
        } else {
            echo "Error uploading file.";
        }
    }
    

    
}
?>
