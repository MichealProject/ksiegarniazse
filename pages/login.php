<?php

if (LOGIN_ACCESS == true) {
	if (isset($_POST['email'])) {
		$mail = htmlspecialchars($_POST['email']);
		$password = $_POST['password'];
		$error = 0;
		$error_password = false;

		$sql = mysqli_fetch_array(mysqli_query($mysql, "SELECT * FROM `admin` WHERE `mail`='" . $mail . "' LIMIT 1"));
		if (!$sql) {
			$error = "Użytkownik o podanym adresie mailowym nie istnieje.";
		} else {
			if (empty($mail) || empty($password)) {
				$error = "Wypełnij wszystkie pola.";
			}

			$valid_password = false;
			if ($error == 0) {
				if (!empty($sql['password'])) {
					if (password_verify($password, $sql['password'])) {
						$valid_password = true;
						if (password_needs_rehash($sql['password'], PASSWORD_DEFAULT)) {
							$new_hash = password_hash($password, PASSWORD_DEFAULT);
							mysqli_query($mysql, "UPDATE `admin` SET `password` = '" . $new_hash . "' WHERE `id` = '" . $sql['id'] . "'");
						}
					} elseif (hash_equals(md5($password), $sql['password'])) {
						$valid_password = true;
						$new_hash = password_hash($password, PASSWORD_DEFAULT);
						mysqli_query($mysql, "UPDATE `admin` SET `password` = '" . $new_hash . "' WHERE `id` = '" . $sql['id'] . "'");
					}
				}
			}

			if (!$valid_password && $error == 0) {
				$error = "Błędne hasło.";
				$error_password = true;
			}
			if ($sql['banned'] == true) {
				$error = "Konto użytkownika zostało dezaktywowane. Skontaktuj się z administratorem systemu.";
			}

			if ($error == 0) {
				do {
					$login_code = bin2hex(random_bytes(32));
					$sql2 = mysqli_fetch_array(mysqli_query($mysql, "SELECT * FROM `admin` WHERE `login_code` = '" . $login_code . "' LIMIT 1"));
				} while ($login_code == $sql2['login_code']);

				mysqli_query($mysql, "UPDATE `admin` SET `login_code` = '" . $login_code . "' WHERE `id` = '" . $sql['id'] . "'");
				$_SESSION['login_code'] = $login_code;
			}
		}
	}
} else {
	$error = "Błąd! Logowanie jest zablokowane.";
}
ob_start();
include 'front/Flogin.php';
$content = ob_get_clean();
$display->toDisplay($content);
