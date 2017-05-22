      $SMS_ID='';// your way2sms no.
			$SMS_PWD='';// your way2sms password
			
      $uid=$SMS_ID; 
			$pwd=$SMS_PWD;
			$phone=$mobile; // mobile no. or nos. with comma to send messages
			$msg=$message; // sms in 140 charaters
			$curl = curl_init();
			$timeout = 30;
			$result = array();
			$uid = urlencode($uid);
			$pwd = urlencode($pwd);
			curl_setopt($curl, CURLOPT_URL, "http://way2sms.com");
			curl_setopt($curl, CURLOPT_HEADER, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			$a = curl_exec($curl);
			if(preg_match('#Location: (.*)#', $a, $r))
			$way2sms = trim($r[1]);
			curl_setopt($curl, CURLOPT_URL, $way2sms."Login1.action");
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, "username=".$uid."&password=".$pwd."&button=Login");
			curl_setopt($curl, CURLOPT_COOKIESESSION, 1);
			curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie_way2sms");
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_MAXREDIRS, 20);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5");
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($curl, CURLOPT_REFERER, $way2sms);
			$text = curl_exec($curl);
			$start=strpos($text,'Location: http://site23.way2sms.com/main.action;jsessionid=')+59;
			$token=substr($text,$start,37);
			if(curl_errno($curl))
			{	
				echo "Error sending SMS, error :".curl_error($curl);
			  die;
      }
			$pos = stripos(curl_getinfo($curl, CURLINFO_EFFECTIVE_URL), "main.action");
			if($pos === "FALSE" || $pos == 0 || $pos == "")
			{	
				echo "Invalid id/ passowrd";
        die;
			}
			if (trim($msg) == "" || strlen($msg) == 0)
			{
				echo "Blank message";
				die;
			}
			$msg = urlencode(substr($msg, 0, 140));
			$pharr = explode(",", $phone);
			$refurl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
			$newurl = "http://site23.way2sms.com/main.action?section=s&Token=$token";
			curl_setopt($curl, CURLOPT_URL, $newurl);
			$jstoken = $token;
			$text = curl_exec($curl);
			foreach ($pharr as $p)
			{
				if (strlen($p) != 10 || !is_numeric($p) || strpos($p, ".") != false)
				{
				  $result[] = array('phone' => $p, 'msg' => urldecode($msg), 'result' => "invalid number");
				  continue;
				}
				$p = urlencode($p);
				curl_setopt($curl, CURLOPT_URL, $way2sms.'smstoss.action');
				curl_setopt($curl, CURLOPT_REFERER, curl_getinfo($curl, CURLINFO_EFFECTIVE_URL));
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, "ssaction=ss&Token=".$jstoken."&mobile=".$p."&message=".$msg."&Send=Send SMS");
				$contents = curl_exec($curl);
				$pos = strpos($contents, 'Message has been submitted successfully');
				$res = ($pos !== false) ? true : false;
			}
			curl_setopt($curl, CURLOPT_URL, $way2sms."LogOut");
			curl_setopt($curl, CURLOPT_REFERER, $refurl);
			$text = curl_exec($curl);
			curl_close($curl);
			if($res)
			{
				echo "message send";
			}
			else
			{
				echo  "failed";
			}
	
