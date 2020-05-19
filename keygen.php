<?php

class Keygen
{
    private $data;
    private $installKey;

    public function __construct($name, $expire = 10000, $agents = 0) {
        $this->data = array(
                'org'        =>    $name,
                'expire'    =>    time() + ($expire * 24 * 60 * 60),
                'agents'    =>    $agents,
                'demo'        =>    false,
                'is_onapp'    =>    false,
                'copyfree'    =>    true,
                'lic_flags'    =>    'disable_callhome'
            );
    }

    public function setInstallKey($installKey) {
        $this->installKey = $installKey;
    }

    private function generateRandomString($length = 10) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    private function generateRandomNumber($length = 10) {
        $characters = '0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
        }

    public function generateKey()
    {
        if (isset($this->installKey)) {

            $license_id = $this->generateRandomString(4) . '-' . $this->generateRandomNumber(4) . '-' . $this->generateRandomString(4);
            $license_salt = $this->generateRandomString(20);

            $this->data['__1'] = '__'.$license_id;
            $this->data['__2'] = '__'.$license_id;
            $this->data['__3'] = '__'.$license_id;
            $this->data['__4'] = '__'.$license_id;

            $serialize = @serialize($this->data);

            $key = sha1(
                $license_id . $license_salt . $this->installKey . '5hIT4WRxHRDP70afPyBwph3wMeAGOVK69zIL62zcS'
            ) . '7ucrx3ghJwt7m3MNwvhXcddAskF0tLTMpIU3GMK6X';
            $key .= sha1(
                $license_id . $license_salt . $this->installKey . 'aPRfHzg1EHDXtQdXYOlRGrvKJmP7G0UPo4SmLIqt4'
            ) . 'djqhyJa40ucOWDGhQ3taSppI8D5Gpyeoc9BlcIlYv';

            $key = $key . strrev($key);

            $b64enc = base64_encode($serialize);

            $enc = $this->xorString($b64enc, $key);

            $enc = strrev($enc);

            $rawSerial = $license_id.$license_salt.$enc;

            $b64serial = base64_encode($rawSerial);

            return $b64serial;

        } else {
            return "Installation key is missing";
        }
    }

    private function xorString($string, $key)
    {
        $string_len = strlen($string);
        $key_len = strlen($key);
        $new_string = array();
        for ($i = 0, $j = 0; $i < $string_len; $i++, $j++) {
            if ($j >= $key_len) {
                $j = 0;
            }
            $new_string[] = chr(ord($string[$i]) ^ ord($key[$j]));
        }
        $new_string = implode('', $new_string);
        return $new_string;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
<meta charset="UTF-8">
<title>KeyGen</title>
</head>
<body>
<body class="bg-light">
    <div class="container">
      <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="https://www.deskpro.com/assets/images/deskpro_logo.svg" alt="">
        <h2>DeskPro KeyGen</h2>
        <p class="lead">Paste here your DeskPro Offline License request</p>
      </div>
      <div class="row">
        <div class="col-md-12 order-md-1">
          <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="needs-validation" novalidate>
            <div class="mb-3">
              <label for="text">License Request</label>
              <input type="text" class="form-control border border-primary" name="Keyfile" id="Keyfile" placeholder="Paste here your key file">
            </div>
            <div class="mb-3">
              <label for="address">Company Name</label>
              <input type="text" class="form-control border border-primary" type="text" name="Name" id="Name" placeholder="Enter your company name" required>
            </div>
            <hr class="mb-4">
            <button class="btn btn-primary btn-lg btn-block" name="submit" id="submit" type="submit">Generate License</button>
          </form>
		  <?php
			if(isset($_POST['submit'])){

			$Name = $_POST['Name'];

			$Keyfile = $_POST['Keyfile'];

			$Keyfile = base64_decode($Keyfile);

			$obj = json_decode($Keyfile);

			$install_key = $obj->{"install_key"};

			$keygen = new Keygen($Name);
			$keygen->setInstallKey($install_key);

			echo "<br>";
			echo "<textarea class='form-control border border-success' name='License' cols='40' rows='10'>";
			echo $keygen->generateKey();
			echo "</textarea>";
			echo "<br>";
			echo "<br>";
			echo "<br>";
			}
			?>
        </div>
      </div>    
    </div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>
