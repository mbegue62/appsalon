<?php
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)

    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        // Crear el objeto de email
        $email = new PHPMailer();
        
        // Configurar SMTP
        $email->isSMTP();
        $email->Host = 'smtp-relay.sendinblue.com';
        $email->SMTPAuth = true;
        $email->SMTPSecure = 'tls';
        $email->Port = 587;
        $email->Username = 'mbegue62@yahoo.com.ar';
        $email->Password = '8UkXZ9gNDGsbSp1m';
        
        // Contenido del email
        $email->setFrom('cuentas@appsalom.com');
        $email->addAddress($this->email);
        $email->Subject = 'Confirma tu Cuenta';

        // Set HTML

        $email->isHTML(TRUE);
        $email->CharSet = 'UTF-8';


        $contenido = "<html>";
        $contenido.= "<p><strong> Hola " . $this->email . "</strong> Has creado tu cuenta en App Salon, solo debes confirmarla presionando en el siguiente enlace </p>";
        $contenido.= "<p>Presiona aquí: <a href='http://apppsalon.alwaysdata.net/confirmar-cuenta?token=" . $this->token . "'>Confirmar cuenta </a></p>";
        $contenido.= "<p>Si tú no solicitaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido.= "</html>";

        $email->Body = $contenido;

        // Enviar mail

        $email->send();
    }

public function enviarInstrucciones() {
    $email = new PHPMailer();
    $email->isSMTP();
    $email->Host = 'sandbox.smtp.mailtrap.io';
    $email->SMTPAuth = true;
    $email->Port = 2525;
    $email->Username = 'f5f22d8e2b5672';
    $email->Password = '7ad75518c2bbe2';
    $email->setFrom('cuentas@appsalom.com');
    $email->addAddress('cuentas@appsalom.com', 'AppSalom.com');
    $email->Subject = 'Reestablece tu Password';
    // Set HTML
    $email->isHTML(TRUE);
    $email->CharSet = 'UTF-8';
    $contenido = "<html>";
    $contenido.= "<p><strong> Hola " . $this->nombre . "</strong> Has solicitado reestablecer tu Password, sigue el siguiente enla para hacerlo </p>";
    $contenido.= "<p>Presiona aquí: <a href='http://localhost:3000/recuperar?token=" . $this->token . 
  "'>Reestablecer Password </a></p>";
    $contenido.= "<p>Si tú no solicitaste esta cuenta, puedes ignorar este mensaje</p>";
    $contenido.= "</html>";
    $email->Body = $contenido;
    // Enviar mail
    $email->send();
}
}