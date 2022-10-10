
<?php
include 'db.connection.php';
include 'Api_model.php';
class controller{
  public function contact()
  {
    //   header("Access-Control-Allow-Origin: *");
    //   header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
    //   header('Access-Control-Allow-Headers: Accept,Accept-Language,Content-Language,Content-Type');

      $formdata = json_decode(file_get_contents('php://input'), true);

      if( ! empty($formdata)) {

          $name = $formdata['firstName'];
          $name = $formdata['lastName'];
          $email = $formdata['email'];
          $number = $formdata['number'];
          $company = $formdata['company'];
          $message = $formdata['message'];

          $contactData = array(
              'firstName' => $name,
              'lastName' => $email,
              'email' => $number,
              'number' => $number,
              'company' => $company,
              'message' => $message,
              'created_at' => date('Y-m-d H:i:s', time())
          );
          
          $id = $this->api_model->insert_contact($contactData);

          $this->sendemail($contactData);
          
          $response = array('id' => $id);
      }
      else {
          $response = array('id' => '');
      }
      
      $this->output
          ->set_content_type('application/json')
          ->set_output(json_encode($response));
  }

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

      $this->email->from('pawan.mukati@newtechfusion.com', 'RSGiTECH');
      $this->email->to('pawan.mukati@newtechfusion.com');

      $this->email->subject('Contact Form');
      $this->email->message($message);

      $this->email->send();
  }
}
?>
