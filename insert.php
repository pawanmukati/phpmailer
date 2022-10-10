<?php
 include 'db_connect.php'
 ?>

<?php

error_reporting(E_ERROR);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    die();
}

 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Bad Request!.Only POST method is allowed',
    ]);
    exit;
endif;
 
require 'db_connect.php';
$database = new Operations();
$conn = $database->dbConnection();
 
$data = json_decode(file_get_contents("php://input"));
 

if ($result = $conn->query($data))
{ echo json_encode("New record created successfully");
   // echo "New record created successfully";
} else {
    echo json_encode("Some error");
   // echo "Error: " . $sql . "<br>" . $conn->error;
}
if (!isset($data->firstName) || !isset($data->lastName) || !isset($data->email)) :
 
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fileds |  First Name, Last Name and Email',
    ]);
    exit;
 
elseif (empty(trim($data->firstName)) || empty(trim($data->lastName)) || empty(trim($data->email))) :
 
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
 
endif;
 
try {
 
    $firstName = htmlspecialchars(trim($data->firstName));
    $lastName = htmlspecialchars(trim($data->lastName));
    $email = htmlspecialchars(trim($data->email));
    $number = htmlspecialchars(trim($data->number));
    $company = htmlspecialchars(trim($data->company));
    $message = htmlspecialchars(trim($data->message));
 
    $query = "INSERT INTO `contact_form`(
    firstName,
    lastName,
    email,
    number,
    company,
    message,
    ) 
    VALUES(
    :firstName,
    :lastName,
    :email,
    :number,
    :company,
    :message,
    )";
 
    $stmt = $conn->prepare($query);
 
    $stmt->bindValue(':firstName', $firstName, PDO::PARAM_STR);
    $stmt->bindValue(':lastName', $lastName, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':number', $number, PDO::PARAM_STR);
    $stmt->bindValue(':company', $company, PDO::PARAM_STR);
    $stmt->bindValue(':message', $message, PDO::PARAM_STR);
    

    if ($stmt->execute()) {
 
        http_response_code(201);
        echo json_encode([
            'success' => 1,
            'message' => 'Data Inserted Successfully.'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => 0,
        'message' => 'There is some problem in data inserting'
    ]);
    exit;
 
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
    
}
class controller{
    public function sendemail($contactData)
    {
        $message = '<p>Hi, <br />Some one has submitted contact form.</p>';
        $message .= '<p><strong>Name: </strong>'.$contactData['firstName'].$contactData['lastName'].'</p>';
        $message .= '<p><strong>Email: </strong>'.$contactData['email'].'</p>';
        $message .= '<p><strong>Phone: </strong>'.$contactData['number'].'</p>';
        $message .= '<p><strong>Company: </strong>'.$contactData['company'].'</p>';
        $message .= '<p><strong>Message: </strong>'.$contactData['message'].'</p>';
        $message .= '<br />Thanks';

        $this->load->library('email');

        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';

        $this->email->initialize($config);

        $this->email->from('pawan.mukati@newtechfusion.com', 'NTF@pawan12345');
        $this->email->to('pawan.mukati@newtechfusion.com');

        $this->email->subject('Contact Form');
        $this->email->message($message);

        $this->email->send();
    }
}