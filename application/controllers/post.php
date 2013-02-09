<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post extends CI_Controller {
	//	Create new thread
	public function _remap() {
		$this->load->library('DisFunctions');
		$this->disfunctions->checkBan();
		
		// Check to see if this user is muted. If they are, abort the script and give them a warning.
		$query = $this->db->query("SELECT expire,duration,reason FROM mutes WHERE ip='".$_SERVER["REMOTE_ADDR"]."' ORDER BY expire DESC LIMIT 1");
		if($query->num_rows()>0)
		{
			$muteTest = $query->row_array();
			if($muteTest["expire"]>time())
			{
				exit("You have been muted for " . $muteTest["duration"].  " " . $muteTest["reason"] . " Please wait and try again. <a href='".$this->input->post("origin")."'>Back to the previous page</a>");
			}
		}

		// Connect to CAPTCHA and verify that the user's challenge was entered correctly. If not, abort the script.
		$captcha_data = array(
			'privatekey' => CAPTCHA_PRIVATE_KEY,
			'remoteip' => $_SERVER['REMOTE_ADDR'],
			'challenge' => $this->input->post("recaptcha_challenge_field"),
			'response' => $this->input->post("recaptcha_response_field"),
		);
		$captchaResponse = $this->post_request("http://www.google.com/recaptcha/api/verify", $captcha_data);
		if($captchaResponse["status"]=="ok")
		{
			$captchaResults = explode("\n",$captchaResponse["content"]);
			if($captchaResults[0]=="false")
			{
				exit("You entered the verification incorrectly. <a href='".$this->input->post("origin")."'>Back to the previous page</a>");
			}
		} else
		{
			exit("Cannot connect to CAPTCHA service. Please try again later. <a href='".$this->input->post("origin")."'>Back to the previous page</a>");
		}
		//	Name and content of the post come from POST. If there is no name, the name is anonymous.
		if($this->input->post('name')=="")
		{
			$name = "Anonymous";
			// Since no name was given, we should generate a color to distinguish between anons.
			$generatecolor=1;
		} else
		{
			$name = $this->input->post('name');
			// Encode special characters to HTML entities (including quotation marks). This sanitizes the post without creating any visible effect on the content.
			$name = htmlentities($name, ENT_QUOTES);
			// generate a tripcode from the name. gT() handles whether or not one will actually be generated (based on the presence of !)
			$name = $this->gT($name);
			// Since a name was given, we don't have to generate a color.
			$generatecolor=0;
		}

		// For admin post. May change in the future. Unnecessary at the moment.
		/*
		$ad = strpos($name, "$!$");
		// Although unwieldy, this code is necessary because strpos() either outputs boolean FALSE or an integer.
		if($ad === FALSE)
		{
			$adminOverride = false;
		} else
		{
			$adminOverride = true;
		}
		*/

		$board = $this->input->post('board');
		// Self-explanatory
		if($this->input->post("content")=="")
		{
			exit("You did not write any content.");
		}

		// Because we parse the content for URLs and greentext, we have to sanitize before we do that.
		$content = htmlentities($this->input->post('content'), ENT_QUOTES);
		// Check for and create greentext.
		$content = $this->greenText($content);
		// Check the text for URLs and replace them with links.
		$content = $this->findURLs($content);
		// Lastly, turn each line-break character into a new paragraph. This allows breaks to appear properly.
		$content = "<p>" . str_replace("\n","</p><p>",$content) . "</p>";

		// Now we check for r9k match. Since the content is already parsed and de-injected we can just put $content directly into the query.
		// Since we want to compare it to existing entries in the DB this is what we want to do anyway.
		// At the moment, the r9k match only checks for an EXACT match in the string - case and everything.
		// In the future it should work the way Munroe has specified it here:
		// Check to see if r9k is enabled for this board
		$query = $this->db->query("SELECT r9k FROM boardmeta WHERE name='".$board."'");
		$r9kcheck = $query->row_array();
		$r9k = $r9kcheck["r9k"];
		$query = $this->db->query("SELECT * FROM posts WHERE content='".$content."' AND board='".$board."'");
		if($query->num_rows()>0 && $r9k)
		{
			exit("This content is not original. Write something unique! <a href='".$this->input->post("origin")."'>Back to the previous page</a>");
		}
		$date = time();
		//	As one would expect, the ID of the post is one greater than the ID of the post before it.
		//	So we just fetch the highest ID and increment it.
		$query = $this->db->query("SELECT id FROM posts ORDER BY id DESC");
		if($query->num_rows()>0)
		{
			$results = $query->row();
			$id = $results->id;
			$id++;
		} else
		{
			$id=0;
		}

		//	The thread value in the DB stands for the thread that the post is within.
		//	This is determined by a hidden field on the page. If the new post is a thread, the thread value will be -1.
		$thread = $this->input->post('thread');
		//	REMOTE_ADDR is modified by Cloudflare.php in application/hooks.
		$ip = $_SERVER["REMOTE_ADDR"];
		//	The board is sent in a hidden field via POST. Everything else here is just a security measure to prevent injection.
		$query = $this->db->query("SELECT * FROM boardmeta WHERE name=".$this->db->escape($board));
		if($query->num_rows()===1)
		{
			$board = $this->input->post('board');
		} else
		{
			exit("Error!");
		}
		//	Find all posts that have the same IP as this one, to see if this poster has posted already
		if($generatecolor)
		{
			if($thread != -1)
			{
				$query = $this->db->query("SELECT color FROM posts WHERE ip='".$ip."' AND(thread='".$thread."' OR id='".$thread."') AND color!='none'");
			}
			if($query->num_rows()==0 || $thread==-1)
			{
				$color = $this->randomColor();
			} else
			{
				//	There is probably a better way to write the color from the IP to the variable.
				foreach($query->result() as $row)
				{
					$color = $row->color;
				}
			}
		} else
		{
			$color = "none";
		}
		//	The parent (the post that the new post that we're creating is in reply to) is sent by a hidden field to this file.
		//	If the post is creating a new thread, there will be no parent, so we use -1.
		//	If the post is in reply to the OP, the OP will be the parent.
		//	If the post is in reply to another post, that post will be the parent.
		//	Currently there is no code in the thread-loading to make use of this, but there will eventually be hierarichal, nested replies.
		if($thread == -1)
		{
			$parent = -1;
		} else if($this->input->post('parent')=="")
		{
			$parent = $thread;
		} else
		{
			$parent = $this->input->post('parent');
		}
		//	The "latest" field is used for threads and holds the date of the latest reply posted. This is used for bumping posts to the front page.
		//	A reply post will not need to have a "latest" field. Only a thread post (top-level, OP, $thread is -1) will need to have a latest field.
		//	When a reply post is created, the "latest" field on its thread is updated.
		//	When a thread is created, the "latest" field on that thread post is the date of its creation.
		if($thread==-1)
		{
			$latest = time();
		} else if($this->input->post('sage')!="y")
		{
			$query = $this->db->query("SELECT * FROM posts WHERE id=".$thread);
			if($query->num_rows>0)
			{
				$results = $query->row_array();
				// If neither the "latest" is sticky, nor the time is within the archive limit, then everything is okay and we can bump the thread.
				//if(!($results["latest"]==9999999999 || $this->disfunctions->checkArchive($thread)))
				if(!$this->disfunctions->checkArchive($thread))
				{
					$this->db->query("UPDATE posts SET latest=".time()." WHERE id=".$thread);
				}
				$latest="";
			}
		} else if($this->input->post('sage')=="y")
		{
			$query = $this->db->query("SELECT latest FROM posts WHERE id=".$thread);
			$latestar = $query->row_array();
			$latest = $latestar['latest'];
		}
		// Mute this person for 15 seconds for flood prevention 
		// $exptime = time()+15;
		//$this->db->query("INSERT INTO mutes VALUES('".$_SERVER["REMOTE_ADDR"]."','".$exptime."','15 seconds','as a flood prevention measure.')");

		// Image handling
		if($_FILES["upload"]["name"] == null)
		{
			$imgEntry = 0;
		} else
		{
			if($_FILES["upload"]["type"] == "image/jpeg")
			{
				$filetype = "jpg";
			} else if($_FILES["upload"]["type"] == "image/png")
			{
				$filetype = "png";
			} else if($_FILES["upload"]["type"] == "image/gif")
			{
				$filetype = "gif";
			} else
			{
				exit("This is not a valid file type.");
			}

			$imgCount = 0;
			$handle = opendir("images");

			while(false !== ($entry = readdir($handle)))
			{
				$imgCount++;
			}
			// Removes two because of ".", "..", and "thumbs" so that the count starts from 1.
			$imgCount-=2;

			move_uploaded_file($_FILES["upload"]["tmp_name"], "images/$imgCount".".".$filetype);
			
			$config["image_library"] = "gd2";
			$config["source_image"] = "images/$imgCount" . "." . $filetype;
			$config["create_thumb"] = FALSE;
			$config["new_image"] = "images/thumbs/$imgCount" . "." . $filetype;
			$config['width'] = 200;
			$config['height'] = 200;

			$this->load->library("image_lib", $config);

			$this->image_lib->resize();

			$imgEntry = $imgCount . "." . $filetype;
		}

		// Put everything into the DB!
		$query = $this->db->query("INSERT INTO posts VALUES('$name', '$content', '$date', '$id', '$thread', '$ip', '$board', '$color', '$parent', '$latest', '$imgEntry')");
		
		if($query)
		{
			if($thread==-1)
			{
				$redirdest = base_url().'board/'.$board.'/thread/'.$id;
			} else
			{
				$redirdest = $this->input->post('origin');
			}
?>
<html>
<head>
<script type="text/javascript">
<!--
function redir(){
    window.location = "<?php echo $redirdest;?>"
}
//-->
</script>
</head>
<body onLoad="setTimeout('redir()',2000)">
<b>Post successful!</b>
<small>Redirecting in 2 seconds.</small>
</body>
<?php

		} else
		{
			echo "The post was not successful.";
		}
	}	
	//	Generate a tripcode. I know little about how this works.
	//	From http://www.rune-server.org/programming/website-development/233780-tripcode-generator.html
	private function gT($name) {
		$test = strpos($name, "#");
		if($test === FALSE){
			$nameo;
			$trip = $name;
		}
		else {
			$k = explode('#', $name);
			$nameo = $k[0];
			$trip = $k[1];
		}
		if((function_exists('mb_convert_encoding'))){
			mb_substitute_character('none');
			$recoded_cap = mb_convert_encoding($trip, 'Shift_JIS', 'UTF-8');
		}
		$trip = (($recoded_cap != '') ? $recoded_cap : $trip);
		$salt = substr($trip.'H.', 1, 2);
		$salt = preg_replace('/[^\.-z]/', '.', $salt);
		$salt = strtr($salt, ':;<=>?@[\]^_`', 'ABCDEFGabcdef');
		$output = substr(crypt($trip, $salt), -10);
		if($test===FALSE)
		{
			return $name;
		} else
		{
			return $nameo.'!'.$output;
		}
	}
	private function randomColor()
	{
		$c="";
	    for ($i = 0; $i<3; $i++) 
	    { 
	        $c .=  dechex(rand(0,15)); 
	    }
	    return "$c"; 
	}
	// Find all ">greentext" and insert the necessary HTML to make the text green.
	// Run this AFTER the conversion to HTML entities.
	private function greenText($input)
	{
		$contentArray = explode("\n",$input);
		$arLength = count($contentArray);
		for($i=0;$i<$arLength;$i++)
		{
			$line = str_split($contentArray[$i]);
			$lineLength = count($line);
			$greentext = false;
			for($j=0;$j<$lineLength;$j++)
			{
				// The characters &gt; in that order are the HTML entity for the greater than > symbol. Kludgy but it works.
				if(($line[$j]=="&") && ($line[$j+1]=="g") && ($line[$j+2]=="t") && ($line[$j+3]==";"))
				{
					$greentext = true;
				}
			}
			if($greentext)
			{
				$contentArray[$i] = "<span class=".'"'.'greentext'.'"'.">" . $contentArray[$i] . "</span>";
			}
		}
		$output = implode("\n",$contentArray);
		return $output;
	}
	//	Slightly modified version of http://css-tricks.com/snippets/php/find-urls-in-text-make-links/
	private function findURLs($text)
	{
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

				// Check if there is a url in the text
		if(preg_match($reg_exUrl, $text, $url)) {

		       		// make the urls hyper links
		       $text = preg_replace($reg_exUrl, "<a href=".'"'.$url[0].'"'.">$url[0]</a> ", $text);
		       return $text;
		} else {

		       		// if no urls in the text just return the text
		       return $text;
		}
	}
	// http://www.jonasjohn.de/snippets/php/post-request.htm
	private function post_request($url, $data, $referer='') {
	 
	    // Convert the data array into URL Parameters like a=b&foo=bar etc.
	    $data = http_build_query($data);
	 
	    // parse the given URL
	    $url = parse_url($url);
	 
	    if ($url['scheme'] != 'http') { 
	        die();
	    }
	 
	    // extract host and path:
	    $host = $url['host'];
	    $path = $url['path'];
	 
	    // open a socket connection on port 80 - timeout: 30 sec
	    $fp = fsockopen($host, 80, $errno, $errstr, 30);
	 
	    if ($fp){
	 
	        // send the request headers:
	        fputs($fp, "POST $path HTTP/1.1\r\n");
	        fputs($fp, "Host: $host\r\n");
	 
	        if ($referer != '')
	            fputs($fp, "Referer: $referer\r\n");
	 
	        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	        fputs($fp, "Content-length: ". strlen($data) ."\r\n");
	        fputs($fp, "Connection: close\r\n\r\n");
	        fputs($fp, $data);
	 
	        $result = ''; 
	        while(!feof($fp)) {
	            // receive the results of the request
	            $result .= fgets($fp, 128);
	        }
	    }
	    else { 
	        return array(
	            'status' => 'err', 
	            'error' => "$errstr ($errno)"
	        );
	    }
	 
	    // close the socket connection:
	    fclose($fp);
	 
	    // split the result header from the content
	    $result = explode("\r\n\r\n", $result, 2);
	 
	    $header = isset($result[0]) ? $result[0] : '';
	    $content = isset($result[1]) ? $result[1] : '';
	 
	    // return as structured array:
	    return array(
	        'status' => 'ok',
	        'header' => $header,
	        'content' => $content
	    );
	}
}