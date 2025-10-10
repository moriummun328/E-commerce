<?php
if(session_status() === PHP_SESSION_NONE)
{
	session_start();
}

require_once __DIR__ . '/dbconfig.php';

class USER
{
	private $conn;

	private $baseUrl;

	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;

		

		$this->baseUrl = defined('BASE_URL') ? BASE_URL : $this->guessBaseUrl();
	}

	function guessBaseUrl()
	{
		$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
		$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

		return $scheme . '://' .$host .'/ecommerce';

	}

	/* Helper Functions */

	public  function redirect($url)
	{
		header("Location: .$url");
		exit;
	}

	public function is_logged_in()
	{
		return !empty($_SESSION['user_id']);		
	}

	public function logout()
	{
		session_unset();
		session_destroy();
		return true;
	}

	public function getUserById($id)
	{
		$st = $this->conn->prepare("SELECT id, username, email, verifed FROM users WHERE id = ? LIMIT 1");
		$st->execute([$id]);
		return $st->fetch(PDO::FETCH_ASSOC);
	}

	/* Auth Core */

	public function register($username, $email, $password)
	{
		$st = $this->conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
		$st->execute([$email]);
		if($st->fetch())
		{
			throw new Exception("This email already registered!");
		}

		$token = bin2hex(random_bytes(16));
		$hash = password_hash($password, PASSWORD_DEFAULT);

		$in = $this->conn->prepare("INSERT INTO users (username, email, password, token, verifed) VALUES (?,?,?,?,0)");

		$in->execute([$username, $email, $hash, $token]);

		//send verification mail
		$verifyLink = $this->baseUrl . "/auth/verify.php?token=".urlencode($token)."&email=" . urlencode($email);

		$message = '
						<div style="font-family: Arial; font-size: 14px; line-height: 1.6; color: #333;">
						<h2 style="margin: 0 0 12px;">Verify Your Email</h2>
						<p>Hi '.htmlspecialchars($u['username']).',</p>
						<p>Please click the button bellow to verify your account:</p>

						<p style="margin: 16px 0;">
							<a href="'.htmlspecialchars($verifyLink).'" target="_blank" style="background: #007bff; color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 6px; display: inline-block;">Verify My Account
							</a>
						</p>

						<p>If the button does not work, copy the following link and paste into your browser:</p>
						<p>
							<a href="'.htmlspecialchars($verifyLink).'" target="_blank">'.htmlspecialchars($verifyLink).'</a>
						</p>

						<hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
						<p style="font-size: 12px; color: #777;">If you did not create this account, you can ignore this email.</p>
					</div>';

		$this->sendMail($email, $message, "Verify Your Email");

		return true;
	}

	public function login($email, $password)
	{
		$st = $this->conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");

		$st->execute([$email]);

		$u = $st->fetch(PDO::FETCH_ASSOC);

		if(!$u)
		{
			throw new Exception("Invalid Credentials");
		}

		if(!password_verify($password, $u['password']))
		{
			throw new Exception("Invalid Credentials");
		}

		if((int)$u['verifed'] !== 1)
		{
			throw new Exception("Your Email is Not Verified. Please verify your email");
		}

		$_SESSION['user_id'] = $u['id'];
		$_SESSION['user_email'] = $u['email'];
		$_SESSION['user_name'] = $u['username'];
		$_SESSION['user_phone'] =$u['phone'];

		header("Location: " .$this->baseUrl."/");
		exit;
	
	}

	public function verify($email, $token)
	{
		$st = $this->conn->prepare("SELECT id, token, verifed FROM users WHERE email = ? LIMIT 1");
		$st->execute([$email]);
		$u = $st->fetch(PDO::FETCH_ASSOC);

		if(!$u)
		{
			throw new Exception("Account Not Found!");
		}

		if((int)$u['verifed'] === 1)
		{
			return true; //already verified
		}

		if(!hash_equals($u['token'] ?? '', $token ?? ''))
		{
			throw new Exception("Invalid verification token");
		}

		$up = $this->conn->prepare("UPDATE users SET verifed = 1, token = NULL WHERE id = ?");
		$up->execute([$u['id']]);

		return true;
	}

	public  function requestPasswordReset($email)
	{
		$st = $this->conn->prepare("SELECT id, username FROM users WHERE email = ? LIMIT 1");
		$st->execute([$email]);
		$u = $st->fetch(PDO::FETCH_ASSOC);

		if(!$u) return true;

		$token = bin2hex(random_bytes(16));
		$expire = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

		$up = $this->conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");

		$up->execute([$token, $expire, $u['id']]);

		$resetLink = $this->baseUrl ."/auth/resetpass.php?token=".urlencode($token)."&email=".urlencode($email);

		$message = '

					<div style="font-family: Arial; font-size: 14px; line-height: 1.6; color: #333;">
						<h2 style="margin: 0 0 12px;">Password Reset</h2>
						<p>Hi '.htmlspecialchars($u['username']).',</p>
						<p>You requested to reset your password. Click the following button to set a new password.</p>

						<p style="margin: 16px 0;">
							<a href="'.htmlspecialchars($resetLink).'" target="_blank" style="background: #007bff; color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 6px; display: inline-block;">Reset My Password
							</a>
						</p>

						<p>If the button does not work, copy the following link and paste into your browser:</p>
						<p>
							<a href="'.htmlspecialchars($resetLink).'" target="_blank">'.htmlspecialchars($resetLink).'</a>
						</p>

						<hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
						<p style="font-size: 12px; color: #777;">If you did not request a password reset, you can ignore this email.</p>
					</div>
				';

				$this->sendMail($email,$message, "Reset Your Password");

				return true;
	}

	public function resetPassword($email, $token, $newPassword)
	{
		$st = $this->conn->prepare("SELECT id, reset_token, reset_expires FROM users WHERE email = ? LIMIT 1");
		$st->execute([$email]);
		$u = $st->fetch(PDO::FETCH_ASSOC);

		if(!$u) throw new Exception("Account Not Found.");

		if(empty($u['reset_token']) || !hash_equals($u['reset_token'], $token ?? ''))
		{
			throw new Exception('Invalid or Expired Token!');
		}

		if(!empty($u['reset_expires']))
		{
			$now = new DateTime();
			$exp = new DateTime($u['reset_expires']);

			if($now > $exp)
			{
				throw new Exception('Reset token expires. Try again');
			}
		}

		$hash = password_hash($newPassword, PASSWORD_DEFAULT);
		$up = $this->conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ? ");
		$up->execute([$hash, $u['id']]);
		return true;
	}

	public function sendMail($email, $message, $subject){
        require_once __DIR__.'/mailer/PHPMailer.php';
        require_once __DIR__.'/mailer/SMTP.php';
        require_once __DIR__.'/mailer/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'rayhan259606@gmail.com'; 
        $mail->Password = 'asuiknjtqmukdtcr'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('rayhan259606@gmail.com','E_commerce');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);

        if(!$mail->send()) throw new Exception("Mail send failed: " . $mail->ErrorInfo);
        return true;
    }
}
?>